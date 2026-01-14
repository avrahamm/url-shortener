<?php

namespace Tests\Feature;

use App\Jobs\LogHit;
use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LinkFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_store_link_succeeds(): void
    {
        $payload = ['target_url' => 'https://google.com'];
        $response = $this->postJson(
            '/api/links',
            $payload,
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Api-Key' => 'secret123',
            ]
        );

        $response->assertStatus(200)
        ->assertJsonStructure(['link']);

        $this->assertDatabaseHas('links', [
            'slug'       => $response->json('link.slug'),
            'target_url' => $payload['target_url'],
        ]);
    }

    public function test_unauthenticated_store_link_fails(): void
    {
        $payload = ['target_url' => 'https://example.com'];
        $response = $this->postJson(
            '/api/links',
            $payload,
            ['Accept' => 'application/json']
        );

        $response->assertStatus(401);
    }

    public function test_slug_duplicate_at_store_link(): void
    {
        $payload = [
            'target_url' => 'https://google.com',
            'slug' => 'google1'
        ];
        $response = $this->postJson(
            '/api/links',
            $payload,
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Api-Key' => 'secret123',
            ]
        );

        $response->assertStatus(200);

        $response = $this->postJson(
            '/api/links',
            $payload,
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Api-Key' => 'secret123',
            ]
        );

        // It should return 422 because the slug is duplicate
        $response->assertStatus(422);

    }

    public function test_redirect_and_dispatched_LogHit_job_to_queue()
    {
        Queue::fake();

        $targetUrl = 'https://apple.com';
        $slug = 'apple';

        $link = Link::factory()->create([
            'target_url' => $targetUrl,
            'slug' => $slug
        ]);

        $response = $this->get("/r/" . $slug);

        $response->assertStatus(302)
            ->assertHeader('Location', $targetUrl);

        \Illuminate\Support\Facades\Queue::assertPushed(LogHit::class,
        function(LogHit $job) use ($link) {
            return $job->linkId === $link->id;
        });
    }
}
