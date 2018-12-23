<?php

// Roman Numerals Decoder
// https://www.codewars.com/kata/roman-numerals-decoder

function solution(string $roman): int
{
    if (strlen($roman) === 0) {
        return 0;
    }

    $chars = str_split($roman);
    $number = 0;
    $skip = -1;
    $values = [
        'I' => 1,
        'V' => 5,
        'X' => 10,
        'L' => 50,
        'C' => 100,
        'D' => 500,
        'M' => 1000
    ];

    foreach ($chars as $i => $char) {
        if ($i === $skip) {
            continue;
        }

        if (
            array_key_exists($i + 1, $chars) && (
                ($char === 'I' && in_array($chars[$i + 1], ['V', 'X'])) ||
                ($char === 'X' && in_array($chars[$i + 1], ['L', 'C'])) ||
                ($char === 'C' && in_array($chars[$i + 1], ['D', 'M']))
            )
        ) {
            $number += $values[$chars[$i + 1]] - $values[$char];
            $skip = $i + 1;
        } else {
            $number += $values[$char];
        }
    }

    return $number;
}
