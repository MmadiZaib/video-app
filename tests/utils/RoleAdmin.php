<?php


namespace App\Tests\utils;


use Doctrine\ORM\EntityManagerInterface;

trait RoleAdmin
{
    protected $client;

    /** @var EntityManagerInterface  */
    protected $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        // return the real and unchanged service container
        $container = self::$kernel->getContainer();
        // gets the special container that allows fetching private services
        $container = self::$container;
        $cache = self::$container->get('App\Services\Cache\CacheInterface');

        $this->cache = $cache->cache;
        $this->cache->clear();


        $this->client = static::createClient([] , [
            'PHP_AUTH_USER' => 'admin@app.test',
            'PHP_AUTH_PW'   => 'test',
        ]);
        $this->client->disableReboot();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cache->clear();

        $this->entityManager = null; // avoid memory leaks
    }
}