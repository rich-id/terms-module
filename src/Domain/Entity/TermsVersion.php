<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionRepository;

#[ORM\Entity(repositoryClass: TermsVersionRepository::class)]
#[ORM\Table(name: 'module_terms_terms_version')]
#[ORM\UniqueConstraint(name: 'terms_version_terms_id_version_UNIQUE', columns: ['version', 'terms_id'])]
class TermsVersion
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    #[ORM\Column(name: 'version', type: 'integer', nullable: false, options: ['unsigned' => true])]
    protected int $version;

    #[ORM\Column(name: 'is_enabled', type: 'boolean', nullable: false)]
    protected bool $isEnabled = false;

    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    protected string $title;

    #[ORM\Column(name: 'content', type: 'text', nullable: false)]
    protected string $content;

    #[ORM\Column(name: 'publication_date', type: 'datetime', nullable: true)]
    protected ?\DateTime $publicationDate = null;

    #[ORM\ManyToOne(targetEntity: Terms::class, inversedBy: 'versions')]
    #[ORM\JoinColumn(name: 'terms_id', referencedColumnName: 'id', nullable: false, onDelete: 'RESTRICT')]
    protected Terms $terms;

    /** @var ArrayCollection<int, TermsVersionSignature> */
    #[ORM\OneToMany(targetEntity: TermsVersionSignature::class, mappedBy: 'version')]
    protected Collection $signatures;

    public function __construct()
    {
        $this->signatures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getVersion(): ?int
    {
        return $this->version ?? null;
    }

    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function enable(): self
    {
        $this->isEnabled = true;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title ?? null;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content ?? null;
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

    public function getTerms(): Terms
    {
        return $this->terms;
    }

    public function setTerms(Terms $terms): self
    {
        $this->terms = $terms;

        return $this;
    }

    /** @return ArrayCollection<int, TermsVersionSignature> */
    public function getSignatures(): Collection
    {
        return $this->signatures;
    }

    public function addSignature(TermsVersionSignature $signature): self
    {
        $this->signatures->add($signature);

        return $this;
    }

    public function removeSignature(TermsVersionSignature $signature): self
    {
        $this->signatures->removeElement($signature);

        return $this;
    }
}
