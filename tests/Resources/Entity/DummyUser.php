<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'app_user')]
final class DummyUser implements UserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $username;

    /** @var array<string> */
    #[ORM\Column(name: 'roles', type: 'json', nullable: false)]
    private array $roles = [];

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /** @return array<string> */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): string
    {
        return '';
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }
}
