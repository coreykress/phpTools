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

$base10Alpha = "0123456789";
$hexAlpha = "0123456789abcdef";
$base64Alpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
$value = "49276d206b696c6c696e6720796f757220627261696e206c696b65206120706f69736f6e6f7573206d757368726f6f6d";
//SSdtIGtpbGxpbmcgeW91ciBicmFpbiBsaWtlIGEgcG9pc29ub3VzIG11c2hyb29t

// var_dump($convertCustomBases($value, $hexAlpha, $base64Alpha));

//part 2
 /**
 *Write a function that takes two equal-length buffers and produces their XOR combination.
 *
 *If your function works properly, then when you feed it the string:
 *
 *1c0111001f010100061a024b53535009181c
 *... after hex decoding, and when XOR'd against:
 *
 *686974207468652062756c6c277320657965
 *... should produce:
 *
 *746865206b696420646f6e277420706c6179
 **/

$hexA = "1c0111001f010100061a024b53535009181c";
$hexB = "686974207468652062756c6c277320657965";

$convertToBinary = function ($value, $alphabetIn) use ($convertCustomBases) {
    return $convertCustomBases($value, $alphabetIn, "01");
};

$convertFromBinary = function ($value, $alphabetOut) use ($convertCustomBases) {
    return $convertCustomBases($value, "01", $alphabetOut);
};

$hexABinary = $convertToBinary($hexA, $hexAlpha);
$hexBBinary = $convertToBinary($hexB, $hexAlpha);

//xor a or b = true, not a and not b = false, a and b = false
$xOrBinaryStrings = function ($a, $b) {
    if (strlen($a) !== strlen($b)) {
        while (strlen($a) !== strlen($b)) {
            if (strlen($a) < strlen($b)) {
                $a = "0" . $a;
            }elseif (strlen($a) > strlen($b)) {
                $b = "0" . $b;
            } else {
                return "ERROR WITH STRING LENGTHS";
                die();
            }
        }
    }

    $xOrString = "";
    for ($i = strlen($a) - 1; $i >= 0; $i--) {
        $xOrString = ($a[$i] === $b[$i] ? "0" : "1") . $xOrString;
    }

    return $xOrString;
};

$binaryXOR = $xOrBinaryStrings($hexABinary, $hexBBinary);
$xOrValue = $convertFromBinary($binaryXOR, $hexAlpha);

// var_dump($xOrValue);

//part 3
// Single-byte XOR cipher
// The hex encoded string:
//
// 1b37373331363f78151b7f2b783431333d78397828372d363c78373e783a393b3736
// ... has been XOR'd against a single character. Find the key, decrypt the message.
//
// You can do this by hand. But don't: write code to do it for you.
//
// How? Devise some method for "scoring" a piece of English plaintext. Character frequency is a good metric.
// Evaluate each output and choose the one with the best score.

//https://en.wikipedia.org/wiki/Letter_frequency
//n-gram?
//is it one char, or a repeated string of that char.  I would assume just one?
//the char should be the ascii value of it in binary
//ord(), ascii value of char
//XOR is reverse of XOR

$orderOfMostCommonCharsInEnglish = "etaoinshrdlcumwfgypbvkjxqz";
$encodedHexString = "1b37373331363f78151b7f2b783431333d78397828372d363c78373e783a393b3736";

$encodedStringBinary = $convertToBinary($encodedHexString, $hexAlpha);

//inital concept, just run down the most common char list
foreach (str_split($orderOfMostCommonCharsInEnglish) as $char) {
    $charBinary = $convertToBinary(ord($char), $base10Alpha);
    $reps = ceil(strlen($encodedStringBinary) / 8);
    $charBinaryString = $charBinary;
    for ($j = 0; $j < $reps; $j++) {
        $charBinaryString .= $charBinary;
    }
    // var_dump(strlen($charBinaryString), strlen($encodedStringBinary));
    $decodedBinary = $xOrBinaryStrings($encodedStringBinary, $charBinaryString);
    //convert this to ascii, readable english
    var_dump(strlen($decodedBinary));
    while (strlen($decodedBinary) % 8 != 0) {
        $decodedBinary = "0" . $decodedBinary;
    }
    $englishString = "";
    //grab in groups of 8 bits, convert to dec, look up in ascii chart or php function chr()
    while (strlen($decodedBinary) > 0) {
        $newCharBinary = substr($decodedBinary, -8);
        $asciiChar = chr($convertFromBinary($newCharBinary, $base10Alpha));
        $englishString = $asciiChar . $englishString;
// var_dump();
        $decodedBinary = substr($decodedBinary, 0, -8);
    }
    // var_dump($englishString);
}

 ?>
