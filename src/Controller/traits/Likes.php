<?php

namespace App\Controller\traits;

use App\Entity\User;
use App\Entity\Video;

trait Likes
{
    private function likeVideo(Video $video): string
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $user->addLikedVideo($video);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);

        $em->flush();

        return 'liked';
    }

    private function dislikeVideo(Video $video): string
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $user->addDislikedVideo($video);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);

        $em->flush();

        return 'disliked';
    }

    private function undoDislikeVideo(Video $video): string
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $user->removeDislikedVideo($video);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);

        $em->flush();

        return 'undo liked';
    }

    private function undoLikeVideo(Video $video): string
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $user->removeLikedVideo($video);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);

        $em->flush();

        return 'undo disliked';
    }
}
