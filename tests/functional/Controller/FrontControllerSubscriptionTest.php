<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Subscription;
use App\Tests\utils\RoleUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerSubscriptionTest extends WebTestCase
{
    use RoleUser;

    /**
     * @dataProvider urlsWithVideo
     */
    public function testLoggedInUserDoesNotSeeTextForNoMembers(string $url): void
    {
        $this->client->request('GET', $url);

        $this->assertStringNotContainsString('Video for <b>MEMBERS</b> only.',  $this->client->getResponse()->getContent());
    }

    /**
     * @dataProvider urlsWithVideo
     */
    public function testNotLoggedInUserSeesTextForNoMembers(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertStringContainsString('Video for <b>MEMBERS</b> only.', $client->getResponse()->getContent());
    }


    public function testExpiredSubscription(): void
    {
        $subscription = $this->entityManager->getRepository(Subscription::class)->find(2);
        $invalidDate = new \DateTime();
        $invalidDate->modify('-1 day');

        $subscription->setValidTo($invalidDate);

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();

        $this->client->request('GET', '/video-list/category/movies,3');
        $this->assertStringContainsString('Video for <b>MEMBERS</b> only.', $this->client->getResponse()->getContent());
    }

    /**
     * @dataProvider urlsWithVideo2
     */
    public function testNotLoggedInUserSeesVideosForMembers(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertStringContainsString('https://player.vimeo.com/video/113716040', $client->getResponse()->getContent());
    }

    public function urlsWithVideo(): iterable
    {
        yield ['/video-list/category/movies,3'];
        yield ['/search-results?query=movies'];
    }

    public function urlsWithVideo2(): iterable
    {
        yield ['/video-list/category/movies,3'];
        yield ['/search-results?query=Movies+3'];
        yield ['/video-details/2#video_comments'];
    }
}
