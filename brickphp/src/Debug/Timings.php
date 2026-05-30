<?php

namespace BrickPHP\Debug;

/**
 * Lightweight sequential section timer.
 *
 *   $t = new Timings();
 *   doParse();      $t->mark('parse');
 *   renderOld();    $t->mark('render_old');
 *   $t->all();      // → ['parse' => 1.23, 'render_old' => 4.56, ...]
 *
 * Each `mark()` records elapsed milliseconds since the previous mark (or
 * since construction for the first one). Values are floats rounded to two
 * decimal places.
 */
final class Timings
{
    private float $previous;

    /** @var array<string, float> */
    private array $marks = [];

    public function __construct()
    {
        $this->previous = microtime(true);
    }

    public function mark(string $section): void
    {
        $now = microtime(true);
        $this->marks[$section] = round(($now - $this->previous) * 1000, 2);
        $this->previous = $now;
    }

    /** @return array<string, float> */
    public function all(): array
    {
        return $this->marks;
    }
}
