<?php

// Conway's Game of Life - Unlimited Edition
// https://www.codewars.com/kata/conways-game-of-life-unlimited-edition

class Life
{
    private $map;

    private $length;

    public function __construct(array $map)
    {
        $this->setMap($map);
    }

    /**
     * @return mixed
     */
    public function getMap(): array
    {
        return $this->map;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function at(int $y, int $x)
    {
        if ($y >= $this->length || $y < 0) {
            return null;
        }

        if ($x >= count($this->map[$y]) || $x < 0) {
            return null;
        }

        return $this->map[$y][$x];
    }

    public function setMap(array $map): Life
    {
        $this->map = $map;
        $this->length = count($map);

        return $this;
    }

    public function wrap(): Life
    {
        $size = count($this->map) ? count($this->map[0]) : 0;
        $empty = array_fill(0, $size + 2, 0);
        $map = [$empty];

        foreach ($this->map as $line) {
            $map[] = array_merge([0], array_values($line), [0]);
        }

        $map[] = $empty;

        return $this->setMap($map);
    }

    public function unwrap(): Life
    {
        $map = $this->map;

        while (self::lineIsDead($map, 0)) {
            array_shift($map);
        }

        while (self::lineIsDead($map, count($map) - 1)) {
            array_pop($map);
        }

        while (count($map) && self::columnIsDead($map, 0)) {
            $map = array_map(function($line) {
                return array_slice($line, 1, count($line) - 1);
            }, $map);
        }

        while (count($map) && self::columnIsDead($map, count($map[0]) - 1)) {
            $map = array_map(function($line) {
                return array_slice($line, 0, count($line) - 1);
            }, $map);
        }

        return $this->setMap(array_map('array_values', $map));
    }

    public function aliveNeighboursAmount(int $y, int $x): int
    {
        $fromY = $y - 1;
        $fromX = $x - 1;
        $toY = $y + 1;
        $toX = $x + 1;
        $amount = 0;

        for ($i=$fromY; $i<=$toY; $i++) {
            for ($j=$fromX; $j<=$toX; $j++) {
                if (!($i === $y && $j === $x) && ($this->at($i, $j) === 1)) {
                    $amount++;
                }
            }
        }

        return $amount;
    }

    private static function columnIsDead(array $map, int $index): bool
    {
        foreach ($map as $line) {
            if (array_key_exists($index, $line) && $line[$index] === 1) {
                return false;
            }
        }

        return true;
    }

    public static function lineIsDead(array $map, int $index): bool
    {
        return array_key_exists($index, $map) && array_search(1, $map[$index]) === false;
    }
}

function get_generation(array $cells, int $generations): array
{
    if (count($cells) < 2 || count($cells[0]) < 2) {
        return $cells;
    }

    $map = new Life($cells);

    for ($i=0; $i<$generations; $i++) {
        $map->wrap();

        $nextGeneration = $map->getMap();

        foreach ($map->getMap() as $y => $line) {
            foreach ($line as $x => $alive) {
                $neighboursAmount = $map->aliveNeighboursAmount($y, $x);
                $mustLive = $alive ? in_array($neighboursAmount, [2, 3]) : ($neighboursAmount == 3);
                $nextGeneration[$y][$x] = $mustLive ? 1 : 0;
            }
        }

        $map->setMap($nextGeneration)->unwrap();
    }

    return count($map->getMap()) ? $map->getMap() : [[]];
}
