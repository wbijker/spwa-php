<?php

namespace Spwa\Samples\site\Components;

use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\FontWeight;
use Spwa\UI\Shadow;
use Spwa\UI\UI;
use Spwa\UI\Unit;
use Spwa\VNode\Component;
use Spwa\VNode\VNode;

class DemoCard extends Component
{
    /**
     * @param string $title
     * @param string $description
     * @param VNode $demo        The live interactive demo
     * @param string $code       The PHP source code
     */
    public function __construct(
        private string $title,
        private string $description,
        private VNode  $demo,
        private string $code,
    ) {}

    protected function build(): VNode
    {
        return UI::column()
            ->background(Color::white())
            ->rounded(Unit::roundedXl())
            ->shadow(Shadow::Medium)
            ->clipContent()
            ->content(
                // Header
                UI::column()
                    ->padding(Unit::large())
                    ->gap(Unit::xs())
                    ->content(
                        UI::text($this->title)
                            ->fontSize(FontSize::Large)
                            ->weight(FontWeight::SemiBold)
                            ->color(Color::slate(800)),
                        UI::text($this->description)
                            ->fontSize(FontSize::Small)
                            ->color(Color::slate(500)),
                    ),

                // Live demo area
                UI::column()
                    ->background(Color::slate(50))
                    ->padding(Unit::large())
                    ->borderTop()
                    ->borderBottom()
                    ->borderColor(Color::slate(200))
                    ->content($this->demo),

                // Code
                new CodeBlock($this->code),
            );
    }
}
