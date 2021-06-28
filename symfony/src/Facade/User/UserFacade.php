<?php

namespace App\Facade\User;

use App\Facade\Generic\ResourceFacade;
use App\Facade\Resource\StructureResourceFacade;
use App\Facade\Resource\VolunteerResourceFacade;
use Bundles\ApiBundle\Annotation as Api;
use Bundles\ApiBundle\Contracts\FacadeInterface;

class UserFacade implements FacadeInterface
{
    /**
     * User's identifier, generally this is the email (s)he used to sign-up to the platform. When using
     * external connectors, it may also be the email tied to the external resource (eg. a Red Cross volunteer).
     *
     * @var string
     */
    protected $identifier;

    /**
     * When registering, every user receive a verification email. User''s email is considered valid once user clicked
     * on the link it contains. Non-verified users cannot connect to the platform.
     *
     * @var bool
     */
    protected $verified = true;

    /**
     * Anyone can subscribe to the platform, but only the ones trusted (activated manually by an administrator)
     * can access the provided tools.
     *
     * @var bool
     */
    protected $trusted = true;

    /**
     * A developer can integrate RedCall APIs and access technical features.
     *
     * @var bool
     */
    protected $developer = false;

    /**
     * An administrator can trust new users and configure the platform.
     *
     * @var bool
     */
    protected $administrator = false;

    /**
     * A root has the same capabilities as an administrator, but can switch between the different platforms
     * (eg. France, Spain, ...), and can also change all resources' platform.
     *
     * @var bool
     */
    protected $root = false;

    /**
     * A locked user is not synchronized anymore with its associated volunteer. If volunteer's scope change
     * it won't be reflected.
     *
     * @var bool
     */
    protected $locked = false;

    static public function getExample(Api\Facade $decorates = null) : FacadeInterface
    {
        $facade = new self;

        $facade->setIdentifier('john.doe@example.org');
        $facade->setVerified(true);
        $facade->setTrusted(true);
        $facade->setDeveloper(false);
        $facade->setAdministrator(false);
        $facade->setRoot(false);
        $facade->setLocked(false);

        return $facade;
    }

    public function getIdentifier() : string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier) : UserFacade
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function isVerified() : bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified) : UserFacade
    {
        $this->verified = $verified;

        return $this;
    }

    public function isTrusted() : bool
    {
        return $this->trusted;
    }

    public function setTrusted(bool $trusted) : UserFacade
    {
        $this->trusted = $trusted;

        return $this;
    }

    public function isDeveloper() : bool
    {
        return $this->developer;
    }

    public function setDeveloper(bool $developer) : UserFacade
    {
        $this->developer = $developer;

        return $this;
    }

    public function isAdministrator() : bool
    {
        return $this->administrator;
    }

    public function setAdministrator(bool $administrator) : UserFacade
    {
        $this->administrator = $administrator;

        return $this;
    }

    public function isRoot() : bool
    {
        return $this->root;
    }

    public function setRoot(bool $root) : UserFacade
    {
        $this->root = $root;

        return $this;
    }

    public function isLocked() : bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked) : UserFacade
    {
        $this->locked = $locked;

        return $this;
    }
}