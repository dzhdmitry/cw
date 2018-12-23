<?php

// Battleship field validator

class Ship
{
    /**
     * @var array
     */
    private $points = [];

    /**
     * @param int $y
     * @param int $x
     */
    public function addPoint(int $y, int $x)
    {
        $this->points[] = [$y, $x];
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return count($this->points);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if (count($this->points) === 0 || count($this->points) > 4) {
            return false;
        }

        $xs = [];
        $ys = [];

        foreach ($this->points as list($y, $x)) {
            $xs[] = $x;
            $ys[] = $y;
        }

        return count(array_unique($xs)) === 1 || count(array_unique($ys)) === 1;
    }
}

class BattleMap
{
    const EXPLORING_TRACKED = 'tracked';
    const EXPLORING_SKIPPED = 'skipped';
    const EXPLORING_INVALID = 'invalid';

    /**
     * @var array
     */
    private $map;

    /**
     * @var int
     */
    private $length;

    /**
     * @var array
     */
    private $tracked = [];

    /**
     * @var Ship[]
     */
    private $ships = [];

    public function __construct(array $map)
    {
        $this->map = $map;
        $this->length = count($map);
    }

    /**
     * @param int $y
     * @param int $x
     * @return int|null
     */
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

    /**
     * @param int $y
     * @param int $x
     * @return bool
     */
    public function hasTracked(int $y, int $x): bool
    {
        return in_array($y . ':' . $x, $this->tracked);
    }

    /**
     * @param int $y
     * @param int $x
     */
    public function addTracked(int $y, int $x)
    {
        $this->tracked[] = $y . ':' . $x;
    }

    /**
     * @param int $y
     * @param int $x
     * @param Ship|null $ship
     * @return string
     */
    public function explore(int $y, int $x, Ship $ship = null): string
    {
        if ($this->at($y, $x) !== 1) {
            return self::EXPLORING_SKIPPED;
        }

        if ($this->hasTracked($y, $x)) {
            return self::EXPLORING_SKIPPED;
        }

        if (!$ship) {
            $this->ships[] = $ship = new Ship();
        }

        $ship->addPoint($y, $x);
        $this->addTracked($y, $x);

        for ($yN=$y-1; $yN<$y+2; $yN++) {
            for ($xN=$x-1; $xN<$x+2; $xN++) {
                if ($yN === $y || $xN === $x) {
                    if ($this->explore($yN, $xN, $ship) === self::EXPLORING_INVALID) {
                        return self::EXPLORING_INVALID;
                    }
                } else {
                    if ($this->at($yN, $xN) === 1) {
                        return self::EXPLORING_INVALID;
                    }
                }
            }
        }

        return self::EXPLORING_TRACKED;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $amounts = [
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];

        foreach ($this->ships as $ship) {
            if (!$ship->isValid()) {
                return false;
            }

            $amounts[$ship->getAmount()] += 1;
        }

        return $amounts === [
            4 => 1,
            3 => 2,
            2 => 3,
            1 => 4,
        ];
    }
}

function validate_battlefield(array $field): bool
{
    $map = new BattleMap($field);

    foreach ($field as $y => $line) {
        foreach ($line as $x => $item) {
            if ($map->explore($y, $x) === BattleMap::EXPLORING_INVALID) {
                return false;
            }
        }
    }

    return $map->isValid();
}
