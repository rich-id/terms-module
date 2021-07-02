<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\Adapter;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Infrastructure\Adapter\EntityRecorder;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionRepository;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionSignatureRepository;

/**
 * @covers \RichId\TermsModuleBundle\Infrastructure\Adapter\EntityRecorder
 * @TestConfig("fixtures")
 */
final class EntityRecorderTest extends TestCase
{
    /** @var EntityRecorder */
    public $adapter;

    /** @var TermsVersionSignatureRepository */
    public $termsVersionSignatureRepository;

    /** @var TermsVersionRepository */
    public $termsVersionRepository;

    public function testSaveSignature(): void
    {
        $this->assertCount(5, $this->termsVersionSignatureRepository->findAll());
        $version = $this->termsVersionRepository->find($this->getReference(TermsVersion::class, 'v3-terms-1'));
        $subject = DummySubject::create('user', '42');

        /* @phpstan-ignore-next-line */
        $signature = TermsVersionSignature::sign($version, $subject);
        $this->adapter->saveSignature($signature);

        $this->assertCount(6, $this->termsVersionSignatureRepository->findAll());
    }
}
