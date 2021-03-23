<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VideoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->videoData() as [$title, $path, $categoryId]) {

            $duration = random_int(10,300);
            $category = $manager->getRepository(Category::class)->find($categoryId);
            $video = new Video();
            $video->setTitle($title);
            $video->setPath('https://player.vimeo.com/video/'. $path);
            $video->setCategory($category);
            $video->setDuration($duration);

            $manager->persist($video);
        }

        $manager->flush();

        $this->loadLikes($manager);
        $this->loadDislikes($manager);
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
            ['Movies 7', 60594348,3],
            ['Movies 8', 60594348,3],
            ['Movies 9', 60594348,3],
            ['Movies 10', 60594348,3],
            ['Movies 11', 60594348,3],
            ['Movies 12', 60594348,3],
            ['Movies 13', 60594348,3],
            ['Movies 14', 60594348,3],
            ['Movies 15', 60594348,3],
            ['Movies 16', 60594348,3],
            ['Movies 17', 60594348,3],
            ['Movies 18', 60594348,3],
            ['Movies 19', 60594348,3],
            ['Movies 20', 60594348,3],
            ['Movies 21', 60594348,3],
        ];
    }

    private function loadLikes(ObjectManager $manager): void
    {
        foreach ($this->likesData() as [$videoId, $userId]) {
            $video = $manager->getRepository(Video::class)->find($videoId);
            $user = $manager->getRepository(User::class)->find($userId);
            $video->addUserThatLike($user);

            $manager->persist($video);
        }

        $manager->flush();
    }

    private function loadDislikes(ObjectManager $manager): void
    {
        foreach ($this->disLikesData() as [$videoId, $userId]) {
            $video = $manager->getRepository(Video::class)->find($videoId);
            $user = $manager->getRepository(User::class)->find($userId);
            $video->addUserThatDontLike($user);

            $manager->persist($video);
        }

        $manager->flush();
    }

    private function likesData(): array
    {
        return [
            [12,1],
            [12,2],
            [12,3],
            [11,1],
            [11,2],
            [11,3],
            [1,1],
            [1,2],
            [1,3],
            [2,1],
            [2,2],
        ];
    }

    private function disLikesData(): array
    {
        return [
            [10, 1],
            [10, 2],
            [10, 3]
        ];
    }
}
