<?php

namespace Samples\Docs\Components;

use BrickPHP\UI\Color;
use BrickPHP\UI\FontSize;
use BrickPHP\UI\FontWeight;
use BrickPHP\UI\Shadow;
use BrickPHP\UI\UI;
use BrickPHP\UI\Unit;
use BrickPHP\VNode\Component;
use BrickPHP\VNode\VNode;

class FeatureCard extends Component
{
    public function __construct(
        private string $title,
        private string $body,
        private string $emoji = '',
        private ?Color $accent = null,
    ) {
        $this->accent ??= Color::red(500);
    }

    protected function build(): VNode
    {
        return UI::column()
            ->background(Color::white())
            ->padding(Unit::large())
            ->rounded(Unit::roundedXl())
            ->shadow(Shadow::Small)
            ->borderTop(3)
            ->borderColor($this->accent)
            ->gap(Unit::small())
            ->content(
                UI::text($this->emoji)
                    ->fontSize(FontSize::TwoXL),
                UI::text($this->title)
                    ->fontSize(FontSize::Large)
                    ->weight(FontWeight::SemiBold)
                    ->color(Color::slate(900)),
                UI::text($this->body)
                    ->fontSize(FontSize::Small)
                    ->color(Color::slate(600)),
            );
    }
}
