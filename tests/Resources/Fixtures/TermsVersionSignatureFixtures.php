<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Fixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use RichCongress\RecurrentFixturesTestBundle\DataFixture\AbstractFixture;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;

final class TermsVersionSignatureFixtures extends AbstractFixture implements DependentFixtureInterface
{
    protected function loadFixtures(): void
    {
        $termsVersion1 = $this->getReference(TermsVersion::class, 'v1-terms-1');
        $termsVersion2 = $this->getReference(TermsVersion::class, 'v2-terms-1');
        $termsVersion3 = $this->getReference(TermsVersion::class, 'v3-terms-1');
        $termsVersion4 = $this->getReference(TermsVersion::class, 'v1-terms-5');

        $this->createObject(
            TermsVersionSignature::class,
            'u42-signature-v1-terms-1',
            [
                'date'              => new \DateTime(),
                'subjectType'       => 'user',
                'subjectIdentifier' => '42',
                'version'           => $termsVersion1,
            ]
        );

        $this->createObject(
            TermsVersionSignature::class,
            'u42-signature-v2-terms-1',
            [
                'date'              => new \DateTime(),
                'subjectType'       => 'user',
                'subjectIdentifier' => '42',
                'version'           => $termsVersion2,
            ]
        );

        $this->createObject(
            TermsVersionSignature::class,
            'u43-signature-v1-terms-1',
            [
                'date'              => new \DateTime(),
                'subjectType'       => 'user',
                'subjectIdentifier' => '43',
                'version'           => $termsVersion1,
            ]
        );

        $this->createObject(
            TermsVersionSignature::class,
            'u43-signature-v2-terms-1',
            [
                'date'              => new \DateTime(),
                'subjectType'       => 'user',
                'subjectIdentifier' => '43',
                'version'           => $termsVersion2,
            ]
        );

        $this->createObject(
            TermsVersionSignature::class,
            'u43-signature-v3-terms-1',
            [
                'date'              => new \DateTime(),
                'subjectType'       => 'user',
                'subjectIdentifier' => '43',
                'version'           => $termsVersion3,
            ]
        );

        $this->createObject(
            TermsVersionSignature::class,
            'my_user_2-signature-v1-terms-5',
            [
                'date'              => new \DateTime(),
                'subjectType'       => 'user',
                'subjectIdentifier' => 'my_user_2',
                'version'           => $termsVersion4,
            ]
        );
    }

    public function getDependencies(): array
    {
        return [
            TermsVersionFixtures::class,
        ];
    }
}
