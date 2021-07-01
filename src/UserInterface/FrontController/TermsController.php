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
}
