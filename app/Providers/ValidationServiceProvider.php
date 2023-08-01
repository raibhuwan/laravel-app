<?php

namespace App\Providers;

use App\Validators\ImageValidation;
use App\Validators\PasswordStrengthValidation;
use App\Validators\UniquePhoneValidation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;


class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Password Strength Validation
         */
        $passwordStrength = app('passwordStrength');

        foreach (['letters', 'numbers', 'caseDiff', 'symbols'] as $rule) {
            Validator::extend($rule, function ($_, $value, $__) use ($passwordStrength, $rule) {
                $capitalizedRule = ucfirst($rule);

                return call_user_func([$passwordStrength, "validate{$capitalizedRule}"], $value);
            });
        }

        /**
         * Image Bases64 Validation
         */

        $imageBase64 = app('imageBase64');

        Validator::extend('imageBase64', function ($_, $value, $__) use ($imageBase64) {
            return call_user_func([$imageBase64, "validateBase64"], $value);
        });

        /**
         * Check unique user country code and phone number validation
         */

        $uniqueField = app('uniquePhoneField');

        Validator::extend('uniquePhoneField',
            function ($attribute, $value, $parameters, $validator) use ($uniqueField) {
                return call_user_func([$uniqueField, "checkUniquePhoneFields"], $attribute, $value, $parameters);
            });
        Validator::extend('uniquePhoneUpdateField',
            function ($attribute, $value, $parameters, $validator) use ($uniqueField) {
                return call_user_func([$uniqueField, "checkUniquePhoneUpdateFields"], $attribute, $value, $parameters);
            });

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton('passwordStrength', function () {
            return new PasswordStrengthValidation();
        });

        app()->singleton('imageBase64', function () {
            return new ImageValidation();
        });

        app()->singleton('uniquePhoneField', function () {
            return new UniquePhoneValidation();
        });
    }
}
