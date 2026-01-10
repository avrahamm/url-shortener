<?php

return [
    'api_key' => env('API_KEY', 'secret123'),
    'rate_limit_by_api_key' => env('API_RATE_LIMIT_BY_API_KEY', 30),
];
