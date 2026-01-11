<?php

namespace Tests\Unit;

use App\Services\LinkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;
use Faker\Provider\Uuid;

class SlugUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_uniqueness_and_allowed_characters(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $slug1 = Uuid::bothify("******");
            $this->assertMatchesRegularExpression(
                '/^[A-Za-z0-9]+$/', $slug1
            );

            $slug2 = Uuid::bothify("******");
            $this->assertMatchesRegularExpression(
                '/^[A-Za-z0-9]+$/', $slug1
            );
            $this->assertNotSame($slug1, $slug2);
        }
    }
}
