<?php

namespace App\Controller;

use App\Base\BaseController;
use App\Entity\Badge;
use App\Entity\Volunteer;
use App\Form\Type\AudienceType;
use App\Manager\AudienceManager;
use App\Manager\BadgeManager;
use App\Manager\VolunteerManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="audience", name="audience_")
 */
class AudienceController extends BaseController
{
    /**
     * @var VolunteerManager
     */
    private $volunteerManager;

    /**
     * @var BadgeManager
     */
    private $badgeManager;

    /**
     * @var AudienceManager
     */
    private $audienceManager;

    public function __construct(VolunteerManager $volunteerManager,
        BadgeManager $badgeManager,
        AudienceManager $audienceManager)
    {
        $this->volunteerManager = $volunteerManager;
        $this->badgeManager     = $badgeManager;
        $this->audienceManager  = $audienceManager;
    }

    /**
     * @Route(path="/search-volunteer", name="search_volunteer")
     */
    public function searchVolunteer(Request $request)
    {
        $criteria = $request->get('keyword');

        if ($this->isGranted('ROLE_ADMIN')) {
            $volunteers = $this->volunteerManager->searchAll($criteria, 20);
        } else {
            $volunteers = $this->volunteerManager->searchForCurrentUser($criteria, 20);
        }

        $results = [];
        foreach ($volunteers as $volunteer) {
            /* @var Volunteer $volunteer */
            $results[] = $volunteer->toSearchResults();
        }

        return $this->json($results);
    }

    /**
     * @Route(path="/search-badge", name="search_badge")
     */
    public function searchBadge(Request $request)
    {
        $badges = $this->badgeManager->searchNonVisibleUsableBadge(
            $request->get('keyword'),
            20
        );

        $results = [];
        foreach ($badges as $badge) {
            /* @var Badge $badge */
            $results[] = $badge->toSearchResults();
        }

        return $this->json($results);
    }

    /**
     * @Route(path="/numbers", name="numbers")
     */
    public function numbers(Request $request)
    {
        $data = AudienceType::getAudienceFormData($request);

        $response = [];

        if ('false' !== $request->get('badge_counts', true)) {
            $badgeCounts = $this->audienceManager->extractBadgeCounts(
                $data,
                $this->badgeManager->getPublicBadges()
            );

            $response['badge_counts'] = $badgeCounts;
        }

        $classification = $this->renderView('audience/classification.html.twig', [
            'classification' => $this->audienceManager->classifyAudience($data),
        ]);

        $response['classification'] = $classification;

        return $this->json($response);
    }

    /**
     * @Route(path="/problems", name="problems")
     */
    public function problems(Request $request)
    {
        $data = AudienceType::getAudienceFormData($request);

        $classification = $this->audienceManager->classifyAudience($data);
        $classification->setReachable([]);

        $volunteers = $this->volunteerManager->getVolunteerList(
            call_user_func_array('array_merge', $classification->toArray())
        );

        return $this->render('audience/problems.html.twig', [
            'classification' => $classification,
            'volunteers'     => $volunteers,
        ]);
    }

    /**
     * @Route(path="/selection", name="selection")
     */
    public function selection(Request $request)
    {
        $data = AudienceType::getAudienceFormData($request);

        $classification = $this->audienceManager->classifyAudience($data);

        $volunteers = $this->volunteerManager->getVolunteerList(
            $classification->getReachable()
        );

        return $this->render('audience/selection.html.twig', [
            'volunteers' => $volunteers,
        ]);
    }
}