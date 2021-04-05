<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\Video;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Twig\Environment;

class NewVideoListener
{
    /**
     */
    private Environment $templating;
    private \Swift_Mailer $mailer;

    public function __construct(Environment $templating, \Swift_Mailer $mailer)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Video) {
            return;
        }

        $entityManager = $args->getObjectManager();

        $users = $entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('send@app.test')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->templating->render('emails/new_video.html.twig', [
                        'name' => $user->getName(),
                        'video' => $entity
                    ])
                );

            $this->mailer->send($message);
        }
    }
}