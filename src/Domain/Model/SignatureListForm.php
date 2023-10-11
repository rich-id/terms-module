<?php

declare(strict_types=1);

namespace RichId\TermsModuleBundle\Domain\Model;

use RichId\TermsModuleBundle\Domain\Entity\Terms;

class SignatureListForm
{
    public const SORT_SIGNATORY = 'signatory';
    public const SORT_DATE = 'date';

    public const SORT_ASC = 'asc';
    public const SORT_DESC = 'desc';

    /** @var string|null */
    private $search;

    /** @var Terms|null */
    private $terms;

    /** @var int */
    private $page = 1;

    /** @var int */
    private $numberItemsPerPage = 10;

    /** @var string */
    private $sort = self::SORT_SIGNATORY;

    /** @var string */
    private $sortDirection = self::SORT_DESC;

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(?string $search): self
    {
        $this->search = $search;

        return $this;
    }

    public function getTerms(): ?Terms
    {
        return $this->terms;
    }

    public function setTerms(?Terms $terms): self
    {
        $this->terms = $terms;

        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function getNumberItemsPerPage(): int
    {
        return $this->numberItemsPerPage;
    }

    public function setNumberItemsPerPage(int $numberItemsPerPage): self
    {
        $this->numberItemsPerPage = $numberItemsPerPage;

        return $this;
    }

    public function getSort(): string
    {
        return $this->sort;
    }

    public function setSort(string $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getSortDirection(): string
    {
        return $this->sortDirection;
    }

    public function setSortDirection(string $sortDirection): self
    {
        $this->sortDirection = $sortDirection;

        return $this;
    }
}
