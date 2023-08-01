<?php

namespace App\Helpers;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

class FacebookHelperFunctions
{

    public static function facebook()
    {
        return new Facebook(config('facebook.config'));
    }

    public static function getPermissions($providerId, $token)
    {
        $fb = self::facebook();

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get("/{$providerId}/permissions", $token);
        } catch (FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();

            return false;
        } catch (FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();

            return false;
        }

        $permissions = $response->getGraphEdge()->asArray();

        return $permissions;
    }

    /**
     * Checks if there is facebook permission
     *
     * @param $permission array
     * @param $permissionName string The name of the permission to be checked
     *
     * @return bool
     */
    public static function checkPermissions($permission, $permissionName)
    {
        $key = array_search($permissionName, array_column($permission, 'permission'));

        if (($permission[$key]['permission'] == $permissionName) && ($permission[$key]['status'] == 'granted')) {
            return true;
        }

        return false;
    }

}