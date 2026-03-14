<?php

namespace Samples\Site\Components;

use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\FontWeight;
use Spwa\UI\UI;
use Spwa\UI\Unit;
use Spwa\VNode\Component;
use Spwa\VNode\VNode;

class SectionHeading extends Component
{
    public function __construct(
        private string $title,
        private string $subtitle = '',
    ) {}

    protected function build(): VNode
    {
        $nodes = [
            UI::text($this->title)
                ->fontSize(FontSize::ThreeXL)
                ->weight(FontWeight::Bold)
                ->color(Color::slate(900)),
        ];

        if ($this->subtitle !== '') {
            $nodes[] = UI::text($this->subtitle)
                ->fontSize(FontSize::Base)
                ->color(Color::slate(500));
        }

        return UI::column()
            ->gap(Unit::small())
            ->paddingVertical(Unit::large())
            ->content(...$nodes);
    }
}
