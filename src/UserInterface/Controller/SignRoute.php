<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Exception\AlreadySignLastTermsVersionException;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Exception\NotPublishedTermsException;
use RichId\TermsModuleBundle\Domain\Exception\TermsHasNoPublishedVersionException;
use RichId\TermsModuleBundle\Domain\Fetcher\GetTermsVersionToSign;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Domain\Model\DummyTermsGuardValidation;
use RichId\TermsModuleBundle\Domain\UseCase\SignTerms;
use RichId\TermsModuleBundle\Infrastructure\SecurityVoter\TermsGuardVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SignRoute extends AbstractController
{
    /** @var GetTermsVersionToSign */
    protected $getTermsVersionToSign;

    /** @var SignTerms */
    protected $signTerms;

    /** @var RequestStack */
    protected $requestStack;

    public function __construct(
        GetTermsVersionToSign $getTermsVersionToSign,
        SignTerms $signTerms,
        RequestStack $requestStack
    ) {
        $this->getTermsVersionToSign = $getTermsVersionToSign;
        $this->signTerms = $signTerms;
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
            $lastVersion = ($this->getTermsVersionToSign)($termsSlug, $subject);
            $terms = $lastVersion->getTerms();

            if ($request->getMethod() === Request::METHOD_POST) {
                $accepted = $this->getIsAcceptedFromRequest($request);

                return ($this->signTerms)($termsSlug, $subject, $accepted);
            }

            return $this->render(
                '@RichIdTermsModule/sign/main.html.twig',
                [
                    'terms'            => $terms,
                    'lastTermsVersion' => $lastVersion,
                    'subject'          => $subject,
                ]
            );
        } catch (NotFoundTermsException | NotPublishedTermsException | TermsHasNoPublishedVersionException $e) {
            throw new NotFoundHttpException($e->getMessage());
        } catch (AlreadySignLastTermsVersionException $e) {
            throw new AccessDeniedException('You have already signed this terms.');
        }
    }

    protected function getSubject(Request $request): TermsSubjectInterface
    {
        $subjectType = $request->query->get('type');
        $subjectIdentifier = $request->query->get('identifier');

        if (!is_string($subjectType) || $subjectType === '') {
            throw new BadRequestHttpException('Query parameter type is missing.');
        }

        if (!is_string($subjectIdentifier) || $subjectIdentifier === '') {
            throw new BadRequestHttpException('Query parameter identifier is missing.');
        }

        return DummySubject::create($subjectType, $subjectIdentifier);
    }

    protected function getIsAcceptedFromRequest(Request $request): ?bool
    {
        $accepted = $request->request->get('accepted');

        return $accepted !== '' ? (bool) $accepted : null;
    }
}
