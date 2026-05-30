<?php

namespace BrickPHP\UI\Examples;

use Closure;
use BrickPHP\Js\Console;
use BrickPHP\UI\Color;
use BrickPHP\UI\FontSize;
use BrickPHP\UI\UI;
use BrickPHP\UI\Unit;
use BrickPHP\VNode\Component;
use BrickPHP\VNode\VNode;

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

    protected function shouldRender(Component $old): bool
    {
        return true;
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
                    ->paddingY(Unit::rem(0.5))
                    ->paddingX(Unit::rem(1))
                    ->background(Color::red(500))
                    ->color(Color::white())
                    ->rounded(Unit::rem(0.25))
                    ->on('click', function() {
                        ($this->onChange)($this->value - 1);
                    }),

                UI::text((string)$this->value)
                    ->paddingY(Unit::rem(0.5))
                    ->paddingX(Unit::rem(1))
                    ->fontSize(FontSize::TwoXL)
                    ->semibold(),

                UI::button('+')
                    ->paddingY(Unit::rem(0.5))
                    ->paddingX(Unit::rem(1))
                    ->background(Color::green(500))
                    ->color(Color::white())
                    ->rounded(Unit::rem(0.25))
                    ->on('click', function() {
                        ($this->onChange)($this->value + 1);
                    })
            );
    }
}
