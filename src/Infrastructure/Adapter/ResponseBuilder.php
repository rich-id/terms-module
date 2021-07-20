<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Adapter;

use RichId\TermsModuleBundle\Domain\Port\ResponseBuilderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class ResponseBuilder implements ResponseBuilderInterface
{
    /** @var RouterInterface */
    protected $router;

    /** @var ParameterBagInterface */
    protected $parameterBag;

    public function __construct(RouterInterface $router, ParameterBagInterface $parameterBag)
    {
        $this->router = $router;
        $this->parameterBag = $parameterBag;
    }

    public function buildDefaultTermsSignedResponse(?bool $accepted): Response
    {
        $url = $this->router->generate($this->getRoute($accepted));

        return new RedirectResponse($url);
    }

    protected function getRoute(?bool $accepted): string
    {
        if ($accepted === null) {
            return $this->parameterBag->get('rich_id_terms_module.default_redirection_routes.ignore');
        }

        if ($accepted) {
            return $this->parameterBag->get('rich_id_terms_module.default_redirection_routes.acceptation');
        }

        return $this->parameterBag->get('rich_id_terms_module.default_redirection_routes.refusal');
    }
}
