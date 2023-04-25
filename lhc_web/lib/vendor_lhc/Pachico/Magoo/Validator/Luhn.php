<?php

namespace Pachico\Magoo\Validator;

/**
 * Simple class related to Luhn algorithm
 * @see https://en.wikipedia.org/wiki/Luhn_algorithm
 */
class Luhn implements ValidatorInterface
{
    /**
     * {@inheritDoc}
     */
    public function isValid($input)
    {
        if (!is_numeric($input)) {
            return false;
        }

        $numericString = (string) preg_replace('/\D/', '', $input);
        $sum = 0;
        $numDigits = strlen($numericString) - 1;
        $parity = $numDigits % 2;
        for ($i = $numDigits; $i >= 0; $i--) {
            $digit = substr($numericString, $i, 1);
            if (!$parity == ($i % 2)) {
                $digit <<= 1;
            }
            $digit = ($digit > 9)
                ? ($digit - 9)
                : $digit;
            $sum += $digit;
        }

        return (0 == ($sum % 10));
    }
}
