<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use PhpCfdi\Rfc\Exceptions\InvalidExpressionToParseException;
use PhpCfdi\Rfc\Rfc as RfcParse;

class Rfc implements Rule
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
     * @throws \PhpCfdi\Rfc\Exceptions\InvalidExpressionToParseException
     */
    public function passes($attribute, $value): bool
    {
        try {
            RfcParse::parse($value);
            return true;
        } catch (InvalidExpressionToParseException $e) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'El RFC es invalido.';
    }
}
