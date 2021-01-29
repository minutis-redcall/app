<?php

namespace Bundles\PaginationBundle\Manager;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use RuntimeException;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationManager
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var int
     */
    private $perPage = 20;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getPerPage() : int
    {
        return $this->perPage;
    }

    public function setPerPage(int $perPage) : PaginationManager
    {
        $this->perPage = $perPage;

        return $this;
    }

    public function getPager($data, $prefix = '', $hasJoins = false) : Pagerfanta
    {
        $request = $this->requestStack->getMasterRequest();

        $adapter = null;
        if ($data instanceof QueryBuilder) {
            $adapter = new QueryAdapter($data, $hasJoins);
        } elseif (is_array($data)) {
            $adapter = new ArrayAdapter($data);
        } else {
            throw new RuntimeException('This data type has no Pagerfanta adapter yet.');
        }

        $pager = new Pagerfanta($adapter);
        $pager->setNormalizeOutOfRangePages(true);

        $pager->setMaxPerPage($this->perPage);
        $pager->setCurrentPage($request->request->get($prefix.'page') ?: $request->query->get($prefix.'page', 1));

        return $pager;
    }
}