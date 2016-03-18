<?php

namespace AppBundle\Form\Handler;

use AppBundle\Entity\GeorgeProfile;
use AppBundle\Entity\WorkedDay;
use AppBundle\Repository\GeorgeProfileRepository;
use AppBundle\Repository\WorkedDaysRepository;
use League\Period\Period;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AddGeorgeProfileHandler
{
    /**
     * @var GeorgeProfileRepository
     */
    private $georgeProfileRepository;

    /**
     * @var WorkedDaysRepository
     */
    private $workedDaysRepository;

    /**
     * AddGeorgeProfileHandler constructor
     *
     * @param GeorgeProfileRepository $georgeProfileRepository
     * @param WorkedDaysRepository $workedDaysRepository
     */
    public function __construct(
        GeorgeProfileRepository $georgeProfileRepository,
        WorkedDaysRepository $workedDaysRepository
    ) {
        $this->georgeProfileRepository = $georgeProfileRepository;
        $this->workedDaysRepository = $workedDaysRepository;
    }

    /**
     * @param FormInterface $form
     * @param UserInterface $user
     */
    public function handle(FormInterface $form, UserInterface $user)
    {
        $data = $form->getData();
        $profileId = $data['profileId'];
        /** @var GeorgeProfile $profile */
        $profile = $this
            ->georgeProfileRepository
            ->find($profileId);

        $profile->setGeorge($user);

        $this
            ->georgeProfileRepository
            ->save($profile);

        $period = Period::createFromMonth($data['year'], $data['month']);

        foreach ($period->getDatePeriod('1 DAY') as $day) {
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
    }
}
