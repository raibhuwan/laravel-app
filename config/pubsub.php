<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Default
    |--------------------------------------------------------------------------
    |
    | The default pub-sub connection to use.
    |
    |
    */
    'default'     => env('PUBSUB_CONNECTION', 'gcloud'),
    /*
    |--------------------------------------------------------------------------
    | Pub-Sub Connections
    |--------------------------------------------------------------------------
    |
    | The available pub-sub connections to use.
    |
    | A default configuration has been provided for all adapters shipped with
    | the package.
    |
    */
    'connections' => [
        'gcloud' => [
            'driver'     => 'gcloud',
            'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
            'key_file'   => env('GOOGLE_CLOUD_KEY_FILE')
        ]
    ],
];