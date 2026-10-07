<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Nomor ponsel Indonesia: 08…, 628…, atau +628… (9–15 digit).
 * Normalkan dulu dengan normalkan() di prepareForValidation().
 */
class NomorPonselIndonesia implements ValidationRule
{
    private const POLA = '/^(\+62|62|0)8[0-9]{7,12}$/';

    /**
     * Buang spasi, tanda hubung, titik, dan kurung dari isian nomor HP.
     */
    public static function normalkan(mixed $nilai): ?string
    {
        if (blank($nilai)) {
            return null;
        }

        return preg_replace('/[\s\-().]/', '', (string) $nilai);
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match(self::POLA, $value)) {
            $fail(':Attribute harus nomor ponsel Indonesia yang valid, mis. 081234567890.');
        }
    }
}
