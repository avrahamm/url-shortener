<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreLinkRequest;

class LinkController extends Controller
{
    /**
     * @param StoreLinkRequest $request
     * @return void
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
        info('LinkController::store', ['request' => $request->all()]);
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
