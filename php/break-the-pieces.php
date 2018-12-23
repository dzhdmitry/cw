<?php

// Break the pieces

class Point
{
    const UP = 'up';
    const RIGHT = 'right';
    const DOWN = 'down';
    const LEFT = 'left';

    /**
     * @var int
     */
    public $y;

    /**
     * @var int
     */
    public $x;

    public function __construct(int $y, int $x)
    {
        $this->y = $y;
        $this->x = $x;
    }

    public function __toString()
    {
        return $this->y . ':' . $this->x;
    }

    /**
     * @return Point
     */
    public function step()
    {
        $steps = func_get_args();
        $y = $this->y;
        $x = $this->x;

        foreach ($steps as $step) {
            if ($step === self::UP) {
                $y--;
            }

            if ($step === self::RIGHT) {
                $x++;
            }

            if ($step === self::DOWN) {
                $y++;
            }

            if ($step === self::LEFT) {
                $x--;
            }
        }

        return new static($y, $x);
    }

    /**
     * @return Point[]
     */
    public function generateSteps()
    {
        return [
            Point::UP => $this->step(Point::UP),
            Point::RIGHT => $this->step(Point::RIGHT),
            Point::DOWN => $this->step(Point::DOWN),
            Point::LEFT => $this->step(Point::LEFT)
        ];
    }
}

class Figure
{
    /**
     * @var Point[]
     */
    public $points = [];

    /**
     * @param Point $point
     */
    public function addPoint(Point $point)
    {
        $this->points[(string)$point] = $point;
    }

    /**
     * @param Point $point
     * @return bool
     */
    public function hasPoint(Point $point)
    {
        return array_key_exists((string)$point, $this->points);
    }
}

class Map
{
    /**
     * @var array
     */
    private $map;

    /**
     * @var int
     */
    private $length;

    public function __construct(array $map)
    {
        $this->map = $map;
        $this->length = count($map);
    }

    /**
     * @param Point $point
     * @return string|null
     */
    public function at(Point $point)
    {
        if ($point->y >= $this->length || $point->y < 0) {
            return null;
        }

        if ($point->x >= count($this->map[$point->y]) || $point->x < 0) {
            return null;
        }

        return $this->map[$point->y][$point->x];
    }

    /**
     * @param Point $point
     * @param string|null $value
     * @return bool
     */
    public function is(Point $point, string $value = null)
    {
        return $this->at($point) === $value;
    }

    /**
     * @param Point $point
     * @param string $value
     */
    public function set(Point $point, string $value)
    {
        if (!array_key_exists($point->y, $this->map)) {
            return;
        }

        if (!array_key_exists($point->x, $this->map[$point->y])) {
            return;
        }

        $this->map[$point->y][$point->x] = $value;
    }

    /**
     * @return string
     */
    public function getShrinkLines(): string
    {
        $lines = array_map(function($line) {
            return rtrim(implode('', $line));
        }, $this->map);

        return implode("\n", $lines);
    }

    /**
     * @param int $height
     * @param int $width
     * @return Map
     */
    public static function fromSize(int $height, int $width): Map
    {
        $map = array_fill(0, $height, array_fill(0, $width, ' '));

        return new static($map);
    }
}

class FigureMap extends Map
{
    /**
     * @var Figure[]
     */
    private $figures = [];

    /**
     * @param Point $point
     */
    public function addFigure(Point $point)
    {
        $this->figures[] = $figure = new Figure();

        $this->explore($point, $figure);
    }

    /**
     * @return string[]
     */
    public function getFigures(): array
    {
        $found = [];
        $result = [];

        foreach ($this->figures as $figure) {
            ksort($figure->points);

            $key = implode(',', $figure->points);

            if (in_array($key, $found)) {
                continue;
            }

            foreach ($figure->points as $point) {
                foreach ($point->generateSteps() as $step) {
                    if ($this->is($step, null)) {
                        continue 3;
                    }
                }
            }

            $found[] = $key;
            list($minY, $minX, $maxY, $maxX) = array_fill(0, 4, null);

            foreach ($figure->points as $point) {
                if ($minY === null || $point->y < $minY) {
                    $minY = $point->y;
                }

                if ($minX === null || $point->x < $minX) {
                    $minX = $point->x;
                }

                if ($maxY === null || $point->y > $maxY) {
                    $maxY = $point->y;
                }

                if ($maxX === null || $point->x > $maxX) {
                    $maxX = $point->x;
                }
            }

            $offsetY = $minY - 1;
            $offsetX = $minX - 1;
            $figureMap = Map::fromSize($maxY - $minY + 3, $maxX - $minX + 3);

            foreach ($figure->points as $point) {
                $steps = $point->generateSteps();
                $y = $point->y - $offsetY;
                $x = $point->x - $offsetX;

                foreach ($steps as $step => $p) {
                    $newPoint = new Point($p->y - $offsetY, $p->x - $offsetX);
                    $isUpDown = in_array($step, [Point::UP, Point::DOWN]);
                    $char = $isUpDown ? '-' : '|';

                    if ($this->is($p, $char)) {
                        $figureMap->set($newPoint, $char);
                    } elseif ($this->is($p, '+')) {
                        list($f, $s) = $isUpDown ? [Point::LEFT, Point::RIGHT] : [Point::UP, Point::DOWN];

                        if (!($this->is($p->step($f), $char) && $this->is($p->step($s), $char))) {
                            $char = '+';
                        }

                        $figureMap->set($newPoint, $char);
                    }
                }

                if ($this->is($steps[Point::UP], '-') && $this->is($steps[Point::LEFT], '|')) {
                    $figureMap->set(new Point($y - 1, $x - 1), '+');
                }

                if ($this->is($steps[Point::UP], '-') && $this->is($steps[Point::RIGHT], '|')) {
                    $figureMap->set(new Point($y - 1, $x + 1), '+');
                }

                if ($this->is($steps[Point::DOWN], '-') && $this->is($steps[Point::LEFT], '|')) {
                    $figureMap->set(new Point($y + 1, $x - 1), '+');
                }

                if ($this->is($steps[Point::DOWN], '-') && $this->is($steps[Point::RIGHT], '|')) {
                    $figureMap->set(new Point($y + 1, $x + 1), '+');
                }
            }

            $result[] = $figureMap->getShrinkLines();
        }

        return $result;
    }

    /**
     * @param Point $point
     * @param Figure $figure
     * @return int
     */
    private function explore(Point $point, Figure $figure)
    {
        $figure->addPoint($point);

        $rd = $point->step(Point::RIGHT, Point::DOWN);
        $area = 1;

        if ($this->is($rd, '+')) {
            foreach ([Point::RIGHT, Point::DOWN] as $step) {
                $p = $point->step($step);

                if (in_array($this->at($p), ['|', '-'])) {
                    $next = $p->step($step);

                    if (!$this->is($next, ' ')) {
                        break;
                    }

                    foreach ($this->figures as $f) {
                        if ($f->hasPoint($next)) {
                            continue 2;
                        }
                    }

                    $this->addFigure($next);
                }
            }

            if ($this->is($rd->step(Point::UP), '-') && $this->is($rd->step(Point::LEFT), '|')) {
                $this->addFigure($rd);
            }
        }

        foreach ($point->generateSteps() as $step) {
            if ($this->is($step, null) || $figure->hasPoint($step)) {
                continue;
            }

            if (!in_array($this->at($step), ['+', '-', '|'])) {
                $area += $this->explore($step, $figure);
            }
        }

        return $area;
    }
}

class BreakPieces
{
    /**
     * @param string $shape
     * @return array
     */
    public function process(string $shape): array
    {
        $lines = array_map(function($line) {
            return str_split($line);
        }, explode("\n", $shape));

        $map = new FigureMap($lines);
        $first = new Point(0, 0);

        if ($map->is($first, '+')) {
            $first = $first->step(Point::DOWN, Point::RIGHT);
        }

        $map->addFigure($first);

        return $map->getFigures();
    }
}
