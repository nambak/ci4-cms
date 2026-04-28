<?php

namespace App\Validation;

class TagRules
{
    public function is_array_of_int($value, ?string &$error = null): bool
    {
        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $v) {
            $valid = filter_var($v, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

            if ($valid === false) {
                $error = 'The {field} field must contain positive integers only';
                return false;
            }
        }

        return true;
    }
}
