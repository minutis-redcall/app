<?php

namespace App\Facade\Resource;

use Bundles\ApiBundle\Annotation as Api;
use Bundles\ApiBundle\Contracts\FacadeInterface;

abstract class ResourceFacade implements FacadeInterface
{
    const TYPE_VOLUNTEER = 'volunteer';
    const TYPE_STRUCTURE = 'structure';
    const TYPE_BADGE     = 'badge';
    const TYPE_CATEGORY  = 'category';
    const TYPE_USER      = 'user';

    /**
     * Resource type ({USER}, {VOLUNTEER}, {STRUCTURE}, {BADGE}, {CATEGORY})
     *
     * @Api\Placeholder("{USER}", replaceBy=ResourceFacade::TYPE_USER)
     * @Api\Placeholder("{VOLUNTEER}", replaceBy=ResourceFacade::TYPE_VOLUNTEER)
     * @Api\Placeholder("{STRUCTURE}", replaceBy=ResourceFacade::TYPE_STRUCTURE)
     * @Api\Placeholder("{BADGE}", replaceBy=ResourceFacade::TYPE_BADGE)
     * @Api\Placeholder("{CATEGORY}", replaceBy=ResourceFacade::TYPE_CATEGORY)
     *
     * @var string
     */
    protected $type;

    /**
     * Resource's external id
     *
     * @var string
     */
    protected $externalId;

    /**
     * Human-readable resource name
     *
     * @var string
     */
    protected $label;

    public function getType() : string
    {
        return $this->type;
    }

    public function getExternalId() : string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId) : ResourceFacade
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getLabel() : string
    {
        return $this->label;
    }

    public function setLabel(string $label) : ResourceFacade
    {
        $this->label = $label;

        return $this;
    }
}