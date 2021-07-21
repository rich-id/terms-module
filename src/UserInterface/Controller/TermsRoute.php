<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Exception\NotPublishedTermsException;
use RichId\TermsModuleBundle\Domain\Exception\TermsHasNoPublishedVersionException;
use RichId\TermsModuleBundle\Domain\Fetcher\GetLastPublishedTermsVersion;
use RichId\TermsModuleBundle\Domain\Model\DummyTermsGuardValidation;
use RichId\TermsModuleBundle\Domain\UseCase\HasSignedTerms;
use RichId\TermsModuleBundle\Infrastructure\SecurityVoter\TermsGuardVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TermsRoute extends AbstractController
{
    use RouteHelpersTrait;

    /** @var GetLastPublishedTermsVersion */
    protected $getLastPublishedTermsVersion;

    /** @var HasSignedTerms */
    protected $hasSignedTerms;

    /** @var RequestStack */
    protected $requestStack;

    public function __construct(
        GetLastPublishedTermsVersion $getLastPublishedTermsVersion,
        HasSignedTerms $hasSignedTerms,
        RequestStack $requestStack
    ) {
        $this->getLastPublishedTermsVersion = $getLastPublishedTermsVersion;
        $this->hasSignedTerms = $hasSignedTerms;
        $this->requestStack = $requestStack;
    }

    public function __invoke(string $termsSlug): Response
    {
        $request = $this->requestStack->getCurrentRequest() ?? new Request();
        $subject = $this->getSubject($request);
        $termsGuardValidation = DummyTermsGuardValidation::create($termsSlug, $subject->getTermsSubjectType(), $subject->getTermsSubjectIdentifier());

        if (!$this->isGranted(TermsGuardVoter::MODULE_TERMS_GUARD_VALID, $termsGuardValidation)) {
            throw new AccessDeniedException();
        }

        try {
            $lastVersion = ($this->getLastPublishedTermsVersion)($termsSlug);
            $terms = $lastVersion->getTerms();

            if (($this->hasSignedTerms)($termsSlug, $subject) !== HasSignedTerms::HAS_SIGNED_LATEST_VERSION) {
                throw new AccessDeniedException('You must sign the terms to access it.');
            }

            return $this->render(
                '@RichIdTermsModule/terms/main.html.twig',
                [
                    'terms'            => $terms,
                    'lastTermsVersion' => $lastVersion,
                ]
            );
        } catch (NotFoundTermsException | NotPublishedTermsException | TermsHasNoPublishedVersionException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
    }
}
