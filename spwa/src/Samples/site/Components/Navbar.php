<?php

namespace Spwa\Samples\site\Components;

use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\FontWeight;
use Spwa\UI\Router;
use Spwa\UI\Shadow;
use Spwa\UI\UI;
use Spwa\UI\Unit;
use Spwa\VNode\Component;
use Spwa\VNode\VNode;

class Navbar extends Component
{
    protected function build(): VNode
    {
        return UI::nav()
            ->background(Color::white())
            ->shadow(Shadow::Small)
            ->paddingHorizontal(Unit::extraLarge())
            ->paddingVertical(Unit::medium())
            ->content(
                UI::row()
                    ->maxWidth(Unit::px(900))
                    ->marginHorizontal(Unit::auto())
                    ->alignMiddle()
                    ->alignBetween()
                    ->content(
                        UI::text('SPWA')
                            ->fontSize(FontSize::ExtraLarge)
                            ->weight(FontWeight::Bold)
                            ->color(Color::indigo(600))
                            ->clickable()
                            ->on('click', fn() => Router::navigate('/')),

                        UI::row()
                            ->gap(Unit::small())
                            ->content(
                                $this->navItem('Home', '/'),
                                $this->navItem('Features', '/features'),
                                $this->navItem('Components', '/components'),
                                $this->navItem('Forms', '/forms'),
                                $this->navItem('State', '/state'),
                            )
                    )
            );
    }

    private function navItem(string $label, string $path): VNode
    {
        return UI::button($label)
            ->borderNone()
            ->background(Color::transparent())
            ->color(Color::slate(600), Color::indigo(700)->hover())
            ->padding(Unit::small())
            ->paddingHorizontal(Unit::medium())
            ->rounded(Unit::roundedLg())
            ->fontSize(FontSize::Small)
            ->clickable()
            ->on('click', fn() => Router::navigate($path));
    }
}
