<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\DependencyInjection\CompilerPass;

use RichCongress\BundleToolbox\Configuration\AbstractCompilerPass;
use RichId\TermsModuleBundle\Domain\Guard\TermsGuardManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class TermsGuardCompilerPass extends AbstractCompilerPass
{
    public const TAG = 'terms_module.guard';

    public function process(ContainerBuilder $container): void
    {
        $services = $container->findTaggedServiceIds(self::TAG);
        $serviceReferences = \array_map(
            static function (string $serviceId): Reference {
                return new Reference($serviceId);
            },
            \array_keys($services)
        );

        $definition = $container->findDefinition(TermsGuardManager::class);
        $definition->addMethodCall('setGuards', [$serviceReferences]);
    }
}
