<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

/**
 * Load yml fixtures from alice bundle
 */
class FixturesLoader implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        Fixtures::load([
            __DIR__ . '/Customer.yml',
            __DIR__ . '/George.yml',
            __DIR__ . '/Project.yml',
            __DIR__ . '/GeorgeProfile.yml',
            __DIR__ . '/WorkedDay.yml',
        ], $manager, [
            'locale' => 'fr_FR'
        ]);
    }
}
