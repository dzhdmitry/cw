<?php

// Pascal's Triangle

function pascals_triangle(int $n): array
{
    $result = [];

    for ($i=0; $i<$n; $i++) {
        $value = [1];

        if ($i !== 0) {
            $previous = array_slice($result, count($result) - $i, $i);

            for ($j=0; $j < count($previous)-1; $j++) {
                $value[] = $previous[$j] + $previous[$j + 1];
            }

            $value[] = 1;
        }

        $result = array_merge($result, $value);
    }

    return $result;
}
