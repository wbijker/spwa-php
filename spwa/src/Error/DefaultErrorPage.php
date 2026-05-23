<?php

namespace Spwa\Error;

use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\FontWeight;
use Spwa\UI\Shadow;
use Spwa\UI\UI;
use Spwa\UI\Unit;
use Spwa\VNode\Component;
use Spwa\VNode\VNode;

/**
 * Modern, centered error page composed entirely with the UI:: builder.
 */
class DefaultErrorPage extends Component
{
    public function __construct(
        private readonly ErrorInfo $info,
    ) {}

    protected function build(): VNode
    {
        $card = UI::column()
            ->maxWidth(Unit::px(760))
            ->extendX()
            ->background(Color::slate(800))
            ->bordered(1)
            ->borderColor(Color::slate(700))
            ->rounded(Unit::px(14))
            ->shadow(Shadow::TwoXL)
            ->padding(Unit::px(32))
            ->gap(Unit::px(20))
            ->alignLeft()
            ->content(
                UI::text(strtoupper($this->info->type))
                    ->fontSize(FontSize::ExtraSmall)
                    ->weight(FontWeight::Bold)
                    ->uppercase()
                    ->color(Color::red(200))
                    ->background(Color::red(900))
                    ->paddingX(Unit::px(10))
                    ->paddingY(Unit::px(4))
                    ->rounded(Unit::px(6))
                    ->shrink(),

                UI::text($this->info->message)
                    ->fontSize(FontSize::TwoXL)
                    ->semibold()
                    ->color(Color::slate(100)),

                UI::row()
                    ->extendX()
                    ->padding(Unit::px(14))
                    ->background(Color::slate(900))
                    ->bordered(1)
                    ->borderColor(Color::slate(700))
                    ->rounded(Unit::px(8))
                    ->content(
                        UI::code($this->info->file)
                            ->fontSize(FontSize::Small)
                            ->color(Color::slate(400)),
                        UI::code(':' . $this->info->line)
                            ->fontSize(FontSize::Small)
                            ->color(Color::amber(400)),
                    ),
            );

        if ($this->info->trace !== null && $this->info->trace !== '') {
            $card->content(
                UI::pre($this->info->trace)
                    ->extendX()
                    ->maxHeight(Unit::px(340))
                    ->scrollable()
                    ->padding(Unit::px(14))
                    ->background(Color::slate(900))
                    ->bordered(1)
                    ->borderColor(Color::slate(700))
                    ->rounded(Unit::px(8))
                    ->color(Color::slate(300))
                    ->fontSize(FontSize::ExtraSmall),
            );
        }

        return UI::row()
            ->center()
            ->minHeight(Unit::screen())
            ->extendX()
            ->background(Color::slate(900))
            ->padding(Unit::px(24))
            ->content($card);
    }
}
