<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class WithExceptionRoute extends AbstractController
{
    public function __invoke(): void
    {
        throw new \Exception();
    }
}
