<?php

namespace App\Tests\Functional\Controller;

use App\Tests\utils\RoleUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerSubscriptionTest extends WebTestCase
{
    use RoleUser;

    public function testDeleteSubscription(): void
    {
        $crawler = $this->client->request('GET', '/admin/');

        $link = $crawler->filter('a:contains("cancel plan")')->link();

        $this->client->click($link);


        $this->client->request('GET', '/video-list/category/movies,3');

        $this->assertStringContainsString('Video for <b>MEMBERS</b> only.', $this->client->getResponse()->getContent());
    }
}
