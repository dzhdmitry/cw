<?php

// Adding words - Part II
// https://www.codewars.com/kata/adding-words-part-ii

class Arith
{
    private $number;

    private static $words = [
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'forty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        1000 => 'thousand',
    ];

    public function __construct(string $number)
    {
        $this->number = $this->wordToInt($number);
    }

    public function add(string $number): string
    {
        $result = $this->number + $this->wordToInt($number);

        return $this->intToWord($result);
    }

    private function wordToInt(string $word): int
    {
        $numbers = [];

        foreach (explode(' and ', $word) as $part) {
            $exploded = explode(' ', $part);

            if (count($exploded) === 2) {
                $first = $this->getInt($exploded[0]);
                $second = $this->getInt($exploded[1]);
                $numbers[] = (0 < $second && $second < 10) ? $first + $second : $first * $second;
            } elseif (count($exploded) === 1) {
                $numbers[] = $this->getInt($exploded[0]);
            }
        }

        return array_sum($numbers);
    }

    private function intToWord(int $n): string
    {
        if ($n < 21) {
            return self::$words[$n];
        }

        $order = min(pow(10, strlen($n) - 1), 1000);
        $base = intval(floor($n / $order));

        if ($order === 10) {
            $result = self::$words[$base * $order];
        } else {
            $result = $this->intToWord($base) . ' ' . self::$words[$order];
        }

        if ($n % $order) {
            $separator = ($order === 10) ? ' ' : ' and ';
            $result .= $separator . $this->intToWord($n % $order);
        }

        return $result;
    }

    private function getInt(string $word): int
    {
        $number = array_search($word, self::$words);

        if ($number === false) {
            throw new InvalidArgumentException();
        }

        return $number;
    }
}
