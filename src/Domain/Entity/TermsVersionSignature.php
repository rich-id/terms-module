<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionSignatureRepository")
 * @ORM\Table(
 *     name="module_terms_terms_version_signature",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="module_terms_terms_version_signature_UNIQUE", columns={"subject_type", "subject_identifier", "version_id"})
 *     }
 * )
 */
class TermsVersionSignature
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="date")
     */
    protected $date;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, length=255, name="subject_type")
     */
    protected $subjectType;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, length=255, name="subject_identifier")
     */
    protected $subjectIdentifier;

    /**
     * @var TermsVersion
     *
     * @ORM\ManyToOne(targetEntity="RichId\TermsModuleBundle\Domain\Entity\TermsVersion", inversedBy="signatures")
     * @ORM\JoinColumn(name="version_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected $version;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setSubjectType(string $subjectType): self
    {
        $this->subjectType = $subjectType;

        return $this;
    }

    public function getSubjectType(): ?string
    {
        return $this->subjectType;
    }

    public function setSubjectIdentifier(string $subjectIdentifier): self
    {
        $this->subjectIdentifier = $subjectIdentifier;

        return $this;
    }

    public function getSubjectIdentifier(): ?string
    {
        return $this->subjectIdentifier;
    }

    public function setVersion(TermsVersion $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion(): ?TermsVersion
    {
        return $this->version;
    }
}
