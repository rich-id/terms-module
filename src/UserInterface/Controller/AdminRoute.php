<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminRoute extends AbstractController
{
    #[IsGranted('MODULE_TERMS_ADMIN')]
    public function __invoke(): Response
    {
        return $this->render('@RichIdTermsModule/admin/main.html.twig', );
    }
}
