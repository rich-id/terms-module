<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use Doctrine\ORM\EntityManagerInterface;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Factory\DefaultTermsVersionFactory;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;
use RichId\TermsModuleBundle\Domain\UseCase\EditTerms;
use RichId\TermsModuleBundle\Infrastructure\FormType\TermsVersionFormType;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditAdminRoute extends AbstractController
{
    /** @var EditTerms */
    protected $editTerms;

    /** @var DefaultTermsVersionFactory */
    protected $defaultTermsVersionFactory;

    /** @var TermsVersionRepository */
    protected $termsVersionRepository;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var RequestStack */
    protected $requestStack;

    public function __construct(
        EditTerms $editTerms,
        DefaultTermsVersionFactory $defaultTermsVersionFactory,
        TermsVersionRepository $termsVersionRepository,
        EntityManagerInterface $entityManager,
        RequestStack $requestStack
    ) {
        $this->editTerms = $editTerms;
        $this->defaultTermsVersionFactory = $defaultTermsVersionFactory;
        $this->termsVersionRepository = $termsVersionRepository;
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    /** @IsGranted("MODULE_TERMS_ADMIN") */
    public function __invoke(Terms $terms): Response
    {
        $request = $this->requestStack->getCurrentRequest() ?? new Request();
        $currentTermsVersion = $this->getTermsVersion($terms);

        $model = new TermsEdition($currentTermsVersion);

        $form = $this->createForm(
            TermsVersionFormType::class,
            $model,
            [TermsVersionFormType::TERMS_VERSION_ENTITY => $currentTermsVersion]
        )->handleRequest($request);

        if ($request->getMethod() === Request::METHOD_POST && $form->isSubmitted() && $form->isValid()) {
            ($this->editTerms)($form->getData());
            $this->entityManager->flush();

            return $this->getSubmissionRedirection($terms, $currentTermsVersion);
        }

        $lastTermsVersion = $terms->getLatestVersion() ?? ($this->defaultTermsVersionFactory)($terms);

        return $this->render(
            '@RichIdTermsModule/admin/edit/main.html.twig',
            [
                'terms'               => $terms,
                'currentTermsVersion' => $currentTermsVersion,
                'lastTermsVersion'    => $lastTermsVersion,
                'form'                => $form->createView(),
            ]
        );
    }

    private function getTermsVersion(Terms $terms): TermsVersion
    {
        $request = $this->requestStack->getCurrentRequest() ?? new Request();
        $version = $request->query->get('version');

        if ($version !== null && $version !== '') {
            $termsVersion = $this->termsVersionRepository->findOneByTermsAndVersion(
                $terms->getSlug() ?? '',
                (int) $version
            );

            if ($termsVersion !== null) {
                return $termsVersion;
            }

            throw new NotFoundHttpException(\sprintf('No terms version found with version %s', $version));
        }

        return $terms->getLatestVersion() ?? ($this->defaultTermsVersionFactory)($terms);
    }

    private function getSubmissionRedirection(Terms $terms, TermsVersion $termsVersion): RedirectResponse
    {
        $request = $this->requestStack->getCurrentRequest() ?? new Request();
        $exit = $request->query->has('exit');
        $hasSpecificVersion = $request->query->has('version');

        if ($exit) {
            return $this->redirectToRoute('module_terms_admin_list');
        }

        if ($hasSpecificVersion) {
            return $this->redirectToRoute('module_terms_admin_edition', ['terms' => $terms->getId(), 'version' => $termsVersion->getVersion()]);
        }

        return $this->redirectToRoute('module_terms_admin_edition', ['terms' => $terms->getId()]);
    }
}
