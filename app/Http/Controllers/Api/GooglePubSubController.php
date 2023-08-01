<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Google\Cloud\PubSub\PubSubClient;

class GooglePubSubController extends Controller
{
    public function pubSub()
    {

        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path() . '/' . config('pubsub.connections.gcloud.key_file'));

        $pubsub = new PubSubClient([
            'projectId' => config('pubsub.connections.gcloud.project_id')
        ]);

        # The name for the new topic
        $topicName = 'my-new-topic-2';

# Creates the new topic
        $topic = $pubsub->createTopic($topicName);

        echo 'Topic ' . $topic->name() . ' created.';
    }
}
