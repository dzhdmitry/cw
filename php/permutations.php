<?php

// Permutations
// https://www.codewars.com/kata/permutations

class Letter
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var Letter[]
     */
    private $children;

    public function __construct(string $value, array $children = [])
    {
        $this->value = $value;
        $this->children = $children;
    }

    public function getStrings()
    {
        switch (count($this->children)) {
            case 0:
                $result = $this->value;

                break;
            case 1:
                $result = $this->value . $this->children[0]->getStrings();

                break;
            default:
                $result = [];

                foreach ($this->children as $letter) {
                    $s = $letter->getStrings();

                    if (is_array($s)) {
                        foreach ($s as $item) {
                            $result[] = $this->value . $item;
                        }
                    } else {
                        $result[] = $this->value . $s;
                    }
                }
        }

        return $result;
    }
}

function compose(array $values): array
{
    $letters = [];

    foreach ($values as $i => $value) {
        $begin = array_slice($values, 0, $i);
        $end = array_slice($values, $i + 1);
        $other = array_merge($begin, $end);

        $letters[] = new Letter($value, compose($other));
    }

    return $letters;
}

function permutations(string $s): array
{
    $letter = new Letter('', compose(str_split($s)));

    return array_unique((array)$letter->getStrings());
}
