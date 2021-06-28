<?php declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Model;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use Symfony\Component\Validator\Constraints as Assert;

class TermsVersionEdition
{
    /**
     * @var TermsVersion
     *
     * @Assert\NotNull
     * @Assert\Type("RichId\TermsModuleBundle\Domain\Entity\TermsVersion")
     */
    protected $entity;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    protected $title;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    protected $content;

    /**
     * @var \DateTime
     *
     * @Assert\Type("datetime")
     */
    protected $publicationDate;

    /**
     * @var bool
     *
     * @Assert\NotNull
     * @Assert\Type("bool")
     */
    protected $isEnabled;
}
