<?php

namespace Spwa\Dom;


class Levenshtein
{
    // modified version of the levenshtein algorithm
    static function populate(array $arr1, array $arr2, callable $key): array
    {
        $n = count($arr1);
        $m = count($arr2);
        $dp = array_fill(0, $n + 1, array_fill(0, $m + 1, 0));
        for ($i = 0; $i <= $n; $i++) {
            for ($j = 0; $j <= $m; $j++) {
                if ($i == 0) {
                    $dp[$i][$j] = $j;
                    continue;
                }
                if ($j == 0) {
                    $dp[$i][$j] = $i;
                    continue;
                }
                // if they are the same, skip, if not substitute
                $cost = $key($arr1[$i - 1]) == $key($arr2[$j - 1]) ? 0 : 1;
                $dp[$i][$j] = min(
                    $dp[$i - 1][$j] + 1,            // Deletion
                    $dp[$i][$j - 1] + 1,            // Insertion
                    $dp[$i - 1][$j - 1] + $cost    // Skip or Substitution
                );
            }
        }
        return $dp;
    }

    static function debug(array $arr1, array $arr2, callable $key)
    {
        $dp = self::populate($arr1, $arr2, $key);
        // render a table with the dp array
        echo "<table border='1'>";
        echo "<tr><td></td><td>-</td>";
        foreach ($arr2 as $item) {
            echo "<td>$item</td>";
        }
        echo "</tr>";
        for ($i = 0; $i <= count($arr1); $i++) {
            echo "<tr>";
            if ($i == 0) {
                echo "<td>-</td>";
            } else {
                echo "<td>{$arr1[$i - 1]}</td>";
            }
            for ($j = 0; $j <= count($arr2); $j++) {
                echo "<td>{$dp[$i][$j]}</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }


}