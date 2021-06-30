<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\FrontController;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Domain\Model\DummyTermsGuardValidation;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TermsController extends AbstractController
{
    public function editAdmin(): Response
    {
        if (!$this->isGranted('MODULE_TERMS_ADMIN')) {
            throw new AccessDeniedException();
        }

        return $this->render('@RichIdTermsModule/admin/edit/main.html.twig');
    }

    public function listAdmin(TermsRepository $termsRepository): Response
    {
        if (!$this->isGranted('MODULE_TERMS_ADMIN')) {
            throw new AccessDeniedException();
        }

        return $this->render(
            '@RichIdTermsModule/admin/list/main.html.twig',
            [
                'termsList' => $termsRepository->findAllOrderedByName(),
            ]
        );
    }

    public function sign(string $termsSlug, TermsRepository $termsRepository, Request $request): Response
    {
        $subject = $this->buildSubject($request);
        $termsGuardValidation = DummyTermsGuardValidation::create($termsSlug, $subject->getTermsSubjectType(), $subject->getTermsSubjectIdentifier());

        if (!$this->isGranted('MODULE_TERMS_GUARD_VALID', $termsGuardValidation)) {
            throw new AccessDeniedException();
        }

        $terms = $termsRepository->findOneBySlug($termsSlug);

        if (!$terms instanceof Terms) {
            throw new NotFoundHttpException(
                \sprintf('No terms found with slug %s', $termsSlug)
            );
        }

        $lastVersion = $terms->getLatestVersion();

        if ($lastVersion === null) {
            throw new NotFoundHttpException(
                \sprintf('Terms %s hasn\'t published version', $termsSlug)
            );
        }

        return $this->render(
            '@RichIdTermsModule/sign/main.html.twig',
            [
                'terms'            => $terms,
                'lastTermsVersion' => $lastVersion,
            ]
        );
    }

    private function buildSubject(Request $request): TermsSubjectInterface
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
}
