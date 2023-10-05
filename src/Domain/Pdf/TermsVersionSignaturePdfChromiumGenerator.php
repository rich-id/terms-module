<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Pdf;

use HeadlessChromium\BrowserFactory;
use RichId\TermsModuleBundle\Domain\Entity\TermsUserInterface;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;
use Twig\Environment;

class TermsVersionSignaturePdfChromiumGenerator implements TermsVersionSignaturePdfGeneratorInterface
{
    /** @var Environment */
    protected $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(TermsVersionSignature $termsVersionSignature, ?TermsUserInterface $editor = null): string
    {
        $content = $this->twig->render(
            '@RichIdTermsModule/admin/signature-list/_partial/_pdf.html.twig',
            [
                'signature' => $termsVersionSignature,
                'editor'    => $editor,
            ]
        );

        $browserFactory = new BrowserFactory('chromium'); // todo conf
        $browser = $browserFactory->createBrowser(['customFlags' => ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage']]); // todo conf

        try {
            $page = $browser->createPage();
            $page->setHtml($content, 10000);
            $page->waitUntilContainsElement('#pdf-fonts-loaded');
            $pdf = \base64_decode(
                $page->pdf(
                    [
                        'printBackground'     => true,
                        'preferCSSPageSize'   => true,
                        'displayHeaderFooter' => true,
                        'footerTemplate'      => $this->twig->render('@RichIdTermsModule/admin/signature-list/_partial/_pdf-footer.html.twig'),
                        'headerTemplate'      => '<div></div>',
                    ]
                )
                    ->getBase64()
            );

            $browser->close();

            return $pdf;
        } catch (\Throwable $e) {
            dump($e);
            throw new \Exception('An error occured on pdf generation.');
        } finally {
            $browser->close();
        }
    }
}
