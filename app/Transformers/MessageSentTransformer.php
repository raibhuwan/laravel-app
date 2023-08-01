<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class MessageSentTransformer extends TransformerAbstract
{
    public function transform($chatMessage)
    {
        $formattedMessageSent = [
            'chat_message_id'   => $chatMessage->chat_message_id,
            'user_creator_id'   => $chatMessage->user_creator_id,
            'user_recipient_id' => $chatMessage->user_recipient_id,
            'device_message_id' => (string)$chatMessage->device_message_id,
            'type'              => (string)$chatMessage->type,
            'meta'              => (string)$chatMessage->meta,
            'created_at'        => (string)$chatMessage->created_at,
            'updated_at'        => (string)$chatMessage->updated_at,
        ];

        switch ($chatMessage->type) {
            case 'VOICE':
            case 'FILE':
                $formattedMessageSent['message_body'] = url('/') . '/storage/' . $chatMessage->message_body;

                break;
            default:
                $formattedMessageSent['message_body'] = $chatMessage->message_body;
        }

        return $formattedMessageSent;
    }
}