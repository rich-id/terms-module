<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Domain\Model;

use RichCongress\TestSuite\TestCase\TestCase;
use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;

/** @covers \RichId\TermsModuleBundle\Domain\Model\DummySubject */
final class DummySubjectTest extends TestCase
{
    public function testModel(): void
    {
        $model = DummySubject::create('subject_type', 'subject_identifier');

        $this->assertInstanceOf(TermsSubjectInterface::class, $model);
        $this->assertSame('subject_type', $model->getTermsSubjectType());
        $this->assertSame('subject_identifier', $model->getTermsSubjectIdentifier());
    }
}
