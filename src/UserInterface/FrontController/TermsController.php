<?php declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\FrontController;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
                'terms' => $termsRepository->findAll(),
            ]
        );
    }

    public function sign(string $termsSlug, TermsRepository $termsRepository): Response
    {
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
}
