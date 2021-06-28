<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
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

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function getSubjectType(): ?string
    {
        return $this->subjectType;
    }

    public function getSubjectIdentifier(): ?string
    {
        return $this->subjectIdentifier;
    }

    public function getVersion(): ?TermsVersion
    {
        return $this->version;
    }

    public static function sign(TermsVersion $version, TermsSubjectInterface $subject): self
    {
        $entity = new static();

        $entity->version = $version;
        $entity->subjectType = $subject->getTermsSubjectType();
        $entity->subjectIdentifier = $subject->getTermsSubjectIdentifier();
        $entity->date = new \DateTime();

        return $entity;
    }
}
