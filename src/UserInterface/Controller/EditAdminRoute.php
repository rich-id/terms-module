<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;

class EditAdminRoute extends AbstractController
{
    use AdminRouteTrait;

    /** @var array<string> */
    protected $adminRoles;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->adminRoles = $parameterBag->get('rich_id_terms_module.admin_roles');
    }

    /** @return array<string> */
    protected function getAdminRoles(): array
    {
        return $this->adminRoles;
    }

    public function __invoke(): Response
    {
        if (!$this->isGranted('MODULE_TERMS_ADMIN')) {
            throw $this->buildAccessDeniedException();
        }

        return $this->render('@RichIdTermsModule/admin/edit/main.html.twig');
    }
}
