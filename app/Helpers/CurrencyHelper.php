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

        $rialWords = ucwords($transformer->toWords($integerPart));

        if($baisaPart > 0){

            $baisaWords = ucwords($transformer->toWords($baisaPart));

            return $rialWords.' Omani Rials and '.$baisaWords.' Baisa Only';
        }

        return $rialWords.' Omani Rials Only';
    }

}