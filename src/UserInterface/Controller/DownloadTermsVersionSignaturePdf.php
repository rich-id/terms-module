<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Domain\Entity\TermsUserInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;
use RichId\TermsModuleBundle\Domain\Pdf\TermsVersionSignaturePdfGeneratorManager;
use RichId\TermsModuleBundle\Domain\Port\SecurityInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class DownloadTermsVersionSignaturePdf extends AbstractController
{
    /** @var TermsVersionSignaturePdfGeneratorManager */
    protected $termsVersionSignaturePdfGeneratorManager;

    /** @var SecurityInterface */
    protected $security;

    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(
        TermsVersionSignaturePdfGeneratorManager $termsVersionSignaturePdfGeneratorManager,
        SecurityInterface $security,
        TranslatorInterface $translator
    )
    {
        $this->termsVersionSignaturePdfGeneratorManager = $termsVersionSignaturePdfGeneratorManager;
        $this->security = $security;
        $this->translator = $translator;
    }

    /**
     * @IsGranted("MODULE_TERMS_ADMIN")
     * @IsGranted("MODULE_TERMS_CAN_GENERATE_SIGNATURE_PDF")
     */
    public function __invoke(TermsVersionSignature $termsVersionSignature): Response
    {
        $generator = $this->termsVersionSignaturePdfGeneratorManager->getConfiguredGenerator();
        $user = $this->security->getUser();

        if ($generator === null || !$user instanceof TermsUserInterface) {
            throw new \LogicException();
        }

        return new Response(
            $generator($termsVersionSignature, $user),
            Response::HTTP_OK,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $this->translator->trans('terms_module.pdf_signature.filename') . '"',
            ]
        );
    }
}
