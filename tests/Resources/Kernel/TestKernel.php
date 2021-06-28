<?php declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Kernel;

use RichCongress\WebTestBundle\Kernel\DefaultTestKernel;

class TestKernel extends DefaultTestKernel
{
    public function __construct()
    {
        parent::__construct('test', false);
    }

    public function getConfigurationDir(): ?string
    {
        return __DIR__ . '/config';
    }
}
