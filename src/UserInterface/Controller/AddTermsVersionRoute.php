<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Domain\Entity\Terms;
use RichId\TermsModuleBundle\Domain\Entity\TermsVersion;
use RichId\TermsModuleBundle\Domain\Exception\CannotAddVersionToTermsException;
use RichId\TermsModuleBundle\Domain\UseCase\CreateTermsVersion;
use RichId\TermsModuleBundle\Infrastructure\Repository\TermsVersionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AddTermsVersionRoute extends AbstractController
{
    /** @var CreateTermsVersion */
    protected $createTermsVersion;

    /** @var TermsVersionRepository */
    protected $termsVersionRepository;

    /** @var RequestStack */
    protected $requestStack;

    public function __construct(
        CreateTermsVersion $createTermsVersion,
        TermsVersionRepository $termsVersionRepository,
        RequestStack $requestStack
    ) {
        $this->createTermsVersion = $createTermsVersion;
        $this->termsVersionRepository = $termsVersionRepository;
        $this->requestStack = $requestStack;
    }

    #[IsGranted('MODULE_TERMS_ADMIN')]
    public function __invoke(Terms $terms): Response
    {
        $currentVersion = $this->getTermsVersion($terms);

        if ($currentVersion === null) {
            return new JsonResponse('The terms has no version.', Response::HTTP_UNAUTHORIZED);
        }

        try {
            ($this->createTermsVersion)($currentVersion);

            return new JsonResponse(null, Response::HTTP_CREATED);
        } catch (CannotAddVersionToTermsException $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
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
