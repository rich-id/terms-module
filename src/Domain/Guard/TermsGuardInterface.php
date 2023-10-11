<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Guard;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;

interface TermsGuardInterface
{
    public function supports(string $slug, TermsSubjectInterface $subject): bool;

    public function check(string $slug, TermsSubjectInterface $subject): bool;

    public function getSubjectName(TermsSubjectInterface $subject): ?string;
}
