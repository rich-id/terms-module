<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\EnabledVersionCannotBeDeletedException;
use RichId\TermsModuleBundle\Domain\Exception\FirstVersionCannotBeDeletedException;
use RichId\TermsModuleBundle\Domain\UseCase\RemoveTermsVersion;
use RichId\TermsModuleBundle\Infrastructure\SecurityVoter\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RemoveTermsVersionRoute extends AbstractController
{
    use AdminRouteTrait;

    /** @var RemoveTermsVersion */
    protected $removeTermsVersion;

    /** @var ParameterBagInterface */
    protected $parameterBag;

    public function __construct(RemoveTermsVersion $removeTermsVersion, ParameterBagInterface $parameterBag)
    {
        $this->removeTermsVersion = $removeTermsVersion;
        $this->parameterBag = $parameterBag;
    }

    public function __invoke(TermsVersion $termsVersion): Response
    {
        if (!$this->isGranted(UserVoter::MODULE_TERMS_ADMIN)) {
            throw $this->buildAccessDeniedException();
        }

        try {
            ($this->removeTermsVersion)($termsVersion);

            return JsonResponse::create(null, Response::HTTP_OK);
        } catch (EnabledVersionCannotBeDeletedException | FirstVersionCannotBeDeletedException $e) {
            return JsonResponse::create($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /** @return string[] */
    protected function getAdminRoles(): array
    {
        return $this->parameterBag->get('rich_id_terms_module.admin_roles');
    }
}
