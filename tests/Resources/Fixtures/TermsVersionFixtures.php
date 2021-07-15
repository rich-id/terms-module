<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Fixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use RichCongress\RecurrentFixturesTestBundle\DataFixture\AbstractFixture;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;

final class TermsVersionFixtures extends AbstractFixture implements DependentFixtureInterface
{
    protected function loadFixtures(): void
    {
        $terms1 = $this->getReference(Terms::class, '1');
        $terms4 = $this->getReference(Terms::class, '4');
        $terms5 = $this->getReference(Terms::class, '5');

        $this->createObject(
            TermsVersion::class,
            'v1-terms-1',
            [
                'version'   => 1,
                'isEnabled' => true,
                'title'     => 'Title Version 1',
                'content'   => 'Content Version 1',
                'terms'     => $terms1,
            ]
        );

        $this->createObject(
            TermsVersion::class,
            'v2-terms-1',
            [
                'version'   => 2,
                'isEnabled' => true,
                'title'     => 'Title Version 2',
                'content'   => 'Content Version 2',
                'terms'     => $terms1,
            ]
        );

        $this->createObject(
            TermsVersion::class,
            'v3-terms-1',
            [
                'version'   => 3,
                'isEnabled' => true,
                'title'     => 'Title Version 3',
                'content'   => 'Content Version 3',
                'terms'     => $terms1,
            ]
        );

        $this->createObject(
            TermsVersion::class,
            'v4-terms-1',
            [
                'version'   => 4,
                'isEnabled' => false,
                'title'     => 'Title Version 4',
                'content'   => 'Content Version 4',
                'terms'     => $terms1,
            ]
        );

        $this->createObject(
            TermsVersion::class,
            'v1-terms-4',
            [
                'version'   => 1,
                'isEnabled' => true,
                'title'     => 'Title Version 1',
                'content'   => 'Content Version 1',
                'terms'     => $terms4,
            ]
        );

        $this->createObject(
            TermsVersion::class,
            'v1-terms-5',
            [
                'version'   => 1,
                'isEnabled' => true,
                'title'     => 'Title Version 1',
                'content'   => 'Content Version 1',
                'terms'     => $terms5,
            ]
        );

        $this->createObject(
            TermsVersion::class,
            'v2-terms-5',
            [
                'version'   => 2,
                'isEnabled' => false,
                'title'     => 'Title Version 2',
                'content'   => 'Content Version 2',
                'terms'     => $terms5,
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
