<?php

namespace App\EventSubscriber;

use App\Services\Cache\CacheInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class AdminChangesDataSubscriber implements EventSubscriberInterface
{
    protected $routesNameThatMustClearCache = [
        'categories.POST',
        'edit_category.POST',
        'delete_video.GET',
        'set_video_duration.GET',
        'update_video_category.POST',
        'like_video.POST',
        'dislike_video.POST',
        'undo_like_video.POST',
        'undo_dislike_video.POST',
    ];

    /** @var CacheInterface */
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $request = $event->getRequest()->attributes->get('_route') . '.' . $event->getRequest()->getMethod();

        if (!in_array($request, $this->routesNameThatMustClearCache)) {
            return;
        }

        $cache = $this->cache->cache;
        $cache->clear();

    }
}
