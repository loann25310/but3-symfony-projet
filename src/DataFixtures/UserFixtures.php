<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class UserFixtures extends Fixture
{

    public const WEBMASTER_USER_REFERENCE = 'webmaster-user';

    private const COUNT = 20;

    private Generator $faker;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        $users = [];
        for ($i = 0; $i < self::COUNT; $i++) {
            $user = $this->createUser();
            $manager->persist($user);
            $users[] = $user;
        }

        // Assign the first user as the webmaster
        $this->addReference(self::WEBMASTER_USER_REFERENCE, $users[0]);
        $users[0]->setRoles(['ROLE_WEBMASTER']);

        $manager->flush();
    }

    private function createUser(): User
    {
        $user = new User();
        $user->setEmail($this->faker->email);
        $user->setPassword($user->getEmail());
        $user->hashPassword();
        $user->setFirstname($this->faker->firstName);
        $user->setLastname($this->faker->lastName);
        return $user;
    }
}