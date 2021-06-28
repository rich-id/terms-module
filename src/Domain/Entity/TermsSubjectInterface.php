<?php declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Entity;

interface TermsSubjectInterface
{
    public function getTermsSubjectType(): string;
    public function getTermsSubjectIdentifier(): string;
}
