<?php

namespace App\Helpers;

class ValidatorHelper
{
    public static function FormatValidatorErrors(array $errors = []): Array
    {
        return array_map(fn ($err) => ($err[0]), $errors);
    }
}
