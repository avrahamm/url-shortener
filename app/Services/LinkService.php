<?php

namespace App\Services;

use App\Models\Link;
use Faker\Provider\Uuid;

class LinkService
{
    /**
     * Returns six chars long slug.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return Uuid::bothify('******');
    }

    public function getUniqueSlug(): string
    {
        while (true) {
            $slug = $this->getSlug();
            if (Link::where('slug', $slug)->exists()) {
                continue;
            }
            return $slug;
        }
    }

    public function getStatCacheKey(int $linkId): string
    {
        return 'link_stats_' . $linkId;

    }
}
