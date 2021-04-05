<?php

namespace App\Tests\Functional\controller;

use App\Entity\Video;
use App\Tests\utils\RoleAdmin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerVideosTest extends WebTestCase
{
    use RoleAdmin;

    public function testDeleteVideo(): void
    {
        $this->client->request('GET', '/admin/su/delete-video/11/60594348');
        $video = $this->entityManager->getRepository(Video::class)->find(11);

        $this->assertNull($video);
    }
}
