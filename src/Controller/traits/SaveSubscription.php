<?php

namespace App\Controller\traits;

use App\Entity\Subscription;
use App\Entity\User;

trait SaveSubscription
{
    private function saveSubscription(string $plan, User $user)
    {
        $date = new \DateTime();
        $date->modify('+1 month');
        $subscription = $user->getSubscription();

        if (null === $subscription) {
            $subscription = new Subscription();
        }

        if ($subscription->getFreePlanUsed() &&
            $plan === Subscription::getPlanDataNameByIndex(Subscription::FREE_PLAN)) {
            return;
        }

        $subscription->setValidTo($date);
        $subscription->setPlan($plan);

        if ($plan === Subscription::getPlanDataNameByIndex(Subscription::FREE_PLAN)) {
            $subscription->setFreePlanUsed(true);
            $subscription->setPaymentStatus('paid');
        }

        $subscription->setPaymentStatus('paid');

        $user->setSubscription($subscription);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
    }
}
