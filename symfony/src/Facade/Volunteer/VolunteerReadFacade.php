<?php

namespace App\Facade\Volunteer;

use App\Facade\Phone\PhoneFacade;
use App\Facade\Phone\PhoneReadFacade;
use App\Facade\Resource\BadgeResourceFacade;
use App\Facade\Resource\StructureResourceFacade;
use App\Facade\Resource\UserResourceFacade;
use App\Facade\User\UserFacade;
use App\Facade\User\UserReadFacade;
use Bundles\ApiBundle\Annotation\Facade;
use Bundles\ApiBundle\Contracts\FacadeInterface;
use Bundles\ApiBundle\Model\Facade\CollectionFacade;

class VolunteerReadFacade extends VolunteerFacade
{
    /**
     * Structures from which volunteer can be triggered
     *
     * @var StructureResourceFacade[]
     */
    protected $structures;

    /**
     * Volunteer phone numbers
     *
     * @var PhoneReadFacade[]
     */
    protected $phones;

    /**
     * Volunteer badges
     *
     * @var BadgeResourceFacade[]
     */
    protected $badges;

    /**
     * A RedCall user resource if the volunteer can trigger people.
     *
     * @var UserResourceFacade|null
     */
    protected $user;

    public function __construct()
    {
        $this->structures = new CollectionFacade();
        $this->phones     = new CollectionFacade();
        $this->badges     = new CollectionFacade();
    }

    static public function getExample(Facade $decorates = null) : FacadeInterface
    {
        /** @var self $facade */
        $facade = new self;
        foreach (get_object_vars(parent::getExample($decorates)) as $property => $value) {
            $facade->{$property} = $value;
        }

        $facade->addStructure(StructureResourceFacade::getExample());
        $facade->addPhone(PhoneReadFacade::getExample());
        $facade->addBadge(BadgeResourceFacade::getExample());

        return $facade;
    }

    public function getStructures() : CollectionFacade
    {
        return $this->structures;
    }

    public function addStructure(StructureResourceFacade $facade)
    {
        $this->structures[] = $facade;
    }

    public function getPhones() : CollectionFacade
    {
        return $this->phones;
    }

    public function addPhone(PhoneFacade $facade)
    {
        $this->phones[] = $facade;
    }

    public function getBadges() : CollectionFacade
    {
        return $this->badges;
    }

    public function addBadge(BadgeResourceFacade $badge)
    {
        $this->badges[] = $badge;

        return $this;
    }

    public function getUser() : ?UserResourceFacade
    {
        return $this->user;
    }

    public function setUser(?UserResourceFacade $user) : VolunteerReadFacade
    {
        $this->user = $user;

        return $this;
    }
}