<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixture extends BaseFixture
{

    protected function loadData(ObjectManager $entityManager)
    {
//        $this->createMany(10, 'main_users', function ($i){
//            $product = new Product();
//            $product->setName($this->faker->streetName);
//            $product->setImageFilename($this->faker->imageUrl());
//            $product->setCategory(1);
//            $product->setImageFilename($this->faker->imageUrl());
//
//
//            return $product;
//        });

        $entityManager->flush();
    }
}
