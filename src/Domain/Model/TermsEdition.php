<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Model;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Infrastructure\ValidatorConstraints;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ValidatorConstraints\CantContentChangeIfTermsPublished
 * @ValidatorConstraints\CantPublicationDateChangeIfTermsPublished
 * @ValidatorConstraints\CantTitleChangeIfTermsPublished
 * @ValidatorConstraints\CantUnpublishLockedPublishedTerms
 */
class TermsEdition
{
    /** @var TermsVersion */
    private $entity;

    /**
     * @var bool|null
     *
     * @Assert\NotNull
     * @Assert\Type("bool")
     */
    private $isTermsPublished;

    /**
     * @var string|null
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private $title;

    /**
     * @var string|null
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

        $this->isTermsPublished = $entity->getTerms()->isPublished();
        $this->title = $entity->getTitle() ?? '';
        $this->content = $entity->getContent() ?? '';
        $this->publicationDate = $entity->getPublicationDate();
    }

    public function getEntity(): TermsVersion
    {
        return $this->entity;
    }

    public function isTermsPublished(): ?bool
    {
        return $this->isTermsPublished;
    }

    public function setIsTermsPublished(?bool $isTermsPublished): self
    {
        $this->isTermsPublished = $isTermsPublished;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
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
