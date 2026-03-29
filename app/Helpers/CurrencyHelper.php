<?php

namespace App\Helpers;

use NumberToWords\NumberToWords;

class CurrencyHelper
{
    public static function omrToWords($amount)
    {
        $integerPart = floor($amount);
        $baisaPart = round(($amount - $integerPart) * 1000);

        $numberToWords = new NumberToWords();
        $transformer = $numberToWords->getNumberTransformer('en');

        $words = '';

        // 🔹 Rials
        if ($integerPart > 0) {
            $rialWords = ucwords($transformer->toWords($integerPart));
            $words .= $rialWords . ' ' . ($integerPart == 1 ? 'Omani Rial' : 'Omani Rials');
        }

        // 🔹 Baisa
        if ($baisaPart > 0) {
            $baisaWords = ucwords($transformer->toWords($baisaPart));

            if ($integerPart > 0) {
                $words .= ' and ';
            }

            $words .= $baisaWords . ' Baisa';
        }

        // 🔹 If both are zero
        if ($integerPart == 0 && $baisaPart == 0) {
            return 'Zero Omani Rials Only';
        }

        return $words . ' Only';
    }
}
