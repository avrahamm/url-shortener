<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreLinkRequest;
use App\Jobs\LogHit;
use App\Models\Link;
use App\Models\LinkHit;
use App\Services\LinkService;

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
//        info('LinkController::store', ['request' => $request->all()]);
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
        info('LinkController::stats', ['slug' => $slug]);
        $link = Link::where('slug', $slug)->firstorFail();

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

        return response()->json([
            'total_hits' => $totalHits,
            'last_hits' => $lastHits,
            'target_url' => $link->target_url,
        ]);
    }

    /**
     * GET /r/{slug} → 302 redirect to target_url,
     * if is_active=true, otherwise return 404/410.
     *
     * @param string $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @example: curl http://url-shortener/r/bl1  \
                   * -H "Content-Type: application/json"
     */
    public function redirect(string $slug)
    {
        //info('LinkController::redirect', ['slug' => $slug]);
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
