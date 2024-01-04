<?php
const SKIP = 0;
const REPLACE = 1;
const DELETE = 2;
const INSERT = 3;

class MatrixNode
{
    public int $action;
    public int $cost;
    public int $i;
    public int $j;

    /**
     * @param int $action
     * @param int $cost
     * @param int $i
     * @param int $j
     */
    public function __construct(int $action, int $cost, int $i, int $j)
    {
        $this->action = $action;
        $this->cost = $cost;
        $this->i = $i;
        $this->j = $j;
    }

    public static function fromMatrix(array $matrix, int $action, int $i, int $j): MatrixNode
    {
        return new MatrixNode($action, $matrix[$i][$j]->cost, $i, $j);
    }
}

function populateMatrix(array $arr1, array $arr2): array
{
    $len1 = count($arr1);
    $len2 = count($arr2);
    /** @var MatrixNode[][] $matrix */
    $matrix = [];

    $matrix[0][0] = new MatrixNode(SKIP, 0, 0, 0);

    for ($i = 1; $i <= $len1; $i++) {
        $matrix[$i][0] = new MatrixNode(DELETE, $i, $i - 1, 0);
    }
    for ($j = 1; $j <= $len2; $j++) {
        $matrix[0][$j] = new MatrixNode(INSERT, $j, 0, $j - 1);
    }

    for ($i = 1; $i <= $len1; $i++) {
        for ($j = 1; $j <= $len2; $j++) {
            $same = ($arr1[$i - 1] === $arr2[$j - 1]) ? 0 : 1;

            $actions = [
                MatrixNode::fromMatrix($matrix, $same == 0 ? SKIP : REPLACE, $i - 1, $j - 1),
                MatrixNode::fromMatrix($matrix, DELETE, $i - 1, $j),
                MatrixNode::fromMatrix($matrix, INSERT, $i, $j - 1)
            ];

            // take the first lowest cost
            usort($actions, fn($a, $b) => $a->cost - $b->cost);
            $take = $actions[0];
            $matrix[$i][$j] = $take;
            // all operations take 1 cost except skip
            $matrix[$i][$j]->cost += $take->action == SKIP ? 0 : 1;
        }
    }
    return $matrix;
}

/**
 * @param string[] $arr1
 * @param string[] $arr2
 * @return array
 */
function lavenshteinDiff(array $arr1, array $arr2): array
{
    $dp = populateMatrix($arr1, $arr2);
    $i = count($arr1);
    $j = count($arr2);
    $operations = [];
    while ($i > 0 || $j > 0) {
        $current = $dp[$i][$j];
        $operations[] = $current;
        $i = $current->i;
        $j = $current->j;
    }

    return array_reverse($operations);
}