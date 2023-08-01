<?php

namespace App\Helpers;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class FcmHelperFunctions
{
    public static function checkPlatform($to)
    {
        $client = new Client();

        $requestContent = [
            'headers' => [
                'Authorization' => 'key=' . config('fcm.http.server_key'),
                'Content-Type'  => 'application/json'
            ]
        ];

        try {
            $responseGuzzle = $client->request('POST', "https://iid.googleapis.com/iid/info/{$to}", $requestContent);
        } catch (ClientException $e) {
            $responseGuzzle = $e->getResponse()->getReasonPhrase();

            return false;
        }

        $responseBody = $responseGuzzle->getBody()->getContents();

        $platform = json_decode($responseBody)->platform;

        return $platform;
    }



    public static function sendFcm($message, $fields, $dataBuilder)
    {
        $notificationBuilder = new PayloadNotificationBuilder("{$message}");
        $notificationBuilder->setBody($fields['data'])->setClickAction(config('fcm.click_action'));

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setContentAvailable(true);
        $optionBuilder->setMutableContent(true);

        $data         = $dataBuilder->build();
        $notification = $notificationBuilder->build();
        $option       = $optionBuilder->build();

        $platform = self::checkPlatform($fields['to']);

        if ( ! $platform) {
            return false;
        }

        if ($platform == 'ANDROID') {
            $messageSent = FCM::sendTo($fields['to'], $option, null, $data);
        } elseif ($platform == 'IOS') {
            $messageSent = FCM::sendTo($fields['to'], $option, $notification, $data);
        }

        if ($messageSent->numberSuccess() >= 1) {
            return true;
        }

        return false;

    }
}