<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\WorkedDay;
use AppBundle\Form\Type\WorkedDayCollectionType;
use DateTime;
use League\Period\Period;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ReportingController extends Controller
{
    /**
     * @Template()
     * @param Request $request
     * @return array
     */
    public function reportingAction(Request $request)
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
        var_dump($year);
        var_dump($month);
        $period = Period::createFromMonth($year, $month);
        $previousPeriod = Period::createFromMonth($year, $month - 1)->getStartDate();
        $nextPeriod = Period::createFromMonth($year, $month + 1);

        $projects = $this
            ->get('lag.project_repository')
            ->findAll();

        $days = [
        ];

        /** @var Project $project */
        foreach ($projects as $project) {

            foreach ($project->getProfiles() as $profile) {


                /** @var DateTime $day */
                foreach ($period->getDatePeriod('1 DAY') as $day) {

                    $workedDay = new WorkedDay();
                    $workedDay->setDate($day);
                    $workedDay->setProfile($profile);

                    $profile->addWorkedDay($workedDay);

                    $days[] = $workedDay;



                    //$forms[$profile->getId()] [$day->format('d')] ;
                }

            }


        }


        $form = $this->createForm(WorkedDayCollectionType::class, [
            'days' => $days
        ]);

        var_dump($form->get('days'));
        die;

        foreach ($form->get('days') as $child) {
        }



        return [
            'period' => $period,
            'previousPeriod' => $previousPeriod,
            'nextPrevious' => $nextPeriod,
            'projects' => $projects,
            'forms' => $forms->createView()
        ];
    }
}
