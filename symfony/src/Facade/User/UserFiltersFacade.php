<?php

namespace App\Facade\User;

use App\Facade\PageFilterFacade;
use Bundles\ApiBundle\Annotation\Facade;
use Bundles\ApiBundle\Contracts\FacadeInterface;

class UserFiltersFacade extends PageFilterFacade
{
    /**
     * An optional search criteria in order to seek for a user by email,
     * attached volunteer name, or volunteer's external id.
     *
     * @var string|null
     */
    protected $criteria;

    /**
     * An optional criteria that let you choose whether search results
     * should only contain users that are admin.
     *
     * @var bool
     */
    protected $onlyAdmins = false;

    /**
     * An optional criteria that let you choose whether search results
     * should only contain users that are developers.
     *
     * @var bool
     */
    protected $onlyDevelopers = false;

    static public function getExample(Facade $decorates = null) : FacadeInterface
    {
        /** @var self $facade */
        $facade = parent::getExample($decorates);

        $facade->criteria = 'someone@example.com';

        return $facade;
    }

    public function getCriteria() : ?string
    {
        return $this->criteria;
    }

    public function setCriteria(?string $criteria) : self
    {
        $this->criteria = $criteria;

        return $this;
    }

    public function isOnlyAdmins() : bool
    {
        return $this->onlyAdmins;
    }

    public function setOnlyAdmins(bool $onlyAdmins) : UserFiltersFacade
    {
        $this->onlyAdmins = $onlyAdmins;

        return $this;
    }

    public function isOnlyDevelopers() : bool
    {
        return $this->onlyDevelopers;
    }

    public function setOnlyDevelopers(bool $onlyDevelopers) : UserFiltersFacade
    {
        $this->onlyDevelopers = $onlyDevelopers;

        return $this;
    }
}