<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Port;

use Symfony\Component\HttpFoundation\Response;

interface ResponseBuilderInterface
{
    public function buildDefaultTermsSignedResponse(?bool $accepted): Response;
}
