<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerVideoTest extends WebTestCase
{
    public function testNoResults(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $form = $crawler->selectButton('Search video')->form([
            'query' => 'aaa',
        ]);

        $crawler = $client->submit($form);

        $this->assertStringContainsString('No results were found', $crawler->filter('h1')->text());
    }

    public function testResultsFound()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $form = $crawler->selectButton('Search video')->form([
            'query' => 'Movies',
        ]);

        $crawler = $client->submit($form);

        $this->assertGreaterThan(4, $crawler->filter('h3')->count());
    }

    public function testSorting()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $form = $crawler->selectButton('Search video')->form([
            'query' => 'Movies',
        ]);

        $crawler = $client->submit($form);

        $form = $crawler->filter('#form-sorting')->form([
           'sortBy' => 'desc'
        ]);

        $crawler = $client->submit($form);

        $this->assertEquals('Movies 9', $crawler->filter('h3')->first()->text());
    }
}
