<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\DriverFactory;
use App\Factory\GroupFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Crear grupos
        GroupFactory::createMany(3);

        // Crear conductores primero
        $drivers = DriverFactory::createMany(10, function () {
            return [
                'groupCollection' => GroupFactory::randomRange(1, 1),
            ];
        });

        // Crear el usuario admin
        $adminUser = UserFactory::createOne([
            'username' => 'admin',
            'password' => $this->passwordHasher->hashPassword(new User(), 'admin'),
            'isAdmin' => true,
        ]);

        // Crear usuarios dependientes de los nombres de los conductores
        foreach ($drivers as $driver) {
            $firstName = $driver->getName();
            $lastName = $driver->getLastName();
            $user = new User();
            $username = $user->generateUsername($firstName, $lastName);

            UserFactory::createOne([
                'username' => $username,
                'password' => $this->passwordHasher->hashPassword(new User(), 'password'),
                'isDriver' => true,
                'isAdmin' => false,
                'driver' => $driver,
            ]);
        }

        // Persistir todas las entidades creadas
        $manager->flush();
    }
}
