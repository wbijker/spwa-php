<?php

namespace Spwa\UI\Examples;

use Spwa\UI\AlignYValue;
use Spwa\UI\BaseElement;
use Spwa\UI\Color;
use Spwa\UI\DirectionValue;
use Spwa\UI\UI;
use Spwa\UI\Unit;

/**
 * Album card example matching the provided HTML:
 *
 * <div class="flex flex-col items-center gap-6 p-7 md:flex-row md:gap-8 rounded-2xl">
 *   <div>
 *     <img class="size-48 shadow-xl rounded-md" alt="" src="/img/cover.png" />
 *   </div>
 *   <div class="flex items-center md:items-start">
 *     <span class="text-2xl font-medium">Class Warfare</span>
 *     <span class="font-medium text-sky-500">The Anti-Patterns</span>
 *     <span class="flex gap-2 font-medium text-gray-600 dark:text-gray-400">
 *       <span>No. 4</span>
 *       <span>·</span>
 *       <span>2025</span>
 *     </span>
 *   </div>
 * </div>
 */
class AlbumCard
{
    public static function build(): BaseElement
    {
        return UI::flex()
            ->column()                                      // flex-col
            ->itemsCenter(AlignYValue::start()->md())       // items-center md:items-start
            ->gap(Unit::sizeLg(), Unit::sizeXl()->md())     // gap-6 md:gap-8
            ->padding(Unit::px(28))                         // p-7 (7 * 4 = 28px)
            ->direction(DirectionValue::row()->md())        // md:flex-row
            ->rounded(Unit::roundedXl())                    // rounded-2xl
            ->children(
                // Album cover container
                UI::element()
                    ->children(
                        UI::image("/img/cover.png", "")
                            ->width(Unit::px(192))          // size-48 = 12rem = 192px
                            ->height(Unit::px(192))
                            ->shadowXl()                    // shadow-xl
                            ->rounded(Unit::rounded())      // rounded-md
                    ),

                // Album info container
                UI::flex()
                    ->column()                              // flex flex-col
                    ->itemsCenter(AlignYValue::start()->md()) // items-center md:items-start
                    ->children(
                        // Title
                        UI::text("Class Warfare")
                            ->text2xl()                     // text-2xl
                            ->medium(),                     // font-medium

                        // Artist
                        UI::text("The Anti-Patterns")
                            ->medium()                      // font-medium
                            ->color(Color::sky(500)),       // text-sky-500

                        // Track info row
                        UI::flex()
                            ->row()
                            ->gap(Unit::sizeSm())           // gap-2
                            ->children(
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
