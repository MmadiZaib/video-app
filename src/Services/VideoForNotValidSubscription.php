<?php

namespace App\Services;

use App\Entity\Video;
use Symfony\Component\Security\Core\Security;

class VideoForNotValidSubscription
{
    public bool $isSubscriptionValid = false;

    public function __construct(Security $security)
    {
        $user = $security->getUser();

        if ($user && null !== $user->getSubscription()) {
            $paymentStatus = $user->getSubscription()->getPaymentStatus();
            $valid = new \DateTime() < $user->getSubscription()->getValidTo();

            if (null !== $paymentStatus && $valid) {
                $this->isSubscriptionValid = true;
            }
        }
    }

    public function check(): ?int
    {
        if ($this->isSubscriptionValid) {
            return null;
        } else {
            return Video::VIDEO_FOR_NOT_LOGGED_IN_OR_NO_MEMBERS;
        }
    }
}
