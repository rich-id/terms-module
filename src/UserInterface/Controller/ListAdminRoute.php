<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Infrastructure\Repository\TermsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ListAdminRoute extends AbstractController
{
    /** @var TermsRepository */
    protected $termsRepository;

    public function __construct(TermsRepository $termsRepository)
    {
        $this->termsRepository = $termsRepository;
    }

    #[IsGranted('MODULE_TERMS_ADMIN')]
    public function __invoke(): Response
    {
        return $this->render(
            '@RichIdTermsModule/admin/list/main.html.twig',
            [
                'termsList' => $this->termsRepository->findAllOrderedByName(),
            ]
        );
    }
}
