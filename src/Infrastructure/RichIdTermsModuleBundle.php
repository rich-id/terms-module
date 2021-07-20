<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use RichCongress\BundleToolbox\Configuration\AbstractBundle;
use RichId\TermsModuleBundle\Infrastructure\DependencyInjection\CompilerPass\TermsGuardCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RichIdTermsModuleBundle extends AbstractBundle
{
    public const COMPILER_PASSES = [TermsGuardCompilerPass::class];

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $this->addDoctrineOrmMappingsPass($container);
    }

    private function addDoctrineOrmMappingsPass(ContainerBuilder $container): void
    {
        if (!\class_exists(DoctrineOrmMappingsPass::class)) {
            return;
        }

        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createAnnotationMappingDriver(
                ['RichId\TermsModuleBundle\Domain\Entity'],
                [__DIR__ . '/../Domain/Entity']
            )
        );
    }
}
