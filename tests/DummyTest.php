<?php declare(strict_types=1);

namespace RichId\TermsModuleBundle\tests;

use RichCongress\TestTools\TestCase\TestCase;
use RichId\TermsModuleBundle\RichIdTermsModuleBundle;

/**
 * Class DummyTest
 *
 * @package   RichId\TermsModuleBundle\Tests
 * @author    Nicolas Guilloux <nguilloux@rich-id.com>
 * @copyright 2014 - 2020 RichId (https://www.rich-id.com)
 *
 * @covers \RichId\TermsModuleBundle\RichIdTermsModuleBundle
 */
class DummyTest extends TestCase
{
    public function testInstanciateBundle(): void
    {
        $bundle = new RichIdTermsModuleBundle();

        self::assertInstanceOf(RichIdTermsModuleBundle::class, $bundle);
    }
}
