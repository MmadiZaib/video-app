<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SubscriptionsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->getSubscriptionsData() as [$userId, $plan, $validTo, $paymentStatus, $freePlanUsed]) {
            $subscription = new Subscription();
            $subscription->setPlan($plan);
            $subscription->setValidTo($validTo);
            $subscription->setPaymentStatus($paymentStatus);
            $subscription->setFreePlanUsed($freePlanUsed);

            $user = $manager->getRepository(User::class)->find($userId);
            $user->setSubscription($subscription);

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getSubscriptionsData(): array
    {
        return [
          [1, Subscription::getPlanDataNameByIndex(2), (new \DateTime())->modify('+100year'), 'paid', false], // super admin
          [2, Subscription::getPlanDataNameByIndex(0), (new \DateTime())->modify('+1 month'), 'paid', true],
          [4, Subscription::getPlanDataNameByIndex(1), (new \DateTime())->modify('+1 minute'), 'paid', false],
        ];
    }
}
