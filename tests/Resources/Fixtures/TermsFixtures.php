<?php declare(strict_types=1);

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
                'isPublished'           => true,
                'isDepublicationLocked' => true,
            ]
        );

        $this->createObject(
            Terms::class,
            '2',
            [
                'slug'                  => 'terms-2',
                'isPublished'           => false,
                'isDepublicationLocked' => false,
            ]
        );

        $this->createObject(
            Terms::class,
            '3',
            [
                'slug'                  => 'terms-3',
                'isPublished'           => true,
                'isDepublicationLocked' => false,
            ]
        );
    }
}
