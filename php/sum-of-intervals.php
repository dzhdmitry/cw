<?php

// Sum of Intervals

function sum_intervals(array $intervals): int
{
    $passed = [];
    $sum = 0;

    foreach ($intervals as list($from, $to)) {
        if ($from === $to) {
            continue;
        }

        foreach (range($from, $to - 1) as $value) {
            if (in_array($value, $passed)) {
                continue;
            }

            $passed[] = $value;
            $sum++;
        }
    }

    return $sum;
}
