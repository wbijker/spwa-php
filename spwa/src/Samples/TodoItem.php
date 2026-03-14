<?php

namespace Spwa\Samples;

use Closure;
use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\FontWeight;
use Spwa\UI\UI;
use Spwa\UI\Unit;
use Spwa\VNode\Component;
use Spwa\VNode\VNode;

class TodoItem extends Component
{
    public function __construct(
        private int     $id,
        private string  $text,
        private bool    $completed,
        private Closure $onToggle,
        private Closure $onDestroy,
    ) {}

    protected function build(): VNode
    {
        return UI::row()
            ->alignMiddle()
            ->background(Color::white())
            ->paddingHorizontal(Unit::rem(1))
            ->paddingVertical(Unit::rem(0.75))
            ->borderTop()
            ->borderColor(Color::hex('#ededed'))
            ->content(
                $this->buildCheckbox(),
                $this->buildLabel(),
                $this->buildDestroyButton(),
            );
    }

    private function buildCheckbox(): \Spwa\UI\UIElement
    {
        $borderColor = $this->completed ? '#d9fad5' : '#949494';

        return UI::row()
            ->center()
            ->size(Unit::rem(2))
            ->noShrink()
            ->bordered()
            ->borderColor(Color::hex($borderColor))
            ->roundedFull()
            ->clickable()
            ->on('click', fn() => ($this->onToggle)($this->id))
            ->content(
                $this->completed
                    ? UI::text('✓')
                        ->color(Color::hex('#5dc2af'))
                        ->fontSize(FontSize::Large)
                    : ''
            );
    }

    private function buildLabel(): \Spwa\UI\UIElement
    {
        $label = UI::text($this->text)
            ->grow()
            ->paddingHorizontal(Unit::rem(0.5))
            ->fontSize(FontSize::TwoXL)
            ->weight(FontWeight::Light);

        if ($this->completed) {
            return $label
                ->color(Color::hex('#949494'))
                ->strikethrough();
        }

        return $label->color(Color::hex('#484848'));
    }

    private function buildDestroyButton(): \Spwa\UI\UIElement
    {
        return UI::button('×')
            ->clickable()
            ->borderNone()
            ->background(Color::transparent())
            ->paddingHorizontal(Unit::rem(0.5))
            ->fontSize(FontSize::TwoXL)
            ->color(Color::hex('#cc9a9a'), Color::hex('#af5b5e')->hover())
            ->on('click', fn() => ($this->onDestroy)($this->id));
    }
}
