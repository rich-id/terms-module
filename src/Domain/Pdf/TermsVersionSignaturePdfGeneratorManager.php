<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Pdf;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;

class TermsVersionSignaturePdfGeneratorManager
{
    /** @var array<TermsVersionSignaturePdfGeneratorInterface> */
    protected $generators;

    /** @var ParameterBagInterface */
    protected $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /** @param array<TermsVersionSignaturePdfGeneratorInterface> $generators */
    public function setGenerators(array $generators): void
    {
        $this->generators = $generators;
    }

    public function hasConfiguredGenerator(): bool
    {
        return $this->getConfiguredGenerator() !== null;
    }
    
    public function getConfiguredGenerator(): ?TermsVersionSignaturePdfGeneratorInterface
    {
        $selectedGenerator = $this->parameterBag->get('rich_id_terms_module.terms_version_signature_pdf_generator') ?? null;
        
        if (empty($selectedGenerator)) {
            return null;
        }
        
        foreach ($this->generators as $generator) {
            if ($selectedGenerator === \get_class($generator)) {
                return $generator;
            }
        }
        
        return null;
    }
}
