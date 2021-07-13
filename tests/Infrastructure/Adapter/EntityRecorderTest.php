<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Infrastructure\Adapter;

use RichCongress\TestFramework\TestConfiguration\Annotation\TestConfig;
use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersionSignature;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use RichId\TermsModuleBundle\Infrastructure\Adapter\EntityRecorder;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsRepository;
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

    /** @var TermsRepository */
    public $termsRepository;

    /** @var TermsVersionRepository */
    public $termsVersionRepository;

    /** @var TermsVersionSignatureRepository */
    public $termsVersionSignatureRepository;

    public function testSaveSignature(): void
    {
        $this->assertCount(5, $this->termsVersionSignatureRepository->findAll());
        $version = $this->getReference(TermsVersion::class, 'v3-terms-1');
        $subject = DummySubject::create('user', '42');

        $signature = TermsVersionSignature::sign($version, $subject);
        $this->adapter->saveSignature($signature);
        $this->adapter->flush();

        $this->assertCount(6, $this->termsVersionSignatureRepository->findAll());
    }

    public function testSaveTerms(): void
    {
        $this->assertCount(5, $this->termsRepository->findAll());

        $terms = new Terms();
        $terms->setSlug('my_terms');
        $terms->setName('My terms');

        $this->adapter->saveTerms($terms);
        $this->adapter->flush();

        $this->assertCount(6, $this->termsRepository->findAll());
    }

    public function testSaveTermsVersion(): void
    {
        $this->assertCount(6, $this->termsVersionRepository->findAll());
        $terms = $this->getReference(Terms::class, '1');

        $termsVersion = TermsVersion::buildDefaultVersion($terms);
        $termsVersion->setVersion(15);
        $termsVersion->setTitle('Title');
        $termsVersion->setContent('Content');

        $this->adapter->saveTermsVersion($termsVersion);
        $this->adapter->flush();

        $this->assertCount(7, $this->termsVersionRepository->findAll());
    }
}
