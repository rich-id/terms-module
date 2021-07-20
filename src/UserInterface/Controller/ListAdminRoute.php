<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Infrastructure\Repository\TermsRepository;
use RichId\TermsModuleBundle\Infrastructure\SecurityVoter\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;

class ListAdminRoute extends AbstractController
{
    use AdminRouteTrait;

    /** @var TermsRepository */
    protected $termsRepository;

    /** @var ParameterBagInterface */
    protected $parameterBag;

    public function __construct(TermsRepository $termsRepository, ParameterBagInterface $parameterBag)
    {
        $this->termsRepository = $termsRepository;
        $this->parameterBag = $parameterBag;
    }

    public function __invoke(): Response
    {
        if (!$this->isGranted(UserVoter::MODULE_TERMS_ADMIN)) {
            throw $this->buildAccessDeniedException();
        }

        return $this->render(
            '@RichIdTermsModule/admin/list/main.html.twig',
            [
                'termsList' => $this->termsRepository->findAllOrderedByName(),
            ]
        );
    }

    /** @return string[] */
    protected function getAdminRoles(): array
    {
        return $this->parameterBag->get('rich_id_terms_module.admin_roles');
    }
}
