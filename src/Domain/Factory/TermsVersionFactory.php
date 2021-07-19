<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Factory;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\InvalidValueException;

class TermsVersionFactory
{
    public function buildDefaultVersion(Terms $terms): TermsVersion
    {
        $entity = new TermsVersion();

        $entity->setTerms($terms);
        $entity->setVersion(1);
        $terms->addVersion($entity);

        return $entity;
    }

    public function buildFromCopy(TermsVersion $termsVersion): TermsVersion
    {
        if ($termsVersion->getTitle() === null || $termsVersion->getTitle() === '') {
            throw new InvalidValueException('title', $termsVersion->getTitle());
        }

        if ($termsVersion->getContent() === null || $termsVersion->getContent() === '') {
            throw new InvalidValueException('content', $termsVersion->getContent());
        }

        $terms = $termsVersion->getTerms();
        $lastVersion = $terms->getLatestVersion();
        $nextVersion = $lastVersion !== null ? $lastVersion->getVersion() + 1 : 1;

        $entity = new TermsVersion();

        $entity->setTerms($terms);
        $entity->setVersion($nextVersion);
        $entity->setTitle($termsVersion->getTitle());
        $entity->setContent($termsVersion->getContent());

        return $entity;
    }
}
