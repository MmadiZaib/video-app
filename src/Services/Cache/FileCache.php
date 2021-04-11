<?php


namespace App\Services\Cache;


use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

class FileCache implements CacheInterface
{
    public $cache;

    public function __construct()
    {
        $this->cache = new TagAwareAdapter(new FilesystemAdapter());
    }
}