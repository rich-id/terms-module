<?php declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="module_terms_terms")
 */
class Terms
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
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, length=255, unique=true, name="slug")
     */
    protected $slug;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false, name="is_published")
     */
    protected $isPublished = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false, name="is_depublication_locked")
     */
    protected $isDepublicationLocked = false;

    /**
     * @var ArrayCollection|TermsVersion[]
     *
     * @ORM\OneToMany(targetEntity="RichId\TermsModuleBundle\Domain\Entity\TermsVersion", mappedBy="terms")
     */
    private $versions;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getVersions(): ArrayCollection
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
                ->where(Criteria::expr()->eq('isEnabled', true))
                ->andWhere(
                    Criteria::expr()->orX(
                        Criteria::expr()->isNull('publicationDate'),
                        Criteria::expr()->lte('publicationDate', new \DateTime('today midnight'))
                    )
                )
                ->orderBy(['version' => 'DESC'])
        )->first();

        return $version instanceof TermsVersion ? $version : null;
    }
}
