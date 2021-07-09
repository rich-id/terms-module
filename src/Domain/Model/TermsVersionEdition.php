<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Model;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Infrastructure\ValidatorConstraints;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ValidatorConstraints\CantUnpublishLockedPublishedTerms
 */
class TermsVersionEdition
{
    /** @var TermsVersion */
    private $entity;

    /**
     * @var bool
     *
     * @Assert\NotNull
     * @Assert\Type("bool")
     */
    private $isTermsEnabled = false;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private $title;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private $content;

    /**
     * @var \DateTime|null
     *
     * @Assert\Type("datetime")
     */
    private $publicationDate;

    public function __construct(TermsVersion $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): TermsVersion
    {
        return $this->entity;
    }

    public function isTermsEnabled(): bool
    {
        return $this->isTermsEnabled;
    }

    public function setIsTermsEnabled(bool $isTermsEnabled): self
    {
        $this->isTermsEnabled = $isTermsEnabled;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublicationDate(): ?\DateTime
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(?\DateTime $publicationDate): self
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }
}
