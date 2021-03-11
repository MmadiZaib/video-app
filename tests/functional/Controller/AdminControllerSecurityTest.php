<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminControllerSecurityTest extends WebTestCase
{
    /** @dataProvider getUrlsForRegularUsers */
    public function testAccessDeniedForRegularUsers(string $httpMethod, string $url): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'user@app.test',
            'PHP_AUTH_PW'   => 'test',
        ]);

        $client->request($httpMethod, $url);
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }


    public function testAdminSu(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin@app.test',
            'PHP_AUTH_PW'   => 'test',
        ]);

        $crawler = $client->request('GET', '/admin/su/categories');

        $this->assertSame('Categories list', $crawler->filter('h2')->text());
    }


    public function getUrlsForRegularUsers(): iterable
    {
        yield ['GET', '/admin/su/categories'];
        yield ['GET', '/admin/su/edit-category/1'];
        yield ['GET', '/admin/su/delete-category/1'];
        yield ['GET', '/admin/su/users'];
    }
}
