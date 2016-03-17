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
     * @var int
     */
    protected $days = 0;

    /**
     * @var Project[]|Collection
     */
    protected $projects;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

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

        if (!$request->get('month')) {
            $month = (int)$now->format('m');
        } else {
            $month = (int)$request->get('month');
        }
        if (!$request->get('year')) {
            $year = (int)$now->format('Y');
        } else {
            $year = (int)$request->get('year');
        }
        $dateFromRequest = new DateTime();
        $dateFromRequest->setDate($year, $month, 1);

        if ($dateFromRequest > $now) {
            $year = (int)$now->format('Y');
            $month = (int)$now->format('m');
        }
        $this->period = Period::createFromMonth($year, $month);

        $this->projects = $this
            ->projectRepository
            ->findForGeorge($this->getUser());
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
                        $workedDay = new WorkedDay();
                        $workedDay->setDate($day);
                        $workedDay->setProfile($profile);
                        $profile->addWorkedDay($workedDay);

                        // save new worked day
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
     * @return Period
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @return int
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
        $date = $this
            ->period
            ->getStartDate()
            ->sub(DateInterval::createFromDateString('1 month'));

        return $this
            ->router
            ->generate('app_reporting', [
                'year' => $date->format('Y'),
                'month' => $date->format('m')
            ]);
    }

    /**
     * @return string
     */
    public function getNextLink()
    {
        $date = $this
            ->period
            ->getStartDate()
            ->add(DateInterval::createFromDateString('1 month'));

        if ($date > new DateTime()) {
            return null;
        }

        return $this
            ->router
            ->generate('app_reporting', [
                'year' => $date->format('Y'),
                'month' => $date->format('m')
            ]);
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
}
