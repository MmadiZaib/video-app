<?php


namespace App\Services\Cache;


use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

class RedisCache implements CacheInterface
{
    public $cache;

    public function __construct()
    {
        $this->cache = new TagAwareAdapter(new RedisAdapter(RedisAdapter::createConnection('redis://redis')));
    }
}