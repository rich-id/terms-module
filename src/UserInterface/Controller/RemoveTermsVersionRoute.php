<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\EnabledVersionCannotBeDeletedException;
use RichId\TermsModuleBundle\Domain\UseCase\RemoveTermsVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RemoveTermsVersionRoute extends AbstractController
{
    use AdminRouteTrait;

    /** @var RemoveTermsVersion */
    protected $removeTermsVersion;

    /** @var array<string> */
    protected $adminRoles;

    public function __construct(RemoveTermsVersion $removeTermsVersion, ParameterBagInterface $parameterBag)
    {
        $this->removeTermsVersion = $removeTermsVersion;
        $this->adminRoles = $parameterBag->get('rich_id_terms_module.admin_roles');
    }

    /** @return array<string> */
    protected function getAdminRoles(): array
    {
        return $this->adminRoles;
    }

    public function __invoke(TermsVersion $termsVersion): Response
    {
        if (!$this->isGranted('MODULE_TERMS_ADMIN')) {
            throw $this->buildAccessDeniedException();
        }

        try {
            ($this->removeTermsVersion)($termsVersion);

            return JsonResponse::create(null, Response::HTTP_OK);
        } catch (EnabledVersionCannotBeDeletedException $e) {
            return JsonResponse::create($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
