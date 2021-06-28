<?php

namespace App\Facade\User;

use App\Facade\Resource\StructureResourceFacade;
use App\Facade\Resource\VolunteerResourceFacade;
use App\Facade\Volunteer\VolunteerReadFacade;
use Bundles\ApiBundle\Annotation as Api;
use Bundles\ApiBundle\Contracts\FacadeInterface;

class UserReadFacade extends UserFacade
{
    /**
     * A user is a resource that only helps in the authentication and authorization process. In order to create
     * triggers, a user should be bound to its volunteer, containing all its context at the Red Cross.
     *
     * @var VolunteerResourceFacade
     */
    protected $volunteer = null;

    /**
     * Structures that a user can trigger, by default the same of its tied volunteer.
     *
     * @var StructureResourceFacade[]
     */
    protected $structures = [];

    public static function getExample(Api\Facade $decorates = null) : FacadeInterface
    {
        /** @var self $facade */
        $facade = new self;
        foreach (get_object_vars(parent::getExample($decorates)) as $property => $value) {
            $facade->{$property} = $value;
        }

        $volunteer = new VolunteerResourceFacade();
        $volunteer->setLabel('John Doe');
        $volunteer->setExternalId('demo-volunteer');
        $facade->setVolunteer($volunteer);

        $structureA = new StructureResourceFacade();
        $structureA->setLabel('UNITE LOCALE DE PARIS 1ER');
        $structureA->setExternalId('demo-structure-1');

        $structureB = new StructureResourceFacade();
        $structureB->setLabel('UNITE LOCALE DE PARIS 2EME');
        $structureB->setExternalId('demo-structure-1');

        $facade->setStructures([
            $structureA,
            $structureB,
        ]);

        return $facade;
    }

    public function getVolunteer() : ?VolunteerResourceFacade
    {
        return $this->volunteer;
    }

    public function setVolunteer(?VolunteerResourceFacade $volunteer) : UserFacade
    {
        $this->volunteer = $volunteer;

        return $this;
    }

    public function getStructures() : array
    {
        return $this->structures;
    }

    public function setStructures(array $structures) : UserFacade
    {
        $this->structures = $structures;

        return $this;
    }
}