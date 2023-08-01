<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class MessageTransformer extends TransformerAbstract
{
    public function transform($chatMessage)
    {
        $formattedMessage = [
            'chat_message_id'   => $chatMessage->chat_message_id,
            'user_creator_id'   => $chatMessage->user_creator_id,
            'device_message_id' => (string)$chatMessage->device_message_id,
            'type'              => (string)$chatMessage->type,
            'meta'              => (string)$chatMessage->meta,
            'created_at'        => (string)$chatMessage->created_at,
            'updated_at'        => (string)$chatMessage->updated_at,
        ];

        switch ($chatMessage->type) {
            case 'VOICE':
            case 'FILE':
                $formattedMessage['message_body'] = url('/') . '/storage/' . $chatMessage->message_body;
                break;
            default:
                $formattedMessage['message_body'] = $chatMessage->message_body;
        }

        return $formattedMessage;
    }
}