<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VideoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->videoData() as [$title, $path, $categoryId]){
            $duration = random_int(10,300);
            $category = $manager->getRepository(Category::class)->find($categoryId);
            $video = new Video();
            $video->setTitle($title);
            $video->setPath('https://player.vimeo/video/'. $path);
            $video->setCategory($category);
            $video->setDuration($duration);

            $manager->persist($video);
        }

        $manager->flush();
    }

    private function videoData(): array
    {
        return [
            ['Movies 1', 289729765,3],
            ['Movies 2', 238902809,3],
            ['Movies 3', 150870038,3],
            ['Movies 4', 219727723,3],
            ['Movies 5', 289879647,3],
            ['Movies 6', 60594348,3],
        ];
    }
}
