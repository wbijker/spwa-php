<?php

namespace Samples\Site\Components;

use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\UI;
use Spwa\UI\Unit;
use Spwa\VNode\Component;
use Spwa\VNode\VNode;

class CodeBlock extends Component
{
    public function __construct(
        private string $code,
        private string $label = 'PHP',
    ) {}

    protected function build(): VNode
    {
        return UI::column()
            ->background(Color::slate(800))
            ->rounded(Unit::roundedLg())
            ->clipContent()
            ->content(
                UI::row()
                    ->background(Color::slate(700))
                    ->paddingHorizontal(Unit::medium())
                    ->paddingVertical(Unit::xs())
                    ->content(
                        UI::text($this->label)
                            ->fontSize(FontSize::ExtraSmall)
                            ->color(Color::slate(300))
                            ->mono()
                    ),
                UI::pre()
                    ->padding(Unit::medium())
                    ->content(
                        UI::code()
                            ->fontSize(FontSize::Small)
                            ->color(Color::emerald(300))
                            ->content($this->code)
                    )
            );
    }
}
