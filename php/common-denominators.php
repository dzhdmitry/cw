<?php

// Common Denominators

function gcd($a, $b)
{
    while ($a != 0 && $b != 0) {
        if ($a > $b) {
            $a = $a % $b;
        } else {
            $b = $b % $a;
        }
    }

    return $a + $b;
}

function lcm($a, $b)
{
    if ($a === 0 || $b === 0) {
        return 0;
    }

    $gcd = gcd($a, $b);

    if ($gcd !== 0) {
        return ($a * $b) / $gcd;
    }

    return 0;
}

function lcmMany(array $values)
{
    if (count($values) === 0) {
        return 0;
    }

    if (count($values) === 1) {
        return $values[0];
    }

    $result = $values[0];

    foreach (array_slice($values, 1) as $value) {
        $result = lcm($result, $value);
    }

    return $result;
}

function convertFrac(array $lst): string
{
    $denominators = [];

    foreach ($lst as &$fraction) {
        $gcd = gcd($fraction[0], $fraction[1]);

        if ($gcd > 1) {
            $fraction = [$fraction[0] / $gcd, $fraction[1] / $gcd];
        }

        $denominators[] = $fraction[1];
    }

    $lcm = lcmMany($denominators);
    $result = '';

    foreach ($lst as list($number, $denominator)) {
        $result .= sprintf('(%d,%d)', ($lcm * $number) / $denominator, $lcm);
    }

    return $result;
}
