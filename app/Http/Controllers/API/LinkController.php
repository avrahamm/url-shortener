<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreLinkRequest;
use App\Jobs\LogHit;
use App\Models\Link;
use App\Models\LinkHit;
use App\Services\LinkService;
use Illuminate\Support\Facades\Cache;

class LinkController extends Controller
{
    /**
     * @param StoreLinkRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @example: curl -X POST http://url-shortener/api/links \
       -H "Content-Type: application/json" \
       -H "Accept: application/json" \
       -H "X-Api-Key: secret123" \
       -d '{
       "target_url": "https://example.com"
       }'
     */
    public function store(StoreLinkRequest $request)
    {
        $slug = $request->get('slug');
        if (!$slug) {
            $slug = (new LinkService)->getUniqueSlug();
        }
        $link = Link::create([
            'slug' => $slug,
            'target_url' => $request->validated()['target_url'],
            'is_active' => true
        ]);

        // Success, return response 200
        return response()->json([
                'link' => $link,
            ]);
    }


    /**
     * Returns JSON:
     *  total_hits,
     *  last_hits (5 latest with timestamp and truncated IP),
     *  and target_url.
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     * @example: curl http://url-shortener/api/links/bla/stats \
         -H "Content-Type: application/json" \
         -H "Accept: application/json" \
         -H "X-Api-Key: secret123"
     */
    public function stats(string $slug)
    {
        $link = Link::where('slug', $slug)->firstorFail();
        $cacheKey = (new LinkService())->getStatCacheKey($link->id);

        // If Cache exists, return it.
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $statsCacheTTLInSeconds = config('api.stats_cache_ttl_in_seconds');

        // If not, calculate and store in Cache it.
        $payload = Cache::remember(
            $cacheKey, $statsCacheTTLInSeconds, function () use ($link) {
            $totalHits = $link->hits()->count();
            $lastHits = LinkHit::where('link_id', $link->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($linkHit) {
                    $ip = $linkHit->ip;
                    $parts = explode('.', $ip);
                    $parts[3] = '0';
                    $truncatedIp = implode('.', $parts);
                    return [
                        'timestamp' => $linkHit->created_at->format('Y-m-d H:i:s'),
                        'ip' => $truncatedIp,
                    ];
                });

            // to make the difference vs. Cached call.
            sleep(5);
            return response()->json([
                'total_hits' => $totalHits,
                'last_hits' => $lastHits,
                'target_url' => $link->target_url,
            ]);
        });

        return response()->json($payload);
    }

    /**
     * GET /r/{slug} → 302 redirect to target_url,
     * if is_active=true, otherwise return 404/410.
     *
     * @param string $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @example: curl http://url-shortener/r/bl1  \
                    -H "Content-Type: application/json"
     */
    public function redirect(string $slug)
    {
        $link = Link::where('slug', $slug)->first();
        if (!$link || !$link->is_active) {
            abort(404);
        }
        LogHit::dispatch(
            linkId: $link->id,
            ip: request()->ip(),
            userAgent: request()->userAgent()
        );

        return redirect($link->target_url);
    }

}
