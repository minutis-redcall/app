<?php

namespace App\Controller\Api\Admin;

use App\Entity\Badge;
use App\Facade\Admin\Badge\BadgeFacade;
use App\Facade\Admin\Badge\BadgeFiltersFacade;
use App\Facade\Admin\Badge\BadgeReadFacade;
use App\Manager\BadgeManager;
use App\Transformer\Admin\BadgeTransformer;
use Bundles\ApiBundle\Annotation\Endpoint;
use Bundles\ApiBundle\Annotation\Facade;
use Bundles\ApiBundle\Base\BaseController;
use Bundles\ApiBundle\Contracts\FacadeInterface;
use Bundles\ApiBundle\Model\Facade\Http\HttpCreatedFacade;
use Bundles\ApiBundle\Model\Facade\Http\HttpNoContentFacade;
use Bundles\ApiBundle\Model\Facade\QueryBuilderFacade;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Badges can be skills, nominations, trainings, or whatever
 * information used to categorize volunteers.
 *
 * During a trigger, a RedCall user can either select all the
 * volunteers, or filter out a list of people having the required
 * badges.
 *
 * @Route("/api/admin/badge", name="api_admin_badge_")
 * @IsGranted("ROLE_ADMIN")
 */
class BadgeController extends BaseController
{
    /**
     * @var BadgeManager
     */
    private $badgeManager;

    /**
     * @var BadgeTransformer
     */
    private $badgeTransformer;

    public function __construct(BadgeManager $badgeManager, BadgeTransformer $badgeTransformer)
    {
        $this->badgeManager     = $badgeManager;
        $this->badgeTransformer = $badgeTransformer;
    }

    /**
     * List all badges.
     *
     * @Endpoint(
     *   priority = 20,
     *   request  = @Facade(class     = BadgeFiltersFacade::class),
     *   response = @Facade(class     = QueryBuilderFacade::class,
     *                      decorates = @Facade(class = BadgeFacade::class))
     * )
     * @Route(name="records", methods={"GET"})
     */
    public function records(BadgeFiltersFacade $filters) : FacadeInterface
    {
        $qb = $this->badgeManager->getSearchInBadgesQueryBuilder(
            $this->getPlatform(),
            $filters->getCriteria(),
            true
        );

        return new QueryBuilderFacade($qb, $filters->getPage(), function (Badge $badge) {
            return $this->badgeTransformer->expose($badge);
        });
    }

    /**
     * Create a new badge category.
     *
     * @Endpoint(
     *   priority = 21,
     *   request  = @Facade(class     = BadgeFacade::class),
     *   response = @Facade(class     = HttpCreatedFacade::class)
     * )
     * @Route(name="create", methods={"POST"})
     */
    public function create(BadgeFacade $facade) : FacadeInterface
    {
        $badge = $this->badgeTransformer->reconstruct($facade);

        $this->validate($badge, [
            new UniqueEntity(['platform', 'externalId']),
        ], ['create']);

        $this->badgeManager->save($badge);

        return new HttpCreatedFacade();
    }

    /**
     * Get a badge.
     *
     * @Endpoint(
     *   priority = 22,
     *   response = @Facade(class = BadgeReadFacade::class)
     * )
     * @Route(name="read", path="/{badgeId}", methods={"GET"})
     * @Entity("badge", expr="repository.findOneByExternalIdAndCurrentPlatform(badgeId)")
     * @IsGranted("BADGE", subject="badge")
     */
    public function read(Badge $badge) : FacadeInterface
    {
        return $this->badgeTransformer->expose($badge);
    }

    /**
     * Update a badge.
     *
     * @Endpoint(
     *   priority = 23,
     *   request  = @Facade(class = BadgeFacade::class),
     *   response = @Facade(class = HttpNoContentFacade::class)
     * )
     * @Route(name="update", path="/{badgeId}", methods={"PUT"})
     * @Entity("badge", expr="repository.findOneByExternalIdAndCurrentPlatform(badgeId)")
     * @IsGranted("BADGE", subject="badge")
     */
    public function update(Badge $badge, BadgeFacade $facade)
    {
        $badge = $this->badgeTransformer->reconstruct($facade, $badge);

        $this->validate($badge, [
            new UniqueEntity(['platform', 'externalId']),
            new Callback(function ($object, ExecutionContextInterface $context, $payload) {
                /** @var Badge $object */
                if ($object->isLocked()) {
                    $context->addViolation('This badge is locked.');
                }
            }),
        ]);

        $this->badgeManager->save($badge);

        return new HttpNoContentFacade();
    }

    public function delete()
    {

    }

    public function volunteerRecords()
    {

    }

    public function volunteerAdd()
    {

    }

    public function volunteerDelete()
    {

    }

    public function lock()
    {

    }

    public function unlock()
    {

    }

    public function enable()
    {

    }

    public function disable()
    {

    }
}