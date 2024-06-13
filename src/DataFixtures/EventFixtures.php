<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class EventFixtures extends Fixture implements DependentFixtureInterface
{

    private const COUNT = 20;

    private Generator $faker;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        for ($i = 0; $i < self::COUNT; $i++) {
            $event = $this->createEvent();
            $manager->persist($event);
        }
        $manager->flush();
    }

    private function createEvent(): Event
    {
        $webmaster = $this->getReference(UserFixtures::WEBMASTER_USER_REFERENCE);
        $event = new Event();
        $event->setName($this->faker->sentences(1, true));
        $event->setDescription($this->faker->paragraphs(3, true));
        $event->setDate($this->faker->dateTimeBetween('now', '+1 year'));
        $event->setPublic($this->faker->boolean(80));
        $event->setCreatedBy($webmaster);
        return $event;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

}