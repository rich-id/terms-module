<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\FrontController;

use RichId\TermsModuleBundle\Infrastructure\Repository\TermsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
