<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Curp implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        $result = preg_match('/^.{11}[a-z]{2}/i', $value);

        return $result === 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'El CURP es invalido.';
    }
}
