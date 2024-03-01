<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Domain\Entity\TermsUserInterface;
use RichId\TermsModuleBundle\Domain\Model\SignatureListForm;
use RichId\TermsModuleBundle\Domain\Pdf\TermsVersionSignaturePdfGeneratorManager;
use RichId\TermsModuleBundle\Domain\Port\SecurityInterface;
use RichId\TermsModuleBundle\Infrastructure\FormType\SignatureListFormType;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionSignatureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SignatureListAdminRoute extends AbstractController
{
    /** @var TermsVersionSignatureRepository */
    protected $termsVersionSignatureRepository;

    /** @var TermsVersionSignaturePdfGeneratorManager */
    protected $termsVersionSignaturePdfGeneratorManager;

    /** @var SecurityInterface */
    protected $security;

    /** @var RequestStack */
    protected $requestStack;

    /** @var FormFactoryInterface */
    protected $formFactory;

    public function __construct(
        TermsVersionSignatureRepository $termsVersionSignatureRepository,
        TermsVersionSignaturePdfGeneratorManager $termsVersionSignaturePdfGeneratorManager,
        SecurityInterface $security,
        RequestStack $requestStack,
        FormFactoryInterface $formFactory
    ) {
        $this->termsVersionSignatureRepository = $termsVersionSignatureRepository;
        $this->termsVersionSignaturePdfGeneratorManager = $termsVersionSignaturePdfGeneratorManager;
        $this->security = $security;
        $this->requestStack = $requestStack;
        $this->formFactory = $formFactory;
    }

    #[IsGranted('MODULE_TERMS_ADMIN')]
    public function __invoke(): Response
    {
        $request = $this->requestStack->getCurrentRequest() ?? new Request();
        $form = $this->formFactory->createNamed('', SignatureListFormType::class)->handleRequest($request);
        $data = $form->getData() ?? new SignatureListForm();

        $paginator = $this->termsVersionSignatureRepository->findForSearch($data);

        return $this->render(
            '@RichIdTermsModule/admin/signature-list/main.html.twig',
            [
                'signatures'     => \iterator_to_array($paginator->getIterator()),
                'canDownloadPdf' => $this->security->getUser() instanceof TermsUserInterface && $this->termsVersionSignaturePdfGeneratorManager->hasConfiguredGenerator(),
                'form'           => $form->createView(),
                'nbPages'        => \ceil($paginator->count() / $data->getNumberItemsPerPage()),
                'currentPage'    => $data->getPage(),
                'sort'           => $data->getSort(),
                'sortDirection'  => $data->getSortDirection(),
            ]
        );
    }
}
