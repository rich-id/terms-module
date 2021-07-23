<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Port;

use Symfony\Component\Security\Core\User\UserInterface;

interface SecurityInterface
{
    public function getUser(): ?UserInterface;
}
