<?php

namespace App\Services;

use App\Models\Link;
use Faker\Provider\Uuid;

class LinkService
{
    public function getUniqueSlug(): string
    {
        while (true) {
            $slug = Uuid::bothify('******');
            if (Link::where('slug', $slug)->exists()) {
                continue;
            }
            return $slug;
        }
    }
}
