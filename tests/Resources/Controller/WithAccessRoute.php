<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Tests\Resources\Controller;

use RichId\TermsModuleBundle\Domain\Exception\SubjectNeedToSignTermsException;
use RichId\TermsModuleBundle\Domain\Model\DummySubject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class WithAccessRoute extends AbstractController
{
    public function __invoke(): void
    {
        $subject = DummySubject::create('user', '42');

        throw new SubjectNeedToSignTermsException('terms-1', $subject);
    }
}
