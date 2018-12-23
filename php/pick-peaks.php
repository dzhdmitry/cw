<?php

// Pick peaks

function pickPeaks(array $arr)
{
    $plateauPeak = null;
    $result = [
        'pos' => [],
        'peaks' => []
    ];

    foreach ($arr as $i => $item) {
        if (!array_key_exists($i - 1, $arr) || !array_key_exists($i + 1, $arr)) {
            continue;
        }

        if ($arr[$i - 1] < $item && $arr[$i + 1] === $item) {
            $plateauPeak = [$i, $item];
        } elseif ($arr[$i - 1] < $item && $arr[$i + 1] < $item) {
            $result['pos'][] = $i;
            $result['peaks'][] = $arr[$i];
        } elseif ($arr[$i - 1] === $item && $arr[$i + 1] > $item) {
            $plateauPeak = null;
        } elseif ($arr[$i - 1] === $item && $arr[$i + 1] < $item) {
            if ($plateauPeak) {
                $result['pos'][] = $plateauPeak[0];
                $result['peaks'][] = $plateauPeak[1];
                $plateauPeak = null;
            }
        }
    }

    return $result;
}
