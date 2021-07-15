<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use RichId\TermsModuleBundle\Domain\Model\TermsEdition;

/**
 * @ORM\Entity(repositoryClass="RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionRepository")
 * @ORM\Table(
 *     name="module_terms_terms_version",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="terms_version_terms_id_version_UNIQUE", columns={"version", "terms_id"})
 *     }
 * )
 */
class TermsVersion
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
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, name="version", options={"unsigned":true})
     */
    protected $version;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false, name="is_enabled")
     */
    protected $isEnabled = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, length=255, name="title")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=false, name="content")
     */
    protected $content;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true, name="publication_date")
     */
    protected $publicationDate;

    /**
     * @var Terms
     *
     * @ORM\ManyToOne(targetEntity="RichId\TermsModuleBundle\Domain\Entity\Terms", inversedBy="versions")
     * @ORM\JoinColumn(name="terms_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    protected $terms;

    /**
     * @var ArrayCollection<int, TermsVersionSignature>
     *
     * @ORM\OneToMany(targetEntity="RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature", mappedBy="version")
     */
    protected $signatures;

    public function __construct()
    {
        $this->signatures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): ?int
    {
        return $this->version;
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

    public static function buildDefaultVersion(Terms $terms): self
    {
        $entity = new self();

        $entity->setTerms($terms);
        $entity->setVersion(1);
        $terms->addVersion($entity);

        return $entity;
    }

    public static function buildFromCopy(TermsVersion $termsVersion): self
    {
        $terms = $termsVersion->getTerms();
        $lastVersion = $terms->getLatestVersion();
        $nextVersion = $lastVersion !== null ? $lastVersion->getVersion() + 1 : 1;

        $entity = new self();

        $entity->setTerms($terms);
        $entity->setVersion($nextVersion);
        $entity->setTitle($termsVersion->getTitle()); /* @phpstan-ignore-line */
        $entity->setContent($termsVersion->getContent()); /* @phpstan-ignore-line */

        return $entity;
    }

    public function update(TermsEdition $termsEdition): self
    {
        $this->title = $termsEdition->getTitle(); /* @phpstan-ignore-line */
        $this->content = $termsEdition->getContent(); /* @phpstan-ignore-line */
        $this->publicationDate = $termsEdition->getPublicationDate();

        return $this;
    }
}
