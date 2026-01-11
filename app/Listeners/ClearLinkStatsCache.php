<?php

namespace App\Listeners;

use App\Events\LinkHitRecorded;
use App\Services\LinkService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class ClearLinkStatsCache
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LinkHitRecorded $event): void
    {
        $statsCacheKey = (new LinkService())->getStatCacheKey($event->linkId);
        Cache::forget($statsCacheKey);
    }
}
