<?php

return [
    'api_key' => env('API_KEY', 'secret123'),
    'rate_limit_by_api_key' => env('API_RATE_LIMIT_BY_API_KEY', 30),
    'stats_cache_ttl_in_seconds' => env('STATS_CACHE_TTL_IN_SECONDS', 30),
];
