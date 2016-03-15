<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="lag_worked_days")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WorkedDaysRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class WorkedDay
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(name="date", type="datetime")
     * @var DateTime
     */
    protected $date;

    /**
     * @ORM\Column(name="duration", type="float", precision=2)
     */
    protected $duration;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\GeorgeProfile", inversedBy="workedDays")
     */
    protected $profile;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return WorkedDay
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     * @return WorkedDay
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param mixed $duration
     * @return WorkedDay
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param mixed $profile
     * @return WorkedDay
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
        return $this;
    }
}
