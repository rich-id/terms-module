<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\CannotAddVersionToTermsException;
use RichId\TermsModuleBundle\Domain\UseCase\CreateTermsVersion;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionRepository;
use RichId\TermsModuleBundle\Infrastructure\SecurityVoter\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AddTermsVersionRoute extends AbstractController
{
    use AdminRouteTrait;

    /** @var CreateTermsVersion */
    protected $createTermsVersion;

    /** @var TermsVersionRepository */
    protected $termsVersionRepository;

    /** @var RequestStack */
    protected $requestStack;

    /** @var ParameterBagInterface */
    protected $parameterBag;

    public function __construct(
        CreateTermsVersion $createTermsVersion,
        TermsVersionRepository $termsVersionRepository,
        ParameterBagInterface $parameterBag,
        RequestStack $requestStack
    ) {
        $this->createTermsVersion = $createTermsVersion;
        $this->termsVersionRepository = $termsVersionRepository;
        $this->requestStack = $requestStack;
        $this->parameterBag = $parameterBag;
    }

    public function __invoke(Terms $terms): Response
    {
        if (!$this->isGranted(UserVoter::MODULE_TERMS_ADMIN)) {
            throw $this->buildAccessDeniedException();
        }

        $currentVersion = $this->getTermsVersion($terms);

        if ($currentVersion === null) {
            return JsonResponse::create('The terms has no version.', Response::HTTP_UNAUTHORIZED);
        }

        try {
            ($this->createTermsVersion)($currentVersion);

            return JsonResponse::create(null, Response::HTTP_CREATED);
        } catch (CannotAddVersionToTermsException $e) {
            return JsonResponse::create($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /** @return string[] */
    protected function getAdminRoles(): array
    {
        return $this->parameterBag->get('rich_id_terms_module.admin_roles');
    }

    private function getTermsVersion(Terms $terms): ?TermsVersion
    {
        $request = $this->requestStack->getCurrentRequest() ?? new Request();
        $version = $request->query->get('version');

        if ($version !== null && $version !== '') {
            $termsVersion = $this->termsVersionRepository->findOneByTermsAndVersion(
                $terms->getSlug() ?? '',
                (int) $version
            );

            if ($termsVersion !== null) {
                return $termsVersion;
            }

            throw new NotFoundHttpException(\sprintf('No terms version found with version %s', $version));
        }

        return $terms->getLatestVersion();
    }
}
