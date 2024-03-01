<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionSignatureRepository;


#[ORM\Entity(repositoryClass: TermsVersionSignatureRepository::class)]
#[ORM\Table(name: 'module_terms_terms_version_signature')]
#[ORM\UniqueConstraint(name: 'module_terms_terms_version_signature_UNIQUE', columns: ['subject_type', 'subject_identifier', 'version_id'])]
class TermsVersionSignature
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: false)]
    protected \DateTime $date;

    #[ORM\Column(name: 'subject_type', type: 'string', length: 255, nullable: false)]
    protected string $subjectType;

    #[ORM\Column(name: 'subject_identifier', type: 'string', length: 255, nullable: false)]
    protected string $subjectIdentifier;

    #[ORM\Column(name: 'subject_name', type: 'string', length: 255, nullable: false)]
    protected string $subjectName;

    #[ORM\ManyToOne(targetEntity: TermsVersion::class, inversedBy: 'signatures')]
    #[ORM\JoinColumn(name: 'version_id', referencedColumnName: 'id', nullable: false, onDelete: 'RESTRICT')]
    protected TermsVersion $version;

    #[ORM\Column(name: 'signed_by', type: 'string', length: 255, nullable: true)]
    protected ?string $signedBy = null;

    #[ORM\Column(name: 'signed_by_name', type: 'string', length: 255, nullable: true)]
    protected ?string $signedByName = null;

    #[ORM\Column(name: 'signed_by_name_for_sort', type: 'string', length: 255, nullable: true)]
    protected ?string $signedByNameForSort = null;

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date ?? null;
    }

    public function setSubjectType(string $subjectType): self
    {
        $this->subjectType = $subjectType;

        return $this;
    }

    public function getSubjectType(): ?string
    {
        return $this->subjectType ?? null;
    }

    public function setSubjectIdentifier(string $subjectIdentifier): self
    {
        $this->subjectIdentifier = $subjectIdentifier;

        return $this;
    }

    public function getSubjectIdentifier(): ?string
    {
        return $this->subjectIdentifier ?? null;
    }

    public function getSubjectName(): string
    {
        return $this->subjectName;
    }

    public function setSubjectName(string $subjectName): self
    {
        $this->subjectName = $subjectName;

        return $this;
    }

    public function setVersion(TermsVersion $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion(): ?TermsVersion
    {
        return $this->version ?? null;
    }

    public function setSignedBy(?string $signedBy): self
    {
        $this->signedBy = $signedBy;

        return $this;
    }

    public function getSignedBy(): ?string
    {
        return $this->signedBy;
    }

    public function getSignedByName(): ?string
    {
        return $this->signedByName;
    }

    public function setSignedByName(?string $signedByName): self
    {
        $this->signedByName = $signedByName;

        return $this;
    }

    public function getSignedByNameForSort(): ?string
    {
        return $this->signedByNameForSort;
    }

    public function setSignedByNameForSort(?string $signedByNameForSort): self
    {
        $this->signedByNameForSort = $signedByNameForSort;

        return $this;
    }
}
