<?php

/*
 * @param string[] $alphabet
 */
$testAlphabetHasDuplicates = function ($alphabet) {
     return count($alphabet) !== count(array_unique($alphabet));
};


$bcfloor = function ($number) {
    if (strpos($number, '.') !== false) {
        if (preg_match("~\.[0]+$~", $number)) return bcround($number, 0);
        if ($number[0] != '-') return bcadd($number, 0, 0);
        return bcsub($number, 1, 0);
    }
    return $number;
};

/*
 *CASE MATTERS
 * @param string $value
 * @param string $alphabetIn
 * @param string $alphabetOut
 */
$convertCustomBases = function ($value, $alphabetIn, $alphabetOut) use ($testAlphabetHasDuplicates, $bcfloor) {
    $alphabetInDict = str_split($alphabetIn);
    $alphabetOutDict = str_split($alphabetOut);

    //test alphabets for uniqueness of characters
    if ($testAlphabetHasDuplicates($alphabetInDict)) {
        echo 'Alphabet in has duplicate characters';
        return;
    }

    if ($testAlphabetHasDuplicates($alphabetOutDict)) {
        echo 'Alphabet out has duplicate characters';
        return;
    }

    $alphaOutSize = count($alphabetOutDict);
    $valueArray = str_split($value);

    //make sure alphabets remain in inputed order
    //convert incoming alphabet to base10
    //start at the rightmost char in the string
    // size of alpha is base,  eq will be summation of (size ^ location) * value

    $exponent = 0;
    $base10Value = "";
    for($i = count($valueArray) - 1; $i >= 0; $i--) {
        $currentValue = array_search($valueArray[$i], $alphabetInDict);
        // $base10Value += $currentValue * (pow(count($alphabetInDict), $exponent));
        $base10Value = bcadd($base10Value, bcmul((string)$currentValue, bcpow((string)count($alphabetInDict), (string)$exponent)));
        $exponent++;
    }

    //convert base10 to outgoing alphabet
    //take base 10 value, divide by length of outgoing alphabet, remainder is first value in new alpha, whole value is divided again, repeat until whole is 0

    $outgoingBaseValue = '';
    while ($base10Value > 0) {
        $outgoingBaseValue = $alphabetOutDict[bcmod($base10Value, (string)$alphaOutSize)] . $outgoingBaseValue;
        $base10Value = $bcfloor(bcdiv($base10Value, (string)$alphaOutSize));
    }

    return $outgoingBaseValue;
};

$hexAlpha = "0123456789abcdef";
$base64Alpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
$value = "49276d206b696c6c696e6720796f757220627261696e206c696b65206120706f69736f6e6f7573206d757368726f6f6d";
//SSdtIGtpbGxpbmcgeW91ciBicmFpbiBsaWtlIGEgcG9pc29ub3VzIG11c2hyb29t

var_dump($convertCustomBases($value, $hexAlpha, $base64Alpha));

 ?>
