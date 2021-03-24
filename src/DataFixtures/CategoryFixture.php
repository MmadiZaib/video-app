<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadMainCategories($manager);
        $this->loadElectronics($manager);
        $this->loadComputers($manager);
        $this->loadLaptops($manager);
        $this->loadBooks($manager);
        $this->loadMovies($manager);
        $this->loadRomance($manager);
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
            ['Books', 4],
        ];
    }

    private function getElectronicsData(): array
    {
        return [
            ['Cameras', 5],
            ['Computers', 6],
            ['Phones', 7],
        ];
    }

    private function getComputersData(): array
    {
        return [
            ['Acer', 8],
            ['Apple', 9],
        ];
    }

    private function getLaptopsData(): array
    {
        return [
            ['Mac', 10],
            ['Asus', 11],
            ['Dell xp', 12],
            ['Lenovo', 13],
            ['HP', 14],
        ];
    }

    private function getBooksData(): array
    {
        return [
            ['Children\'s books', 15],
            ['Kindle eBooks', 16],
        ];
    }

    public function getMoviesData(): array
    {
        return [
            ['Family', 17],
            ['Romance', 18],
        ];
    }

    private function getRomanceData(): array
    {
        return [
            ['Romantic Comedy', 19],
            ['Romantic Drama', 20],
        ];
    }

    private function loadSubCategories(ObjectManager $manager, string $category, $parentId)
    {
        $parent = $manager->getRepository(Category::class)->find($parentId);
        $methodName = "get${category}Data";

        foreach ($this->$methodName() as [$name]) {
            $category = new Category();
            $category->setName($name);
            $category->setParent($parent);

            $manager->persist($category);
        }

        $manager->flush();
    }

    private function loadElectronics(ObjectManager $manager)
    {
        $this->loadSubCategories($manager, 'Electronics', 1);
    }

    private function loadComputers(ObjectManager $manager)
    {
        $this->loadSubCategories($manager, 'Computers', 6);
    }

    private function loadLaptops(ObjectManager $manager)
    {
        $this->loadSubCategories($manager, 'Laptops', 8);
    }

    private function loadRomance(ObjectManager $manager)
    {
        $this->loadSubCategories($manager, 'Romance', 18);
    }

    private function loadBooks(ObjectManager $manager)
    {
        $this->loadSubCategories($manager, 'Books', 4);
    }

    private function loadMovies(ObjectManager $manager)
    {
        $this->loadSubCategories($manager, 'Movies', 3);
    }
}
