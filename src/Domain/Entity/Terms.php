<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsRepository;

#[ORM\Entity(repositoryClass: TermsRepository::class)]
#[ORM\Table(name: 'module_terms_terms')]
class Terms
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    #[ORM\Column(name: 'slug', type: 'string', length: 255, unique: true, nullable: false)]
    protected string $slug;

    #[ORM\Column(name: 'name', type: 'string', length: 255, unique: true, nullable: false)]
    protected string $name;

    #[ORM\Column(name: 'is_published', type: 'boolean', nullable: false)]
    protected bool $isPublished = false;

    #[ORM\Column(name: 'is_depublication_locked', type: 'boolean', nullable: false)]
    protected bool $isDepublicationLocked = false;

    /** @var ArrayCollection<int, TermsVersion> */
    #[ORM\OneToMany(targetEntity: TermsVersion::class, mappedBy: "terms")]
    private Collection $versions;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getSlug(): ?string
    {
        return $this->slug ?? null;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name ?? null;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function isDepublicationLocked(): bool
    {
        return $this->isDepublicationLocked;
    }

    public function setIsDepublicationLocked(bool $isDepublicationLocked): self
    {
        $this->isDepublicationLocked = $isDepublicationLocked;

        return $this;
    }

    /** @return ArrayCollection<int, TermsVersion> */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function addVersion(TermsVersion $version): self
    {
        $this->versions->add($version);

        return $this;
    }

    public function removeVersion(TermsVersion $version): self
    {
        $this->versions->removeElement($version);

        return $this;
    }

    public function getLatestVersion(): ?TermsVersion
    {
        $version = $this->versions->matching(
            Criteria::create()
                ->orderBy(['version' => 'DESC'])
        )->first();

        return $version instanceof TermsVersion ? $version : null;
    }

    public function getLatestPublishedVersion(): ?TermsVersion
    {
        $version = $this->versions->matching(
            Criteria::create()
                ->where(Criteria::expr()->eq('isEnabled', true))
                ->andWhere(
                    Criteria::expr()->orX(
                        Criteria::expr()->isNull('publicationDate'),
                        Criteria::expr()->lte('publicationDate', new \DateTime('now'))
                    )
                )
                ->orderBy(['version' => 'DESC'])
        )->first();

        return $version instanceof TermsVersion ? $version : null;
    }
}
