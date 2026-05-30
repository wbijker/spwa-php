<?php

namespace Samples\Docs\Components;

use BrickPHP\UI\Color;
use BrickPHP\UI\FontSize;
use BrickPHP\UI\FontWeight;
use BrickPHP\UI\Pseudo;
use BrickPHP\UI\Shadow;
use BrickPHP\UI\UI;
use BrickPHP\UI\UIElement;
use BrickPHP\UI\Unit;
use BrickPHP\VNode\Component;
use BrickPHP\VNode\VNode;

/**
 * VS Code-style tabbed code viewer. Add files via `add($filename, $code)`;
 * clicking a tab swaps the visible source. State lives on the component so
 * the active tab survives renders.
 */
class CodeTabs extends Component
{
    /** @var array<int, array{name:string, code:string}> */
    private array $files = [];
    private int $active = 0;

    protected function initialize(): void
    {
        $this->useState($this->active);
    }

    public function add(string $name, string $code): static
    {
        $this->files[] = ['name' => $name, 'code' => $code];
        return $this;
    }

    protected function build(): VNode
    {
        $tabs = [];
        foreach ($this->files as $i => $file) {
            $tabs[] = $this->tab($file['name'], $i);
        }

        $current = $this->files[$this->active] ?? ['name' => '', 'code' => ''];

        return UI::column()
            ->rounded(Unit::roundedLg())
            ->overflow()
            ->shadow(Shadow::Large)
            ->background(Color::slate(900))
            ->content(
                // Title bar
                UI::row()
                    ->background(Color::slate(800))
                    ->paddingX(Unit::small())
                    ->paddingY(Unit::xs())
                    ->gap(Unit::xs())
                    ->alignMiddle()
                    ->content(
                        $this->dot(Color::red(500)),
                        $this->dot(Color::amber(500)),
                        $this->dot(Color::emerald(500)),
                    ),
                // Tab strip
                UI::row()
                    ->background(Color::slate(800))
                    ->borderTop(1)
                    ->borderColor(Color::slate(700))
                    ->content(...$tabs),
                // Code body
                UI::pre()
                    ->paddingX(Unit::large())
                    ->paddingY(Unit::medium())
                    ->background(Color::slate(900))
                    ->overflow()
                    ->content(
                        UI::code($current['code'])
                            ->color(Color::slate(100))
                            ->fontSize(FontSize::Small),
                    ),
            );
    }

    private function tab(string $name, int $index): UIElement
    {
        $isActive = $index === $this->active;

        $tab = UI::row()
            ->paddingX(Unit::medium())
            ->paddingY(Unit::small())
            ->gap(Unit::xs())
            ->alignMiddle()
            ->clickable()
            ->background(Color::slate(700), Pseudo::hover())
            ->onClick(fn() => $this->active = $index)
            ->content(
                UI::text($name)
                    ->fontSize(FontSize::ExtraSmall)
                    ->weight(FontWeight::Medium)
                    ->color($isActive ? Color::white() : Color::slate(400)),
            );

        if ($isActive) {
            $tab = $tab
                ->background(Color::slate(900))
                ->borderTop(2)
                ->borderColor(Color::red(500));
        }

        return $tab;
    }

    private function dot(Color $color): UIElement
    {
        return UI::container()
            ->width(Unit::px(10))
            ->height(Unit::px(10))
            ->roundedFull()
            ->background($color);
    }
}
