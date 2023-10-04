<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Domain\Pdf\TermsVersionSignaturePdfGeneratorManager;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsRepository;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionSignatureRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SignatureListAdminRoute extends AbstractController
{
    /** @var TermsVersionSignatureRepository */
    protected $termsVersionSignatureRepository;

    /** @var TermsVersionSignaturePdfGeneratorManager */
    protected $termsVersionSignaturePdfGeneratorManager;

    public function __construct(TermsVersionSignatureRepository $termsVersionSignatureRepository, TermsVersionSignaturePdfGeneratorManager $termsVersionSignaturePdfGeneratorManager)
    {
        $this->termsVersionSignatureRepository = $termsVersionSignatureRepository;
        $this->termsVersionSignaturePdfGeneratorManager = $termsVersionSignaturePdfGeneratorManager;
    }

    /** @IsGranted("MODULE_TERMS_ADMIN") */
    public function __invoke(): Response
    {
        return $this->render(
            '@RichIdTermsModule/admin/signature-list/main.html.twig',
            [
                'signatures'   => $this->termsVersionSignatureRepository->findAllForSearch(),
                'hasGenerator' => $this->termsVersionSignaturePdfGeneratorManager->hasConfiguredGenerator(),
            ]
        );
    }
}
