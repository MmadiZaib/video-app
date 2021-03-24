<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->commentDate() as [$content, $user, $video, $createdAt]) {
            $comment = new Comment();
            $user = $manager->getRepository(User::class)->find($user);
            $video = $manager->getRepository(Video::class)->find($video);

            $comment->setUser($user);
            $comment->setVideo($video);
            $comment->setContent($content);
            $comment->setCreatedAtForFixtures(new \DateTime($createdAt));

            $manager->persist($comment);
        }

        $manager->flush();
    }

    private function commentDate(): array
    {
        return [
          ['Cras sit amo, vestnc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.', 1, 10, '2021-10-08 12:24:45'],
          ['Cras sit amo, vestnc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.', 2, 10, '2021-10-08 13:24:45'],
          ['Cras sit amo, vestnc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.', 3, 10, '2021-10-08 15:24:45'],
        ];
    }

    public function getDependencies()
    {
        return [
           UserFixtures::class,
       ];
    }
}
