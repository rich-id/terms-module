<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\DependencyInjection;

use RichCongress\BundleToolbox\Configuration\AbstractConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class Configuration extends AbstractConfiguration
{
    public const CONFIG_NODE = 'rich_id_terms_module';

    protected function buildConfiguration(ArrayNodeDefinition $rootNode): void
    {
        $children = $rootNode->children();

        $this->addAdminRoles($children);
        $this->addDefaultRefusalRoute($children);
        $this->addAccessDeniedRedirection($children);
    }

    protected function addAdminRoles(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('admin_roles')
            ->example(['ROLE_ADMIN'])
            ->scalarPrototype();
    }

    protected function addDefaultRefusalRoute(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder->scalarNode('default_refusal_route');
    }

    protected function addAccessDeniedRedirection(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('access_denied_redirection')
            ->example(['app_front_protected_by_terms'])
            ->defaultValue([])
            ->scalarPrototype();
    }
}
