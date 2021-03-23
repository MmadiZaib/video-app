<?php

namespace App\Tests\Functional\Controller;

use App\Tests\utils\Rollback;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerCommentsTest extends WebTestCase
{
    use Rollback;

    public function testNotLoggedInUser(): void
    {
        $client  = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/video-details/16');
        $form = $crawler->selectButton('Add')->form([
            'comment' => 'Test'
        ]);

        $client->submit($form);

        $this->assertStringContainsString('Please sign in', $client->getResponse()->getContent());
    }

    public function testNewCommentAndNumberOfComments(): void
    {
        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/new-details/16');

        $form = $crawler->selectButton('Add')->form([
            'comment' => 'Test comment'
        ]);

        $this->client->submit($form);

        $this->assertStringContainsString('Test comment', $this->client->getResponse()->getContent());

        $crawler = $this->client->request('GET', '/video-list/category/movies,3');

        //$this->assertSame('Comments (1)', $crawler->filter('a.ml-2')->text());
    }
}
