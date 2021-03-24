<?php

namespace App\Tests\Functional\Controller;

use App\Tests\utils\RoleUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerLikesTest extends WebTestCase
{
    use RoleUser;

    public function testLike(): void
    {
        $this->client->request('POST', '/video-list/11/like');

        $crawler = $this->client->request('GET', '/video-list/category/movies,3');

        $this->assertSame('(3)', $crawler->filter('small.number-of-likes-11')->text());
    }

    public function testDislike(): void
    {
        $this->client->request('POST', '/video-list/11/dislike');

        $crawler = $this->client->request('GET', '/video-list/category/movies,3');

        $this->assertSame('(1)', $crawler->filter('small.number-of-dislikes-11')->text());
    }

    public function testNumberOfLikesVideos(): void
    {
        $this->client->request('POST', '/video-list/6/like');
        $crawler = $this->client->request('GET', '/admin/videos');

        $this->assertEquals(5, $crawler->filter('tbody>tr')->count());
    }

    public function testNumberOfDislikesVideos(): void
    {
        $this->client->request('POST', '/video-list/6/dislike');
        $crawler = $this->client->request('GET', '/admin/videos');

        $this->assertEquals(4, $crawler->filter('tbody>tr')->count());
    }

}
