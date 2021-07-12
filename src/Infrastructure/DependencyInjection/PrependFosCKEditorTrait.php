<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait PrependFosCKEditorTrait
{
    private function prependFosCKEditor(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('fos_ck_editor')) {
            return;
        }

        $container->prependExtensionConfig(
            'fos_ck_editor',
            [
                'toolbars' => [
                    'configs' => [
                        'terms_module' => [
                            '@full.clipboard',
                            '@full.basic_styles',
                            '@full.paragraph',
                            '/',
                            '@full.styles',
                            '@full.colors',
                        ],
                    ],
                ],
            ]
        );
    }
}
