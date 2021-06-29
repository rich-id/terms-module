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
        $terms1 = $this->getTerms('1');

        $this->createObject(
            TermsVersion::class,
            'v1-terms-1',
            [
                'version'   => 1,
                'isEnabled' => true,
                'title'     => 'title',
                'content'   => 'content',
                'terms'     => $terms1,
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
                'terms'     => $terms1,
            ]
        );

        $this->createObject(
            TermsVersion::class,
            'v3-terms-1',
            [
                'version'   => 3,
                'isEnabled' => true,
                'title'     => 'title',
                'content'   => 'content',
                'terms'     => $terms1,
            ]
        );

        $this->createObject(
            TermsVersion::class,
            'v4-terms-1',
            [
                'version'   => 4,
                'isEnabled' => false,
                'title'     => 'title',
                'content'   => 'content',
                'terms'     => $terms1,
            ]
        );
    }

    public function getDependencies(): array
    {
        return [
            TermsFixtures::class,
        ];
    }

    private function getTerms(string $reference): ?Terms
    {
        $termsFixture = $this->getReference(Terms::class, $reference);

        if ($termsFixture === null) {
            return null;
        }

        return $this->manager
            ->getRepository(Terms::class)
            ->find($termsFixture->getId());
    }
}
