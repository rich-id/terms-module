<?php declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Fixtures;

use RichCongress\RecurrentFixturesTestBundle\DataFixture\AbstractFixture;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;

final class TermsVersionFixtures extends AbstractFixture
{
    protected function loadFixtures(): void
    {
        $this->createObject(
            TermsVersion::class,
            'v1-terms-1',
            [
                'version'   => 1,
                'isEnabled' => true,
                'title'     => 'title',
                'content'   => 'content',
                'terms'     => $this->getReference(Terms::class, '1')
            ]
        );

        $this->createObject(
            TermsVersion::class,
            'v2-terms-1',
            [
                'version'   => 2,
                'isEnabled' => true,
                'title'     => 'title',
                'content'   => 'content',
                'terms'     => $this->getReference(Terms::class, '1')
            ]
        );
    }

    public function getDependencies(): array
    {
        return [
            TermsFixtures::class,
        ];
    }
}
