<?php

namespace Spwa\Samples\site\Pages;

use Spwa\Samples\site\Components\Navbar;
use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\UI;
use Spwa\UI\Unit;
use Spwa\VNode\Component;
use Spwa\VNode\VNode;

class SiteApp extends Component
{
    protected function build(): VNode
    {
        return UI::column()
            ->background(Color::slate(50))
            ->minHeight(Unit::screen())
            ->content(
                new Navbar(),
                UI::router()
                    ->route('/', new HomePage())
                    ->route('/features', new FeaturesPage())
                    ->route('/components', new ComponentsPage())
                    ->route('/forms', new FormsPage())
                    ->route('/state', new StatePage())
                    ->fallback(new HomePage()),
                $this->buildFooter(),
            );
    }

    private function buildFooter(): VNode
    {
        return UI::footer()
            ->background(Color::slate(900))
            ->padding(Unit::extraLarge())
            ->content(
                UI::column()
                    ->maxWidth(Unit::px(900))
                    ->marginHorizontal(Unit::auto())
                    ->gap(Unit::small())
                    ->alignCenter()
                    ->content(
                        UI::text('SPWA — Server-Powered Web Applications')
                            ->color(Color::slate(400))
                            ->fontSize(FontSize::Small),
                        UI::text('Pure PHP. No Node. No external libraries.')
                            ->color(Color::slate(500))
                            ->fontSize(FontSize::ExtraSmall),
                    )
            );
    }
}
