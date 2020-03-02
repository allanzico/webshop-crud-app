<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends BaseFixture
{


    protected function loadData(ObjectManager $entityManager)
    {
        $this->createMany(10, 'main_users', function ($i){
            $user = new User();
            $user->setEmail(sprintf('allan%d@example.com', $i));
            $user->setName($this->faker->name);

            return $user;
        });

        $entityManager->flush();
    }
}
