<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixture extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $this->loadMainCategories($manager);
    }

    private function loadMainCategories(ObjectManager $manager)
    {
        foreach ($this->getMainCategoriesData() as [$name]) {
            $category = new Category();
            $category->setName($name);

            $manager->persist($category);
        }

        $manager->flush();
    }

    private function getMainCategoriesData(): array
    {
        return [
            ['Electronics', 1],
            ['Toys', 2],
            ['Movies', 3],
            ['Books', 4]
        ];
    }
}
