<?php

namespace App\Validators;

use Illuminate\Support\Facades\DB;

class UniquePhoneValidation
{
    public function checkUniquePhoneFields($attribute, $value, $parameters)
    {
        $count = DB::table('users')->where('country_code', $parameters[0])
                   ->where('phone', $value)
                   ->count();

        return $count === 0;
    }
    public function checkUniquePhoneUpdateFields($attribute, $value, $parameters)
    {
        $count = DB::table('users')->where('country_code', $parameters[0])
                   ->where('phone', $value)->whereNotIn('id', [$parameters[1]])
            ->count();

        return $count === 0;
    }
}