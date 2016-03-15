<?php

namespace AppBundle\View\Helper;

use AppBundle\Entity\Project;
use AppBundle\Entity\WorkedDay;
use AppBundle\Repository\ProjectRepository;
use AppBundle\Repository\WorkedDaysRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use League\Period\Period;
use Symfony\Component\HttpFoundation\Request;

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
     * @var Period
     */
    protected $previousPeriod;

    /**
     * @var Period
     */
    protected $nextPeriod;

    /**
     * @var int
     */
    protected $days = 0;

    /**
     * @var Project[]|Collection
     */
    protected $projects;

    /**
     * ReportingHelper constructor.
     *
     * @param ProjectRepository $projectRepository
     * @param WorkedDaysRepository $workedDaysRepository
     */
    public function __construct(ProjectRepository $projectRepository, WorkedDaysRepository $workedDaysRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->workedDaysRepository = $workedDaysRepository;
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
        $this->period = Period::createFromMonth($year, $month);
        $this->previousPeriod = Period::createFromMonth($year, $month - 1)->getStartDate();
        $this->nextPeriod = Period::createFromMonth($year, $month + 1);

        $this->projects = $this
            ->projectRepository
            ->findAll();

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
     * @return Period
     */
    public function getPreviousPeriod()
    {
        return $this->previousPeriod;
    }

    /**
     * @return Period
     */
    public function getNextPeriod()
    {
        return $this->nextPeriod;
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
}
