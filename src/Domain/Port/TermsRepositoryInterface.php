<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Port;

use RichId\TermsModuleBundle\Domain\Entity\Terms;

interface TermsRepositoryInterface
{
    public function findOneBySlug(string $termsSlug): ?Terms;
}
