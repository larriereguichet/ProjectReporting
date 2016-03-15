<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="lag_george_profile")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GeorgeProfileRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class GeorgeProfile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     * @var string
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\George", inversedBy="profiles")
     */
    protected $george;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project", inversedBy="profiles")
     */
    protected $project;

    /**
     * @ORM\Column(name="daily_rate", type="float", precision=2)
     */
    protected $dailyRate;

    /**
     * @ORM\Column(name="number_of_days", type="integer")
     */
    protected $numberOfDays;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\WorkedDay", mappedBy="profile")
     */
    protected $workedDays;

    /**
     * GeorgeProfile constructor.
     */
    public function __construct()
    {
        $this->workedDays = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return GeorgeProfile
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return GeorgeProfile
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGeorge()
    {
        return $this->george;
    }

    /**
     * @param mixed $george
     * @return GeorgeProfile
     */
    public function setGeorge($george)
    {
        $this->george = $george;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     * @return GeorgeProfile
     */
    public function setProject($project)
    {
        $this->project = $project;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDailyRate()
    {
        return $this->dailyRate;
    }

    /**
     * @param mixed $dailyRate
     * @return GeorgeProfile
     */
    public function setDailyRate($dailyRate)
    {
        $this->dailyRate = $dailyRate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNumberOfDays()
    {
        return $this->numberOfDays;
    }

    /**
     * @param mixed $numberOfDays
     * @return GeorgeProfile
     */
    public function setNumberOfDays($numberOfDays)
    {
        $this->numberOfDays = $numberOfDays;
        return $this;
    }

    /**
     * @return WorkedDay[]|Collection
     */
    public function getWorkedDays()
    {
        return $this->workedDays;
    }

    /**
     * @param mixed $workedDays
     * @return GeorgeProfile
     */
    public function setWorkedDays($workedDays)
    {
        $this->workedDays = $workedDays;
        return $this;
    }

    /**
     * @param WorkedDay $day
     */
    public function addWorkedDay(WorkedDay $day)
    {
        $this
            ->workedDays
            ->add($day);
    }
}
