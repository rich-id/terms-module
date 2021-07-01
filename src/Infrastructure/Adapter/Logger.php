<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Infrastructure\Adapter;

use Psr\Log\LoggerInterface as PsrLogger;
use RichId\TermsModuleBundle\Domain\Entity\TermsSubjectInterface;
use RichId\TermsModuleBundle\Domain\Port\LoggerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class Logger implements LoggerInterface
{
    /** @var PsrLogger */
    protected $logger;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var Security */
    protected $security;

    public function __construct(PsrLogger $logger, TranslatorInterface $translator, Security $security)
    {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->security = $security;
    }

    public function logTermsSigned(string $termsSlug, TermsSubjectInterface $subject, ?bool $accepted): void
    {
        $user = $this->security->getUser();
        $userUsername = $user !== null ? $user->getUsername() : '';
        $choice = $this->getTermsSignedChoice($accepted);
        $now = new \DateTime();

        $this->logger->info(
            $this->translator->trans(
                'terms_module.log.terms_signed',
                [
                    '%terms_slug%' => $termsSlug,
                    '%choice%'     => $choice,
                    '%date%'       => $now->format('c'),
                    '%user%'       => $userUsername,
                ],
                'terms_module'
            ),
            [
                'extra' => [
                    '_event'  => 'terms_module.terms_signed',
                    '_terms'  => $termsSlug,
                    '_choice' => $choice,
                    '_user'   => $userUsername,
                ],
            ]
        );
    }

    private function getTermsSignedChoice(?bool $accepted): string
    {
        if ($accepted === null) {
            return $this->translator->trans('terms_module.sign.prefer_answer_later', [], 'terms_module');
        }

        if ($accepted) {
            return $this->translator->trans('terms_module.sign.accept', [], 'terms_module');
        }

        return $this->translator->trans('terms_module.sign.refuse', [], 'terms_module');
    }
}
