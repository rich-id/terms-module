<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Model\TermsVersionEdition;
use RichId\TermsModuleBundle\Infrastructure\FormType\TermsVersionFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class EditAdminRoute extends AbstractController
{
    use AdminRouteTrait;

    /** @var array<string> */
    protected $adminRoles;

    /** @var RequestStack */
    protected $requestStack;

    public function __construct(ParameterBagInterface $parameterBag, RequestStack $requestStack)
    {
        $this->adminRoles = $parameterBag->get('rich_id_terms_module.admin_roles');
        $this->requestStack = $requestStack;
    }

    /** @return array<string> */
    protected function getAdminRoles(): array
    {
        return $this->adminRoles;
    }

    public function __invoke(Terms $terms): Response
    {
        if (!$this->isGranted('MODULE_TERMS_ADMIN')) {
            throw $this->buildAccessDeniedException();
        }

        $request = $this->requestStack->getCurrentRequest();

        $termsVersion = $terms->getLatestVersion() ?? TermsVersion::buildDefaultVersion($terms);

        $model = new TermsVersionEdition($termsVersion);

        $form = $this->createForm(
            TermsVersionFormType::class,
            $model,
            [TermsVersionFormType::TERMS_ENTITY => $terms]
        )->handleRequest($request);

        return $this->render(
            '@RichIdTermsModule/admin/edit/main.html.twig',
            [
                'terms' => $terms,
                'form'  => $form->createView(),
            ]
        );
    }
}
