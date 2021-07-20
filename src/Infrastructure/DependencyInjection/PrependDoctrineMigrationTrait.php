<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait PrependDoctrineMigrationTrait
{
    private function prependDoctrineMigrations(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('doctrine_migrations')) {
            return;
        }

        $doctrineConfig = $container->getExtensionConfig('doctrine_migrations');
        $doctrineMigrationPaths = \array_pop($doctrineConfig)['migrations_paths'] ?? [];

        $container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => \array_merge($doctrineMigrationPaths, [
                'RichId\TermsModuleBundle\Infrastructure\Migrations' => '@RichIdTermsModuleBundle/Migrations',
            ]),
        ]);
    }
}
