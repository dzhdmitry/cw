<?php

// Valid Braces
// https://www.codewars.com/kata/valid-braces

function validBraces(string $braces): bool
{
    $stack = [];

    foreach (str_split($braces) as $char) {
        if (in_array($char, ['(', '{', '['])) {
            $stack[] = $char;
        } elseif (in_array($char, [')', '}', ']'])) {
            if (count($stack) === 0) {
                return false;
            }

            $last = array_pop($stack);

            if ($char === ')' && $last !== '(' || $char === '}' && $last !== '{' || $char === ']' && $last !== '[') {
                return false;
            }
        } else {
            return false;
        }
    }

    return count($stack) === 0;
}
