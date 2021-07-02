<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

trait AdminRouteTrait
{
    /** @return array<string> */
    abstract protected function getAdminRoles(): array;

    protected function buildAccessDeniedException(): AccessDeniedException
    {
        $roles = $this->getAdminRoles();

        if (empty($roles)) {
            return new AccessDeniedException('You must define one or more roles in the configuration.');
        }

        if (\count($roles) === 1) {
            return new AccessDeniedException(
                \sprintf(
                    'Only the "%s" role is allowed to access the back office.',
                    $roles[0]
                )
            );
        }

        return new AccessDeniedException(
            \sprintf(
                'Only the "%s" roles are allowed to access the back office.',
                \implode(', ', $roles)
            )
        );
    }
}
