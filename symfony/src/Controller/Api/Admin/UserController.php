<?php

namespace App\Controller\Api\Admin;

use App\Base\BaseController;
use App\Entity\User;
use App\Manager\UserManager;
use App\Transformer\UserTransformer;
use Bundles\ApiBundle\Contracts\FacadeInterface;
use Bundles\ApiBundle\Model\Facade\QueryBuilderFacade;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Bundles\ApiBundle\Annotation\Endpoint;
use App\Facade\User\UserFiltersFacade;
use App\Facade\User\UserReadFacade;
use Bundles\ApiBundle\Annotation\Facade;

/**
 * Users are the ones using the RedCall application to trigger
 * volunteers.
 *
 * They are associated with a list of structures they can trigger
 *
 * @Route("/api/admin/user", name="api_admin_user_")
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends BaseController
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var UserTransformer
     */
    private $userTransformer;

    public function __construct(UserManager $userManager, UserTransformer $userTransformer)
    {
        $this->userManager     = $userManager;
        $this->userTransformer = $userTransformer;
    }

    /**
     * List all users.
     *
     * @Endpoint(
     *   priority = 200,
     *   request  = @Facade(class     = UserFiltersFacade::class),
     *   response = @Facade(class     = QueryBuilderFacade::class,
     *                      decorates = @Facade(class = UserReadFacade::class))
     * )
     * @Route(name="records", methods={"GET"})
     */
    public function records(UserFiltersFacade $filters) : FacadeInterface
    {
        $qb = $this->userManager->searchQueryBuilder(
            $filters->getCriteria(),
            $filters->isOnlyAdmins(),
            $filters->isOnlyDevelopers()
        );

        return new QueryBuilderFacade($qb, $filters->getPage(), function (User $user) {
            return $this->userTransformer->expose($user);
        });
    }

}