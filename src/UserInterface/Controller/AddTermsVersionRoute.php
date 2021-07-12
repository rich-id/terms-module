<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Exception\CannotAddVersionToTermsException;
use RichId\TermsModuleBundle\Domain\UseCase\CreateTermsVersion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AddTermsVersionRoute extends AbstractController
{
    use AdminRouteTrait;

    /** @var CreateTermsVersion */
    protected $createTermsVersion;

    /** @var array<string> */
    protected $adminRoles;

    public function __construct(CreateTermsVersion $createTermsVersion, ParameterBagInterface $parameterBag)
    {
        $this->createTermsVersion = $createTermsVersion;
        $this->adminRoles = $parameterBag->get('rich_id_terms_module.admin_roles');
    }

    /** @return array<string> */
    protected function getAdminRoles(): array
    {
        return $this->adminRoles;
    }

    public function __invoke(Terms $terms): Response
    {
        if (!$this->isGranted('MODULE_TERMS_ADMIN')) {
            throw $this->buildAccessDeniedException();
        }

        $lastVersion = $terms->getLatestVersion();

        if ($lastVersion === null) {
            throw new AccessDeniedException('The terms has no version.');
        }

        try {
            ($this->createTermsVersion)($lastVersion);

            return new JsonResponse(null, Response::HTTP_CREATED);
        } catch (CannotAddVersionToTermsException $e) {
            throw new AccessDeniedException($e->getMessage());
        }
    }
}
