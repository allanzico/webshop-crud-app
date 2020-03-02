<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends BaseFixture
{

    /**
     * @var UserPasswordEncoder
     */
    private $userPasswordEncoder;

    /**
     * UserFixture constructor.
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    protected function loadData(ObjectManager $entityManager)
    {
        $this->createMany(10, 'main_users', function ($i){
            $user = new User();
            $user->setEmail(sprintf('allan%d@example.com', $i));
            $user->setName($this->faker->name);
            $user->setPassword($this->userPasswordEncoder->encodePassword(
                $user,
                'test1234567'
            ));

            return $user;
        });

        $entityManager->flush();
    }
}
