<?php

namespace Spwa\Dom;


class Levenshtein
{
    const DELETE = 0;
    const INSERT = 1;
    const SKIP = 2;
    const SUBSTITUTE = 3;

    /**
     * modified version of the levenshtein algorithm
     * @param string[] $arr1
     * @param string[] $arr2
     * @return array
     */
    static function populate(array $arr1, array $arr2): array
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
                $same = $arr1[$i - 1] == $arr2[$j - 1];

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

    /**
     * @template T
     * @param T[] $from
     * @param T[] $to
     * @param (callable(T $item): string|int)|null $key
     * @return array [int, T, T][]
     */
    static function diff(array $from, array $to, $key)
    {
        // convert array to keys
        $fromKeys = array_map($key, $from);
        $toKeys = array_map($key, $to);
        // then fo the levenshtein algorithm
        $dp = self::populate($fromKeys, $toKeys);
        // self::render($dp, $toKeys, $fromKeys);
        // transverse the dp arrays
        $i = count($fromKeys);
        $j = count($toKeys);

        $jm = [0, -1, -1, -1];
        $im = [-1, 0, -1, -1];

        $ret = [];
        $count = 20;
        while ($count > 0 && ($i > 0 || $j > 0)) {
            $action = $dp[$i][$j][1];
            $ret[] = [$action, $from[$i - 1], $to[$j - 1]];
            $i += $im[$action];
            $j += $jm[$action];
            $count--;
        }
        return $ret;
    }

    static function render($dp, $to, $from)
    {
        // render a table with the dp array
        echo "<table border='1'>";
        echo "<tr><td></td><td>-</td>";
        foreach ($to as $item) {
            echo "<td>" . $item . "</td>";
        }
        echo "</tr>";
        for ($i = 0; $i <= count($from); $i++) {
            echo "<tr>";
            if ($i == 0) {
                echo "<td>-</td>";
            } else {
                echo "<td>" . $from[$i - 1] . "</td>";
            }
            for ($j = 0; $j <= count($to); $j++) {
                echo "<td>{$dp[$i][$j][0]} , {$dp[$i][$j][1]}</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}