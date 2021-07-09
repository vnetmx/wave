<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Nss implements Rule
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
        $value = preg_replace('/\s+/', '', trim($value));

        if (!is_string($value)) {
            return false;
        }
        // 19 14 89 62 43 4
        $result = preg_match('/^(\d{2})(\d{2})(\d{2})\d{5}$/i', $value, $output);

        if($result !== 1){
            return false;
        }

        $anno = date('Y') % 100;
        $subde = $output[1];
        $annoAlta = $output[2];
        $annoNac = $output[3];

        if ($subde != 97) {
            if ($annoAlta <= $anno) $annoAlta += 100;
            if ($annoNac  <= $anno) $annoNac  += 100;
            if ($annoNac  >  $annoAlta)
                return false;
        }

        return $this->luhn($value);
    }

    /**
     * @see http://en.wikipedia.org/wiki/Luhn_algorithm
     */
    private function luhn ($card_number) {
        $card_number_checksum = '';

        foreach (str_split(strrev((string) $card_number)) as $i => $d) {
            $card_number_checksum .= $i %2 !== 0 ? $d * 2 : $d;
        }

        return array_sum(str_split($card_number_checksum)) % 10 === 0;
    }
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'El número de seguro social es invalido.';
    }
}
