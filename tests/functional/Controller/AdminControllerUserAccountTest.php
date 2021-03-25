<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use App\Tests\utils\RoleUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerUserAccountTest extends WebTestCase
{
    use RoleUser;

    public function testUserDeleteAccount(): void
    {
        $this->client->request('GET', '/admin/delete-account');
        $user = $this->entityManager->getRepository(User::class)->find(2);

        $this->assertNull($user);
    }

    public function testUserChangePassword(): void
    {
        $crawler = $this->client->request('GET', '/admin/');

        $form = $crawler->selectButton('Save')->form([
           'user[name]' => 'name',
           'user[last_name]' => 'last_name',
           'user[email]' => 'user@app.test',
           'user[password][first]' => 'test',
           'user[password][second]' => 'test'
        ]);

        $this->client->submit($form);

        $user = $this->entityManager->getRepository(User::class)->find(2);
        $this->assertSame('name', $user->getName());
    }
}
