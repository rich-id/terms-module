<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class FrontRoute extends AbstractController
{
    public function __invoke(): Response
    {
        return new Response('<html><body>app_front</body></html>');
    }
}
