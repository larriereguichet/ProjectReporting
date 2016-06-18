<?php

namespace AppBundle\View\Helper;

use AppBundle\Entity\George;
use AppBundle\Entity\Project;
use AppBundle\Entity\WorkedDay;
use AppBundle\Repository\ProjectRepository;
use AppBundle\Repository\WorkedDaysRepository;
use DateInterval;
use DateTime;
use Doctrine\Common\Collections\Collection;
use League\Period\Period;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ReportingHelper
{
    /**
     * @var ProjectRepository
     */
    protected $projectRepository;

    /**
     * @var WorkedDaysRepository
     */
    protected $workedDaysRepository;

    /**
     * @var Period
     */
    protected $period;

    /**
     * @var WorkedDay[]
     */
    protected $days;

    /**
     * @var Project[]|Collection
     */
    protected $projects;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var DateTime
     */
    protected $previousDate;

    /**
     * @var DateTime
     */
    protected $nextDate;

    /**
     * @var string
     */
    protected $previousLink;

    /**
     * @var string
     */
    protected $nextLink;

    /**
     * ReportingHelper constructor.
     *
     * @param ProjectRepository $projectRepository
     * @param WorkedDaysRepository $workedDaysRepository
     * @param RouterInterface $router
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        ProjectRepository $projectRepository,
        WorkedDaysRepository $workedDaysRepository,
        RouterInterface $router,
        TokenStorageInterface $tokenStorage
    ) {
        $this->projectRepository = $projectRepository;
        $this->workedDaysRepository = $workedDaysRepository;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function handleRequest(Request $request)
    {
        $now = new DateTime();

        // get date from request parameter
        if (!$request->get('month')) {
            $month = (int) $now->format('m');
        } else {
            $month = (int) $request->get('month');
        }
        if (!$request->get('year')) {
            $year = (int) $now->format('Y');
        } else {
            $year = (int) $request->get('year');
        }
        $dateFromRequest = new DateTime();
        $dateFromRequest->setDate($year, $month, 1);

        if ($dateFromRequest > $now) {
            $year = (int) $now->format('Y');
            $month = (int) $now->format('m');
        }
        // create period from year and month
        $this->period = Period::createFromMonth($year, $month);

        // get projects for this user
        $this->projects = $this
            ->projectRepository
            ->findForGeorge($this->getUser());

        // organize worked days for the view
        $this->processWorkedDays();

        // organize links for the view
        $this->processLinks();
    }

    /**
     * @return Period
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @return WorkedDay[]
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @return Project[]|Collection
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * @return string
     */
    public function getPreviousLink()
    {
        return $this->previousLink;
    }

    /**
     * @return string
     */
    public function getNextLink()
    {
        return $this->nextLink;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return (int) $this
            ->period
            ->getStartDate()
            ->format('Y');
    }

    /**
     * @return int
     */
    public function getMonth()
    {
        return (int) $this
            ->period
            ->getStartDate()
            ->format('m');
    }

    /**
     * @return George
     */
    protected function getUser()
    {
        return $this
            ->tokenStorage
            ->getToken()
            ->getUser();
    }

    /**
     * Process worked days for the view.
     */
    protected function processWorkedDays()
    {
        $days = [];

        /** @var Project $project */
        foreach ($this->projects as $project) {

            foreach ($project->getProfiles() as $profile) {

                $workedDays = [];

                // sort worked days by date
                foreach ($profile->getWorkedDays() as $day) {

                    // we take only worked days within the current period
                    if ($this->period->contains($day->getDate())) {
                        $workedDays[$day->getDate()->getTimestamp()] = $day;
                    }
                }
                /** @var DateTime $day */
                foreach ($this->period->getDatePeriod('1 DAY') as $day) {

                    if (array_key_exists($day->getTimestamp(), $workedDays)) {
                        $workedDay = $workedDays[$day->getTimestamp()];
                    } else {
                        // fill missing working days in database
                        $workedDay = new WorkedDay();
                        $workedDay->setDate($day);
                        $workedDay->setProfile($profile);

                        // bind it to the profile
                        $profile->addWorkedDay($workedDay);

                        $this
                            ->workedDaysRepository
                            ->save($workedDay);
                    }
                    $days[] = $workedDay;
                }
            }
        }
        $this->days = $days;
    }

    /**
     * Define previous and next dates and links.
     */
    protected function processLinks()
    {
        // previous date and link
        $this->previousDate = $this
            ->period
            ->getStartDate()
            ->sub(DateInterval::createFromDateString('1 month'));
        $this->previousLink = $this
            ->router
            ->generate('app_reporting', [
                'year' => $this->previousDate->format('Y'),
                'month' => $this->previousDate->format('m'),
            ]);

        // next date and link
        $this->nextDate = $this
            ->period
            ->getStartDate()
            ->add(DateInterval::createFromDateString('1 month'));
        $this->nextLink = $this
            ->router
            ->generate('app_reporting', [
                'year' => $this->nextDate->format('Y'),
                'month' => $this->nextDate->format('m'),
            ]);
    }
}
