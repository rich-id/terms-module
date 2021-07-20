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
        $this->addDefaultRedirectionRoutes($children);
    }

    protected function addAdminRoles(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('admin_roles')
            ->example(['ROLE_ADMIN'])
            ->scalarPrototype();
    }

    protected function addDefaultRedirectionRoutes(NodeBuilder $nodeBuilder): void
    {
        $children = $nodeBuilder
            ->arrayNode('default_redirection_routes')
            ->isRequired()
            ->children();

        $this->addDefaultAcceptationRoute($children);
        $this->addDefaultRefusalRoute($children);
        $this->addDefaulIgnoreRoute($children);
    }

    protected function addDefaultAcceptationRoute(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->scalarNode('acceptation')
            ->isRequired();
    }

    protected function addDefaultRefusalRoute(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->scalarNode('refusal')
            ->isRequired();
    }

    protected function addDefaulIgnoreRoute(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->scalarNode('ignore')
            ->isRequired();
    }
}
