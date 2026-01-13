<?php

namespace Tests\Unit;

use App\Services\LinkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class SlugUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_allowed_characters(): void
    {
        $count = 10;
        $links = [];

        for ($i = 0; $i < $count; $i++) {
             $slug = new LinkService()->getSlug();
            $this->assertMatchesRegularExpression(
                '/^[A-Za-z0-9]+$/', $slug
            );
        }
    }
}
