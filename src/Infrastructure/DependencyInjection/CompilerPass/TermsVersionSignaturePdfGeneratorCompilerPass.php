<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\DependencyInjection\CompilerPass;

use RichCongress\BundleToolbox\Configuration\AbstractCompilerPass;
use RichId\TermsModuleBundle\Domain\Pdf\TermsVersionSignaturePdfGeneratorManager;
use RichId\TermsModuleBundle\Infrastructure\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class TermsVersionSignaturePdfGeneratorCompilerPass extends AbstractCompilerPass
{
    public const TAG = 'terms_module.terms_version_signature_pdf_generator';

    public function process(ContainerBuilder $container): void
    {
        $selectedGeneratorClass = $container->getParameter(\sprintf('%s.terms_version_signature_pdf_generator', Configuration::CONFIG_NODE));

        $services = $container->findTaggedServiceIds(self::TAG);
        $generatorClasses = \array_keys($services);

        if (!empty($selectedGeneratorClass) && !\in_array($selectedGeneratorClass, $generatorClasses)) {
            throw new \InvalidArgumentException(
                \sprintf('The given generator is invalid. Available generators: %s', \implode(' / ', $generatorClasses))
            );
        }

        $serviceReferences = \array_map(
            static function (string $serviceId): Reference {
                return new Reference($serviceId);
            },
            $generatorClasses
        );

        $definition = $container->findDefinition(TermsVersionSignaturePdfGeneratorManager::class);
        $definition->addMethodCall('setGenerators', [$serviceReferences]);
    }
}
