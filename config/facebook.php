<?php

return [
    'config' => [
        'app_id'                => env('FACEBOOK_CLIENT_ID', null),
        'app_secret'            => env('FACEBOOK_CLIENT_SECRET', null),
        'default_graph_version' => env('FACEBOOK_DEFAULT_GRAPH_VERSION', 'v2.10')
    ]

];
