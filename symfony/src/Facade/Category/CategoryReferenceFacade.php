<?php

namespace App\Facade\Category;

use Bundles\ApiBundle\Annotation as Api;
use Bundles\ApiBundle\Contracts\FacadeInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryReferenceFacade implements FacadeInterface
{
    /**
     * The identifier you've set to identify a resource.
     *
     * @Assert\NotBlank()
     * @Assert\Length(max = 64)
     *
     * @var string
     */
    private $externalId;

    static public function getExample(Api\Facade $decorates = null) : FacadeInterface
    {
        $facade = new static;

        $facade->externalId = sprintf('demo-%d', rand() % 100);

        return $facade;
    }

    public function getExternalId() : string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId) : CategoryReferenceFacade
    {
        $this->externalId = $externalId;

        return $this;
    }
}