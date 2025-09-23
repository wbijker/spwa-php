<?php

namespace Spwa\UI;

class SampleUI
{
    static function build(): Element
    {
        return UI::rows()
            ->alignCenter()
            ->maxWidth(Unit::sm())
            ->padding(Unit::value(5))
            ->radius(Unit::xl())
            ->shadow(Unit::xl(), Unit::none()->dark())
            ->background(Color::white(), Color::slate(800)->dark(), Color::green(300)->hover())
            ->outline(Unit::single(), Unit::value(-1)->dark())
            ->outlineColor(Color::black(), Color::black(5)->dark())
            ->children([
                UI::image("/img/logo.svg", "ChitChat Logo")
                    ->size(Unit::value(12))
                    ->shrink(Unit::value(0)),

                Ui::cols()->children([
                    Ui::text("Chitchat")
                        ->textXl()
                        ->fontMedium()
                        ->color(Color::black(), dark: Color::white()),

                    Ui::text("You have a new message!")
                        ->color(Color::gray(500), dark: Color::gray(400))
                ])
            ]);

    }
}