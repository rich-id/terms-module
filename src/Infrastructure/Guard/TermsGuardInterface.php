<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Guard;

interface TermsGuardInterface
{
    public function supports(string $slug, string $subjectType, string $subjectIdentifier): bool;

    public function check(string $slug, string $subjectType, string $subjectIdentifier): bool;
}
