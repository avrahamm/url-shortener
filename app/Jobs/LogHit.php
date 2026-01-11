<?php

namespace App\Jobs;

use App\Events\LinkHitRecorded;
use App\Models\LinkHit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Link;

class LogHit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $linkId,
        public string $ip,
        public string $userAgent,
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        LinkHit::create([
            'link_id' => $this->linkId,
            'ip' => $this->ip,
            'user_agent' => $this->userAgent
        ]);

        event(new LinkHitRecorded($this->linkId));
    }
}
