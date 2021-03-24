<?php


namespace App\Tests\utils;


use Doctrine\ORM\EntityManagerInterface;

trait RoleUser
{
    protected $client;

    /** @var EntityManagerInterface  */
    protected $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient([] , [
            'PHP_AUTH_USER' => 'user@app.test',
            'PHP_AUTH_PW'   => 'test',
        ]);

        $this->client->disableReboot();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager = null; // avoid memory leaks
    }
}