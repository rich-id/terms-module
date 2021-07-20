<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Controller;

use RichId\TermsModuleBundle\UserInterface\Controller\AdminRouteTrait;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class DummyAdminRoute
{
    use AdminRouteTrait;

    /** @var array<string> */
    protected $roles = [];

    /** @return array<string> */
    protected function getAdminRoles(): array
    {
        return $this->roles;
    }

    /** @param array<string> $roles */
    public function setAdminRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function __invoke(): AccessDeniedException
    {
        return $this->buildAccessDeniedException();
    }
}
