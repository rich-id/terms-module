<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use Doctrine\ORM\EntityManagerInterface;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use RichId\TermsModuleBundle\Domain\UseCase\EditTerms;
use RichId\TermsModuleBundle\Infrastructure\FormType\TermsVersionFormType;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditAdminRoute extends AbstractController
{
    use AdminRouteTrait;

    /** @var EditTerms */
    protected $editTerms;

    /** @var TermsVersionRepository */
    protected $termsVersionRepository;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var array<string> */
    protected $adminRoles;

    /** @var RequestStack */
    protected $requestStack;

    public function __construct(
        EditTerms $editTerms,
        TermsVersionRepository $termsVersionRepository,
        EntityManagerInterface $entityManager,
        ParameterBagInterface $parameterBag,
        RequestStack $requestStack
    ) {
        $this->editTerms = $editTerms;
        $this->termsVersionRepository = $termsVersionRepository;
        $this->entityManager = $entityManager;
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

        $request = $this->requestStack->getCurrentRequest() ?? new Request();
        $termsVersion = $this->getTermsVersion($terms);

        $model = new TermsEdition($termsVersion);

        $form = $this->createForm(
            TermsVersionFormType::class,
            $model,
            [TermsVersionFormType::TERMS_VERSION_ENTITY => $termsVersion]
        )->handleRequest($request);

        if ($request->getMethod() === Request::METHOD_POST && $form->isSubmitted() && $form->isValid()) {
            ($this->editTerms)($form->getData());
            $this->entityManager->flush();

            return $this->getSubmissionRedirection($terms);
        }

        return $this->render(
            '@RichIdTermsModule/admin/edit/main.html.twig',
            [
                'terms'        => $terms,
                'termsVersion' => $termsVersion,
                'form'         => $form->createView(),
            ]
        );
    }

    private function getTermsVersion(Terms $terms): TermsVersion
    {
        $request = $this->requestStack->getCurrentRequest() ?? new Request();
        $version = $request->query->get('version');

        if ($version !== null && $version !== '') {
            $termsVersion = $this->termsVersionRepository->findOneByTermsAndVersion(
                $terms->getSlug(),
                (int) $version
            );

            if ($termsVersion !== null) {
                return $termsVersion;
            }

            throw new NotFoundHttpException(\sprintf('No terms version found with version %s', $version));
        }

        return $terms->getLatestVersion() ?? TermsVersion::buildDefaultVersion($terms);
    }

    private function getSubmissionRedirection(Terms $terms): RedirectResponse
    {
        $request = $this->requestStack->getCurrentRequest() ?? new Request();
        $exit = $request->query->has('exit');

        if ($exit) {
            return $this->redirectToRoute('module_terms_admin_list');
        }

        return $this->redirectToRoute('module_terms_admin_edition', ['terms' => $terms->getId()]);
    }
}
