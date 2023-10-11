<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Guard;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;

class TermsGuardManager
{
    /** @var array<TermsGuardInterface> */
    protected $guards;

    /** @param array<TermsGuardInterface> $guards */
    public function setGuards(array $guards): void
    {
        $this->guards = $guards;
    }

    public function getGuardFor(string $termsSlug, TermsSubjectInterface $subject): ?TermsGuardInterface
    {
        foreach ($this->guards as $guard) {
            if ($guard->supports($termsSlug, $subject)) {
                return $guard;
            }
        }

        return null;
    }
}
