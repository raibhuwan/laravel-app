<?php

namespace App\Validators;

class ImageValidation
{
    public function validateBase64($value)
    {
        // Check if there are valid base64 characters
        if ( ! preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $value)) {
            return false;
        }

        // Decode the string in strict mode and check the results
        $decoded = base64_decode($value, true);

        if (false === $decoded) {
            return false;
        }

        // Encode the string again
        if (base64_encode($decoded) != $value) {
            return false;
        }

        return true;
    }
}
