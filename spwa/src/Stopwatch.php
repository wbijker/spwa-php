<?php

namespace Spwa;

class Stopwatch
{
    private float $startTime;
    private float $endTime;
    private bool $running = false;

    public function start(): void
    {
        $this->startTime = microtime(true);
        $this->running = true;
    }

    public function stop(): void
    {
        if ($this->running) {
            $this->endTime = microtime(true);
            $this->running = false;
        }
    }

    public function elapsed(): float
    {
        return (($this->running ? microtime(true) : $this->endTime) - $this->startTime) * 1000;
    }

    public function reset(): void
    {
        $this->startTime = 0;
        $this->endTime = 0;
        $this->running = false;
    }
}