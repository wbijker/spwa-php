<?php

namespace Spwa\UI\Examples;

use Closure;
use Spwa\Js\Console;
use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\UI;
use Spwa\UI\Unit;
use Spwa\VNode\Component;
use Spwa\VNode\VNode;

/**
 * A simple counter component with increment and decrement buttons.
 * Accepts value and change handler from parent.
 */
class Counter extends Component
{
    public function __construct(
        private int $value,
        private Closure $onChange
    ) {}

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
                        ($this->onChange)($this->value - 1);
                    }),

                UI::text((string)$this->value)
                    ->padding(Unit::rem(0.5), Unit::rem(1))
                    ->fontSize(FontSize::TwoXL)
                    ->semibold(),

                UI::button('+')
                    ->padding(Unit::rem(0.5), Unit::rem(1))
                    ->background(Color::green(500))
                    ->color(Color::white())
                    ->rounded(Unit::rem(0.25))
                    ->on('click', function() {
                        ($this->onChange)($this->value + 1);
                    })
            );
    }
}
