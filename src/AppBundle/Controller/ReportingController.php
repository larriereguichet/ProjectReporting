<?php

namespace AppBundle\Controller;

use AppBundle\Entity\WorkedDay;
use AppBundle\Form\Type\AddGeorgeProfileType;
use AppBundle\Form\Type\WorkedDayCollectionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\DateFormatter\IntlDateFormatter;

class ReportingController extends Controller
{
    /**
     * @Template()
     */
    public function homepageAction()
    {
        return [];
    }

    /**
     * @Template()
     *
     * @param Request $request
     * @return array
     */
    public function reportingAction(Request $request)
    {
        $helper = $this->get('lag.reporting.view_helper');
        $helper->handleRequest($request);

        $form = $this->createForm(WorkedDayCollectionType::class, [
            'days' => $helper->getDays()
        ]);
        $forms = [];

        $projects = $this
            ->get('lag.project_repository')
            ->findAll();
        $profilesForms = $this->createForm(AddGeorgeProfileType::class, [
            'year' => $helper->getYear(),
            'month' => $helper->getMonth()
        ], [
            'projects' => $projects,
        ]);

        /** @var FormInterface $child */
        foreach ($form->get('days') as $child) {
            /** @var WorkedDay $workedDay */
            $workedDay = $child->getData();

            // sort by george profile first
            $profileId = $workedDay
                ->getProfile()
                ->getId();

            // then sort by day (d: '01' for example)
            $day = $workedDay
                ->getDate()
                ->format('d');

            $forms[$profileId][$day] = $child->createView();
        }

        return [
            'period' => $helper->getPeriod(),
            'projects' => $helper->getProjects(),
            'form' => $form->createView(),
            'forms' => $forms,
            'previousLink' => $helper->getPreviousLink(),
            'nextLink' => $helper->getNextLink(),
            'profilesForms' => $profilesForms->createView()
        ];
    }

    /**
     * @Method({"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function saveReportingAjaxAction(Request $request)
    {
        $helper = $this->get('lag.reporting.view_helper');
        $helper->handleRequest($request);

        $form = $this->createForm(WorkedDayCollectionType::class, [
            'days' => $helper->getDays()
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // get worked days from form
            $days = $form->getData()['days'];
            $workedDayRepository = $this->get('lag.worked_day_repository');

            // save each worked day
            foreach ($days as $day) {
                $workedDayRepository->save($day);
            }
            $response = new JsonResponse();
        } else {
            $errors = [];

            foreach ($form->getErrors() as $error) {
                $errors[] = [
                    'message' => $error->getMessage(),
                    'cause' => $error->getCause(),
                    'origin' => $error->getOrigin()
                ];
            }
            $response = new JsonResponse([
                'errors' => (string)$form->getErrors(true, false)
            ], 500);
        }

        return $response;
    }

    public function addProfileAction(Request $request)
    {
        $projects = $this
            ->get('lag.project_repository')
            ->findAll();
        $form = $this->createForm(AddGeorgeProfileType::class, null, [
            'projects' => $projects
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('lag.form.add_george_profile_handler')->handle($form, $this->getUser());

            $this->addFlash('info', 'ok');
        }
        return $this->redirectToRoute('app_reporting');
    }

    public function removeProfileAction()
    {

    }
}
