<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\DependencyInjection;

use RichCongress\BundleToolbox\Configuration\AbstractConfiguration;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class Configuration extends AbstractConfiguration
{
    public const CONFIG_NODE = 'rich_id_terms_module';

    protected function buildConfig(NodeBuilder $nodeBuilder): void
    {
        $this->addAdminRoles($nodeBuilder);
        $this->addDefaultRedirectionRoutes($nodeBuilder);
        $this->addTermsVersionSignaturePdfGenerator($nodeBuilder);
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
        $this->addDefaultIgnoreRoute($children);
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

    protected function addDefaultIgnoreRoute(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->scalarNode('ignore')
            ->isRequired();
    }

    protected function addTermsVersionSignaturePdfGenerator(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder->scalarNode('terms_version_signature_pdf_generator')->defaultNull();
    }
}
