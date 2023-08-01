<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class SwipeHelperFunctions
{
    /**
     * Get users that has been matched
     *
     * @param $usersDetails
     *
     * @return array
     */
    public static function getMatchedUsers($usersDetails)
    {
        DB::connection()->enableQueryLog();
        // Now we check if the match exists
        $first = DB::table('swipe_matches')->where('a', $usersDetails->id)->select('swipe_matches.b');

        $matchedMatch = DB::table('swipe_matches')->where('b',
            $usersDetails->id)->select('swipe_matches.a as matches')->union($first)->orderby('matches')->get();

        $matchedUsers = [];

        foreach ($matchedMatch as $key => $value) {
            $matchedUsers[] = $value->matches;
        }

        return $matchedUsers;
    }

    /**
     * Calculate the distance between two points
     *
     * @param $lat
     * @param $lng
     * @param string $units
     * @param bool $fields
     *
     * @return string
     */
    public static function haversine($lat, $lng, $units = 'miles', $fields = false)
    {
        if (empty($lat)) {
            $lat = 0;
        }
        if (empty($lng)) {
            $lng = 0;
        }
        /*
         *  Allow for changing of units of measurement
         */
        switch ($units) {
            case 'miles':
                //radius of the great circle in miles
                $gr_circle_radius = 3959;
                break;
            case 'kilometers':
                //radius of the great circle in kilometers
                $gr_circle_radius = 6371;
                break;
        }

        /*
 *  Support the selection of certain fields
 */
        if ( ! $fields) {
            $fields = array('locations.user_id');
        }

        /*
         *  Generate the select field for disctance
         */

        $distance_select = sprintf("           
					                ROUND(( %d * acos( cos( radians(%s) ) " . " * cos( radians( latitude ) ) " . " * cos( radians( longitude ) - radians(%s) ) " . " + sin( radians(%s) ) * sin( radians( latitude ) ) " . " ) " . ")
        							, 2 ) " . "AS location_distance
					                ", $gr_circle_radius, $lat, $lng, $lat);

        $data = (implode(',', $fields) . ',' . $distance_select);

        return $data;

    }

    /**
     * Calculate age mysql query
     * @return string
     */
    public static function constructQueryStringAge()
    {
        return sprintf("TIMESTAMPDIFF (YEAR, dob, CURDATE())" . " AS user_age");
    }


}