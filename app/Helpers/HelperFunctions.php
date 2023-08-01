<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class HelperFunctions
{
    public static function sendSmsMessage($user, $message)
    {
        $accountSid = config('services.twilio')['accountSid'];
        $authToken  = config('services.twilio')['authToken'];

        $client = new Client($accountSid, $authToken);

        $twilioPhoneNumber = config('services.twilio')['phoneNumber'];
        $messageParams     = array(
            'from' => $twilioPhoneNumber,
            'body' => $message
        );

        try {
            $client->messages->create($user->country_code . $user->phone, $messageParams);

            Log::info('Message sent to ' . $user->phone);
        } catch (TwilioException $e) {
            Log::error('Could not send SMS notification.' . ' Twilio replied with: ' . $e->getMessage());
        }
    }

    /**
     * This function check if the phone verification code is expired or not.
     *
     * @param $date
     *
     * @return bool
     */
    public static function checkDateExpiration($date)
    {
        $today       = Carbon::now();
        $expiry_time = Carbon::parse($date);

        $expiry_time_carbon = Carbon::create($expiry_time->year, $expiry_time->month, $expiry_time->day,
            $expiry_time->hour, $expiry_time->minute, $expiry_time->second);

        if ($today->gt($expiry_time_carbon)) {
            return false;
        }

        return true;
    }

    public static function getExpiryTime()
    {
        // Get current date using Carbon
        $today = Carbon::now();

        // Add 30 minutes to current date
        return $today->addMinutes(30);

    }

    /**
     * check if the token is valid
     *
     * @param $token
     * @param $hashedToken
     *
     * @return bool
     */
    public static function validateToken($token, $hashedToken)
    {
        if (Hash::check($token, $hashedToken)) {
            return true;
        }

        return false;
    }

    /**
     * This will generate the image Url for the images to be shown
     *
     * @param $path path of the directory where the images is in (eg : storage/image/logo/)
     * @param $configUrl config_file.variable_name
     * @param $assetUrl path of the default image (eg : images/logo/image.png)
     *                  This should be in public folder in the project
     *
     * @return string
     *
     */
    public static function getEmailTemplateImages($path, $configUrl, $assetUrl)
    {
        if (config($configUrl) != '') {
            return asset($path . config($configUrl));
        }

        return asset($assetUrl);
    }

    /**
     * Helper function to send the path of dummy profile, either male or female. If the current user's gender
     * is male then male png is sent otherwise female png is send
     *
     * @return string Path of the image
     */
    public static function getBlankProfileImagePath()
    {
        $currentUser = Auth::user();

        $path = ($currentUser->gender == 'MALE') ? '/images/profile/male.png' : '/images/profile/female.png';

        return $path;
    }

    /**
     * Helper function to send back the profile image link
     * First it will check the path and If the link is set to 1, it will just return the path else it will return the full path that is stored in folder
     * If the path is not set then it will check the gender and return the dummy profile
     *
     * @param $link
     * @param $path
     * @param $name
     * @param $gender
     *
     * @return string
     */
    public static function getImageLink($link, $path, $name, $gender)
    {
        return isset($path) ? (($link == 1) ? $path : url('/') . '/storage/' . $path . $name) : ($gender == 'MALE' ? '/images/profile/male.png' : '/images/profile/female.png');
    }

    public static function getImageLinkPlaceholder($link, $path, $name)
    {
        return ($link == 1) ? $path : url('/') . '/storage/' . $path . $name;
    }

    /**
     * Used to receive image link.
     * If the image is link, then the image link is returned, but if it not a link, path and image name are joined and then returned.
     * If the image is not set then image placeholder is sent
     *
     * @param $image
     * @param $number
     *
     * @return mixed|string
     */
    public static function imageLink($image, $number)
    {
        foreach ($image as $key => $value) {

            if (isset($image[$key]['number']) && $image[$key]['number'] == $number) {
                if ($image[$key]['link'] == 1) {
                    return $image[$key]['path'];
                }

                return asset('/storage/' . $image[$key]['path'] . $image[$key]['name']);
            }
        }

        return asset('images/profile/portrait_placeholder.png');
    }

    /**
     * Enable or Disable button for images
     *
     * @param $image
     * @param $number
     *
     * @return string
     */
    public static function imageDelete($image, $number)
    {
        foreach ($image as $key => $value) {

            if (isset($image[$key]['number']) && $image[$key]['number'] == $number) {
                return true;
            }
        }

        return false;
    }
}
