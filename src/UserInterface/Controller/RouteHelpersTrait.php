<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\UserInterface\Controller;

use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait RouteHelpersTrait
{
    protected function getSubject(Request $request): TermsSubjectInterface
    {
        $subjectType = $request->query->get('type');
        $subjectIdentifier = $request->query->get('identifier');

        if (!\is_string($subjectType) || $subjectType === '') {
            throw new BadRequestHttpException('Query parameter type is missing.');
        }

        if (!\is_string($subjectIdentifier) || $subjectIdentifier === '') {
            throw new BadRequestHttpException('Query parameter identifier is missing.');
        }

        return DummySubject::create($subjectType, $subjectIdentifier);
    }
}
