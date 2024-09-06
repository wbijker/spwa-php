<?php

namespace Spwa\Dom;


class Levenshtein
{
    const DELETE = 0;
    const INSERT = 1;
    const SKIP = 2;
    const SUBSTITUTE = 3;

    // modified version of the levenshtein algorithm
    static function populate(array $arr1, array $arr2, callable $key): array
    {
        $n = count($arr1);
        $m = count($arr2);
        $dp = array_fill(0, $n + 1, array_fill(0, $m + 1, 0));

        for ($i = 0; $i <= $n; $i++) {
            for ($j = 0; $j <= $m; $j++) {
                if ($i == 0) {
                    $dp[$i][$j] = [$j, self::INSERT];
                    continue;
                }
                if ($j == 0) {
                    $dp[$i][$j] = [$i, self::DELETE];
                    continue;
                }
                $same = $key($arr1[$i - 1]) == $key($arr2[$j - 1]);

                $neighbours = [
                    [$dp[$i - 1][$j - 1][0] + ($same ? 0 : 1), ($same ? self::SKIP : self::SUBSTITUTE)],
                    [$dp[$i - 1][$j][0], self::DELETE],
                    [$dp[$i][$j - 1][0], self::INSERT]
                ];

                // take the minimum of the neighbours
                $min = array_reduce($neighbours, fn($acc, $item) => $item[0] < $acc[0] ? $item : $acc, $neighbours[0]);
                $dp[$i][$j] = [$min[0] + ($same ? 0 : 1), $min[1]];
            }
        }
        return $dp;
    }

    static function debug(array $from, array $to, callable $key)
    {
        $dp = self::populate($from, $to, $key);
        // render a table with the dp array
        echo "<table border='1'>";
        echo "<tr><td></td><td>-</td>";
        foreach ($to as $item) {
            echo "<td>$item[0]</td>";
        }
        echo "</tr>";
        for ($i = 0; $i <= count($from); $i++) {
            echo "<tr>";
            if ($i == 0) {
                echo "<td>-</td>";
            } else {
                echo "<td>{$from[$i - 1][0]}</td>";
            }
            for ($j = 0; $j <= count($to); $j++) {
                echo "<td>{$dp[$i][$j][0]} , {$dp[$i][$j][1]}</td>";
            }
            echo "</tr>";
        }
        echo "</table>";

        $i = count($from);
        $j = count($to);

        while ($i > 0 || $j > 0) {
            $node = $dp[$i][$j];
            print_r($node);

            switch ($node[1]) {
                case self::INSERT:
                    echo "Insert {$to[$j - 1]}<br>";
                    $j--;
                    break;
                case self::DELETE:
                    echo "delete {$from[$i-1]}<br>";
                    $i--;
                    break;
                case self::SUBSTITUTE:
                    echo "Sub {$from[$i-1]} with {$to[$j-1]}<br>";
                    $i--;
                    $j--;
                    break;
                case self::SKIP:
                    echo "SKIP {$to[$j - 1]}<br>";
                    $i--;
                    $j--;
                    break;
            }
        }
    }


}