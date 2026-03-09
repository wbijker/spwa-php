<?php

namespace Spwa\UI\Examples;

use Spwa\Js\Console;
use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\UI;
use Spwa\UI\Unit;
use Spwa\VNode\Component;
use Spwa\VNode\VNode;

/**
 * A simple counter component with increment and decrement buttons.
 */
class Counter extends Component
{
    private int $count = 0;

    protected function getState(): array
    {
        return ['count' => $this->count];
    }

    protected function setState(array $state): void
    {
        $this->count = $state['count'] ?? 0;
    }

    protected function build(): VNode
    {
        return UI::row()
            ->gap(Unit::rem(1))
            ->padding(Unit::rem(1))
            ->background(Color::gray(100))
            ->rounded(Unit::rem(0.5))
            ->content(
                UI::button('-')
                    ->padding(Unit::rem(0.5), Unit::rem(1))
                    ->background(Color::red(500))
                    ->color(Color::white())
                    ->rounded(Unit::rem(0.25))
                    ->on('click', function() {
                        $this->count--;
                        Console::log("We have decremented the count to {$this->count}");
                    }),

                UI::text((string)$this->count)
                    ->padding(Unit::rem(0.5), Unit::rem(1))
                    ->fontSize(FontSize::TwoXL)
                    ->semibold(),

                UI::button('+')
                    ->padding(Unit::rem(0.5), Unit::rem(1))
                    ->background(Color::green(500))
                    ->color(Color::white())
                    ->rounded(Unit::rem(0.25))
                    ->on('click', function() {
                        $this->count++;
                        Console::log("We have incremented the count to {$this->count}");
                    })
            );
    }
}
