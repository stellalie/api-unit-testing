<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach (['BMW', 'Mercedes', 'Tesla'] as $name) {
            $product = new Product();
            $product->setName($name);
            $manager->persist($product);
        }
        $manager->flush();
    }
}
