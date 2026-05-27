<?php

namespace Samples\News;

use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\FontWeight;
use Spwa\UI\Pseudo;
use Spwa\UI\Shadow;
use Spwa\UI\UI;
use Spwa\UI\UIElement;
use Spwa\UI\Unit;
use Spwa\VNode\StatelessComponent;
use Spwa\VNode\VNode;

/**
 * Story-location section — Leaflet map plus a row of landmark "chips".
 * Clicking a chip pans the map to that landmark; the map opens
 * centered on the first entry in Landmarks::all().
 *
 * Stateless: the Leaflet is constructed each render and the click
 * handlers drive it via Js::invoke, so no component state is needed
 * to remember the active landmark across the POST round-trip.
 */
class StoryMap extends StatelessComponent
{
    protected function build(): VNode
    {
        $leaflet = new Leaflet('story-map', Landmarks::all()[0]['coords']);

        return UI::column()
            ->gap(Unit::rem(0.75))
            ->content(
                $leaflet,
                $this->landmarkRow($leaflet),
            );
    }

    private function landmarkRow(Leaflet $leaflet): UIElement
    {
        $chips = array_map(
            fn(array $lm) => UI::text($lm['name'])
                ->fontSize(FontSize::ExtraSmall)
                ->weight(FontWeight::Medium)
                ->color(Color::gray(700))
                ->color(Color::red(600), Pseudo::hover())
                ->paddingX(Unit::rem(0.6))
                ->paddingY(Unit::rem(0.3))
                ->background(Color::white())
                ->background(Color::gray(100), Pseudo::hover())
                ->rounded(Unit::rem(0.25))
                ->shadow(Shadow::Small)
                ->clickable()
                ->onClick(fn() => $leaflet->setView($lm['coords'], 14)),
            Landmarks::all(),
        );

        return UI::row()
            ->wrap()
            ->gap(Unit::rem(0.5))
            ->content(...$chips);
    }
}
