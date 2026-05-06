<?php

namespace App\Validation;

use BackedEnum;

class EnumRules
{
    public function enumValue(string $value, string $params, array $data, ?string &$error = null): bool
    {
        if (!enum_exists($params)) {
            $error = 'Invalid enum class.';
            return false;
        }

        if (!is_subclass_of($params, BackedEnum::class)) {
            $error = 'Enum must be a backed enum.';
            return false;
        }

        if ($params::tryFrom($value) !== null) {
            return true;
        }

        $error = 'The selected value is invalid.';
        return false;
    }
}
