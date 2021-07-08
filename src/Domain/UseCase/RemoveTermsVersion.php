<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\UseCase;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\EnabledVersionCannotBeDeletedException;
use RichId\TermsModuleBundle\Domain\Port\EntityRemoverInterface;

class RemoveTermsVersion
{
    /** @var EntityRemoverInterface */
    protected $entityRemover;

    public function __construct(EntityRemoverInterface $entityRemover)
    {
        $this->entityRemover = $entityRemover;
    }

    public function __invoke(TermsVersion $termsVersion): void
    {
        if ($termsVersion->isEnabled()) {
            throw new EnabledVersionCannotBeDeletedException($termsVersion);
        }

        $this->entityRemover->removeTermsVersion($termsVersion);
    }
}
