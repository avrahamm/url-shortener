<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreLinkRequest;
use App\Models\Link;
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
     * @param string $slug
     * @return void
     * @example: curl http://url-shortener/api/links/bla/stats \
     *   -H "Content-Type: application/json" \
     *   -H "Accept: application/json" \
     *   -H "X-Api-Key: secret123"
     */
    public function stats(string $slug)
    {
        info('LinkController::stats', ['slug' => $slug]);
    }

    /**
     * @param string $slug
     * @return void
     * @example: curl http://url-shortener/r/bl1  \
     *              -H "Content-Type: application/json"
     */
    public function redirect(string $slug)
    {
        info('LinkController::redirect', ['slug' => $slug]);
    }

}
