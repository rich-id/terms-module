<?php declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests;

use RichCongress\TestTools\TestCase\TestCase;
use RichId\TermsModuleBundle\Infrastructure\RichIdTermsModuleBundle;

/** @covers \RichId\TermsModuleBundle\Infrastructure\RichIdTermsModuleBundle */
class DummyTest extends TestCase
{
    public function testInstanciateBundle(): void
    {
        $bundle = new RichIdTermsModuleBundle();

        self::assertInstanceOf(RichIdTermsModuleBundle::class, $bundle);
    }
}
