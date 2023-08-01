<?php

/**
 * Global helpers file with misc functions.
 */
if ( ! function_exists('app_name')) {
    /**
     * Helper to grab the application name.
     *
     * @return mixed
     */
    function app_name()
    {
        return config('app.name');
    }

    /**
     * Generate 6 digits unique random code
     *
     * @return string
     */
    function generatePin()
    {
        $pin = '';
        $a   = array();
        $a   = array_fill(0, 10, 0);

        for ($i = 0; $i < 6; $i++) {
            $rand = rand(0, 9);
            while ($a[$rand] == 1) {
                $rand = rand(0, 9);
            }
            $a[$rand] = 1;
            $pin      .= $rand;
        };

        return $pin;
    }
}