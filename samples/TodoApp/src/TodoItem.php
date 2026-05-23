<?php

namespace Samples\TodoApp;

use Closure;
use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\FontWeight;
use Spwa\UI\Pseudo;
use Spwa\UI\UI;
use Spwa\UI\UIElement;
use Spwa\UI\Unit;
use Spwa\VNode\Component;
use Spwa\VNode\VNode;

class TodoItem extends Component
{
    private bool $editing = false;
    private string $editText = '';

    public function __construct(
        private int     $id,
        private string  $text,
        private bool    $completed,
        private Closure $onToggle,
        private Closure $onDestroy,
        private Closure $onSave,
    ) {}

    protected function initialize(): void
    {
        $this->useState($this->editing);
        $this->useState($this->editText);
    }

    protected function build(): VNode
    {
        $row = UI::row()
            ->key((string)$this->id)
            ->alignMiddle()
            ->background(Color::white())
            ->paddingHorizontal(Unit::rem(1))
            ->paddingVertical(Unit::rem(0.75))
            ->borderTop()
            ->borderColor(Color::neutral(200));

        if ($this->editing) {
            return $row->content(
                $this->buildEditInput(),
                $this->buildSaveButton(),
                $this->buildCancelButton(),
            );
        }

        return $row->content(
            $this->buildCheckbox(),
            $this->buildLabel(),
            $this->buildDestroyButton(),
        );
    }

    private function buildCheckbox(): UIElement
    {
        $borderColor = $this->completed ? Color::green(100) : Color::neutral(400);

        return UI::row()
            ->center()
            ->size(Unit::rem(2))
            ->noShrink()
            ->bordered()
            ->borderColor($borderColor)
            ->roundedFull()
            ->clickable()
            ->on('click', fn() => ($this->onToggle)($this->id))
            ->content(
                $this->completed
                    ? UI::text('✓')
                        ->color(Color::teal(400))
                        ->fontSize(FontSize::Large)
                    : ''
            );
    }

    private function buildLabel(): UIElement
    {
        $label = UI::text($this->text)
            ->grow()
            ->paddingHorizontal(Unit::rem(0.5))
            ->fontSize(FontSize::TwoXL)
            ->weight(FontWeight::Light)
            ->on('dblclick', function () {
                $this->editing = true;
                $this->editText = $this->text;
            });

        if ($this->completed) {
            return $label
                ->color(Color::neutral(400))
                ->strikethrough();
        }

        return $label->color(Color::neutral(700));
    }

    private function buildDestroyButton(): UIElement
    {
        return UI::button('×')
            ->clickable()
            ->borderNone()
            ->background(Color::transparent())
            ->paddingHorizontal(Unit::rem(0.5))
            ->fontSize(FontSize::TwoXL)
            ->color(Color::rose(300))
            ->color(Color::rose(500), Pseudo::hover())
            ->on('click', fn() => ($this->onDestroy)($this->id));
    }

    private function buildEditInput(): UIElement
    {
        return UI::input()
            ->text()
            ->autofocus()
            ->bind($this->editText)
            ->grow()
            ->paddingHorizontal(Unit::rem(0.5))
            ->paddingVertical(Unit::rem(0.25))
            ->bordered()
            ->borderColor(Color::neutral(300))
            ->rounded(Unit::rem(0.25))
            ->outlineNone()
            ->fontSize(FontSize::TwoXL)
            ->weight(FontWeight::Light)
            ->color(Color::neutral(700));
    }

    private function buildSaveButton(): UIElement
    {
        return UI::button('✓')
            ->clickable()
            ->borderNone()
            ->background(Color::transparent())
            ->paddingHorizontal(Unit::rem(0.5))
            ->fontSize(FontSize::TwoXL)
            ->color(Color::teal(400))
            ->color(Color::teal(500), Pseudo::hover())
            ->on('click', function () {
                $next = trim($this->editText);
                if ($next !== '') {
                    ($this->onSave)($this->id, $next);
                }
                $this->editing = false;
                $this->editText = '';
            });
    }

    private function buildCancelButton(): UIElement
    {
        return UI::button('✗')
            ->clickable()
            ->borderNone()
            ->background(Color::transparent())
            ->paddingHorizontal(Unit::rem(0.5))
            ->fontSize(FontSize::TwoXL)
            ->color(Color::rose(300))
            ->color(Color::rose(500), Pseudo::hover())
            ->on('click', function () {
                $this->editing = false;
                $this->editText = '';
            });
    }
}
