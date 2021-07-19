<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Updater;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\InvalidValueException;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;

class TermsVersionUpdater
{
    public function update(TermsVersion $termsVersion, TermsEdition $termsEdition): TermsVersion
    {
        if ($termsEdition->getTitle() === null || $termsEdition->getTitle() === '') {
            throw new InvalidValueException('title', $termsEdition->getTitle());
        }

        if ($termsEdition->getContent() === null || $termsEdition->getContent() === '') {
            throw new InvalidValueException('content', $termsEdition->getContent());
        }

        $termsVersion->setTitle($termsEdition->getTitle());
        $termsVersion->setContent($termsEdition->getContent());
        $termsVersion->setPublicationDate($termsEdition->getPublicationDate());

        return $termsVersion;
    }
}
