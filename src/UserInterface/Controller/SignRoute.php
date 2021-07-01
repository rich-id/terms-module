<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Exception\AlreadySignLastTermsVersionException;
use RichId\TermsModuleBundle\Domain\Exception\NotFoundTermsException;
use RichId\TermsModuleBundle\Domain\Exception\TermsHasNoPublishedVersionException;
use RichId\TermsModuleBundle\Domain\Fetcher\GetTermsVersionToSign;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Domain\Model\DummyTermsGuardValidation;
use RichId\TermsModuleBundle\Domain\UseCase\SignTerms;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    public function __construct(GetTermsVersionToSign $getTermsVersionToSign, SignTerms $signTerms)
    {
        $this->getTermsVersionToSign = $getTermsVersionToSign;
        $this->signTerms = $signTerms;
    }

    public function __invoke(string $termsSlug, Request $request): Response
    {
        $subject = $this->getSubject($request);
        $termsGuardValidation = DummyTermsGuardValidation::create($termsSlug, $subject->getTermsSubjectType(), $subject->getTermsSubjectIdentifier());

        if (!$this->isGranted('MODULE_TERMS_GUARD_VALID', $termsGuardValidation)) {
            throw new AccessDeniedException();
        }

        try {
            $lastVersion = ($this->getTermsVersionToSign)($termsSlug, $subject);
            $terms = $lastVersion->getTerms();

            $accepted = $this->getIsAcceptedFromRequest($request);

            if ($request->getMethod() === Request::METHOD_POST) {
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
        } catch (NotFoundTermsException $e) {
            throw new NotFoundHttpException(
                \sprintf('No terms found with slug %s', $termsSlug)
            );
        } catch (TermsHasNoPublishedVersionException $e) {
            throw new NotFoundHttpException(
                \sprintf('Terms %s hasn\'t published version', $termsSlug)
            );
        } catch (AlreadySignLastTermsVersionException $e) {
            throw new AccessDeniedException('You have already signed this terms');
        }
    }

    protected function getSubject(Request $request): TermsSubjectInterface
    {
        $subjectType = $request->query->get('type', null);
        $subjectIdentifier = $request->query->get('identifier', null);

        if ($subjectType === null || $subjectType === '') {
            throw new BadRequestHttpException('Query parameter type is missing.');
        }

        if ($subjectIdentifier === null || $subjectIdentifier === '') {
            throw new BadRequestHttpException('Query parameter identifier is missing.');
        }

        return DummySubject::create($subjectType, $subjectIdentifier);
    }

    protected function getIsAcceptedFromRequest(Request $request): ?bool
    {
        $accepted = $request->request->get('accepted');

        if ($accepted === '') {
            return null;
        }

        return (bool) $accepted;
    }
}
