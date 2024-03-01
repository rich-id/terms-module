<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Adapter;

use RichId\TermsModuleBundle\Domain\Port\SecurityInterface;
use \Symfony\Bundle\SecurityBundle\Security as SymfonySecurity;
use Symfony\Component\Security\Core\User\UserInterface;

class Security implements SecurityInterface
{
    /** @var SymfonySecurity */
    protected $security;

    public function __construct(SymfonySecurity $security)
    {
        $this->security = $security;
    }

    public function getUser(): ?UserInterface
    {
        return $this->security->getUser();
    }
}
