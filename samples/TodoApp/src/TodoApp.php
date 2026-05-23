<?php

namespace Samples\TodoApp;

use Spwa\State\SessionStateManager;
use Spwa\State\StateManager;
use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\UI;
use Spwa\UI\Unit;
use Spwa\VNode\App;
use Spwa\VNode\VNode;

class TodoApp extends App
{
    public function title(): string
    {
        return 'TodoMVC - SPWA';
    }

    public function state(): StateManager
    {
        return new SessionStateManager();
    }

    protected function loader(): ?VNode
    {
        return UI::row()
            ->fixed()
            ->inset(Unit::px(0))
            ->layer(9999)
            ->alignCenter()
            ->alignMiddle()
            ->background(Color::black()->alpha(0.25))
            ->content(
                UI::text('Loading…')
                    ->background(Color::white())
                    ->padding(Unit::rem(0.75))
                    ->paddingX(Unit::rem(1.25))
                    ->rounded(Unit::rem(0.5))
                    ->color(Color::gray(700))
                    ->fontSize(FontSize::Small)
            );
    }

    protected function view(): VNode
    {
        return new TodoList();
    }
}
