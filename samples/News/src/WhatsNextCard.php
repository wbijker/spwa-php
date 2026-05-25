<?php

namespace Samples\News;

use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\FontWeight;
use Spwa\UI\Pseudo;
use Spwa\UI\Shadow;
use Spwa\UI\UI;
use Spwa\UI\Unit;
use Spwa\VNode\Stateless;
use Spwa\VNode\VNode;

/**
 * Compact card inside the "What's Next" horizontal strip — fixed
 * width, small image, category + title only.
 */
class WhatsNextCard extends Stateless
{
    public function __construct(private WhatsNextItem $item) {}

    protected function build(): VNode
    {
        $i = $this->item;

        return UI::column()
            ->background(Color::white())
            ->rounded(Unit::rem(0.375))
            ->overflow()
            ->shadow(Shadow::Small)
            ->shadow(Shadow::Medium, Pseudo::hover())
            ->animated()
            ->clickable()
            ->width(Unit::px(220))
            ->noShrink()
            ->content(
                UI::image($i->coverImage, $i->title)
                    ->width(Unit::full())
                    ->height(Unit::px(120))
                    ->objectCover(),
                UI::column()
                    ->padding(Unit::rem(0.75))
                    ->gap(Unit::rem(0.375))
                    ->content(
                        News::categoryLabel($i->category),
                        UI::text($i->title)
                            ->fontSize(FontSize::Small)
                            ->weight(FontWeight::SemiBold)
                            ->color(Color::gray(900))
                            ->color(Color::red(600), Pseudo::hover()),
                    )
            );
    }
}
