<?php

namespace Spwa\UI\Examples;

use Spwa\UI\Align;
use Spwa\UI\Color;
use Spwa\UI\Direction;
use Spwa\UI\FontSize;
use Spwa\UI\Shadow;
use Spwa\UI\UI;
use Spwa\UI\UIElement;
use Spwa\UI\Unit;

/**
 * Album card example demonstrating the fluent UI API.
 *
 * This builds:
 * - Column layout on mobile, row on medium+ screens
 * - Album cover image with shadow
 * - Album info with title, artist, track info
 * - Responsive gap spacing
 * - Dark mode support for text colors
 */
class SampleUI
{
    public static function build(): UIElement
    {
        return UI::column()
            ->alignCenter()                              // items-center
            ->alignHorizontal(Align::left()->md())       // md:items-start
            ->gap(Unit::large(), Unit::extraLarge()->md()) // gap-6, md:gap-8
            ->padding(Unit::value(7))                     // p-7
            ->direction(Direction::row()->md())          // md:flex-row
            ->rounded(Unit::roundedXl())                 // rounded-2xl
            ->content(
                // Album cover
                UI::container()->content(
                    UI::image("/assets/images/logo.png", "logo")
//                        ->size(Unit::value(48))       // size-48
//                        ->shadow(Shadow::ExtraLarge) // shadow-xl
//                        ->rounded(Unit::rounded())   // rounded-md
                ),

                // Album info
                UI::column()
                    ->alignCenter()                          // items-center
                    ->alignHorizontal(Align::left()->md())   // md:items-start
                    ->content(
                        // Title
                        UI::text("BrickPHP")
                            ->medium(),

                        // Artist
                        UI::text("Spwa Team")
                            ->medium()
                            ->color(Color::sky(500)),

                        // Track info
                        UI::row()
                        ->gap(Unit::small())
                            ->content(
                                UI::text("No. 4")
                                    ->medium()
                                    ->color(
                                        Color::gray(600),
                                        Color::gray(400)->dark()
                                    ),
                                UI::text("·")
                                    ->medium()
                                    ->color(
                                        Color::gray(600),
                                        Color::gray(400)->dark()
                                    ),
                                UI::text("2025")
                                    ->medium()
                                    ->color(
                                        Color::gray(600),
                                        Color::gray(400)->dark()
                                    )
                            )
                    )
            );
    }
}
