<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use App\Tests\utils\RoleAdmin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerUserTest extends WebTestCase
{
    use RoleAdmin;

    public function testDeleteUser(): void
    {
        $this->client->request('GET', '/admin/delete-user/4');
        $user = $this->entityManager->getRepository(User::class)->find(4);

        $this->assertNull($user);
    }
}
