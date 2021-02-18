<?php

namespace App\Facade\Admin\Category;

use Bundles\ApiBundle\Annotation\Facade;
use Bundles\ApiBundle\Contracts\FacadeInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryFacade implements FacadeInterface
{
    /**
     * An unique identifier for the category.
     * A random UUID or the same identifier as in your own application.
     *
     * @Assert\NotBlank
     * @Assert\Length(max = 64)
     *
     * @var string
     */
    private $externalId;

    /**
     * A name for the given category.
     * Name should be human readable as it may be used in user interfaces.
     *
     * @Assert\NotBlank
     * @Assert\Length(max = 64)
     *
     * @var string
     */
    private $name;

    /**
     * The category rendering priority.
     * You may want to see "First Aid" category before "Vehicles" in the audience selection form,
     * or when listing volunteers badge.
     *
     * @Assert\Range(min = 0, max = 1000)
     *
     * @var int
     */
    private $priority = 500;

    static public function getExample(Facade $decorates = null) : FacadeInterface
    {
        $facade = new self;

        $facade->externalId = Uuid::uuid4();
        $facade->name       = 'Vehicles';
        $facade->priority   = 20;

        return $facade;
    }

    public function getExternalId() : string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId) : CategoryFacade
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : CategoryFacade
    {
        $this->name = $name;

        return $this;
    }

    public function getPriority() : int
    {
        return $this->priority;
    }

    public function setPriority(int $priority) : CategoryFacade
    {
        $this->priority = $priority;

        return $this;
    }
}