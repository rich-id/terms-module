<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Fixtures;

use RichCongress\RecurrentFixturesTestBundle\DataFixture\AbstractFixture;
use RichId\TermsModuleBundle\Domain\Entity\Terms;

final class TermsFixtures extends AbstractFixture
{
    protected function loadFixtures(): void
    {
        $this->createObject(
            Terms::class,
            '1',
            [
                'slug'                  => 'terms-1',
                'name'                  => 'Terms 1',
                'isPublished'           => true,
                'isDepublicationLocked' => true,
            ]
        );

        $this->createObject(
            Terms::class,
            '2',
            [
                'slug'                  => 'terms-2',
                'name'                  => 'Terms 2',
                'isPublished'           => false,
                'isDepublicationLocked' => false,
            ]
        );

        $this->createObject(
            Terms::class,
            '3',
            [
                'slug'                  => 'terms-3',
                'name'                  => 'Terms 3',
                'isPublished'           => true,
                'isDepublicationLocked' => false,
            ]
        );

        $this->createObject(
            Terms::class,
            '4',
            [
                'slug'                  => 'terms-4',
                'name'                  => 'Terms 4',
                'isPublished'           => true,
                'isDepublicationLocked' => false,
            ]
        );

        $this->createObject(
            Terms::class,
            '5',
            [
                'slug'                  => 'terms-5',
                'name'                  => 'Terms 5',
                'isPublished'           => true,
                'isDepublicationLocked' => false,
            ]
        );
    }
}
