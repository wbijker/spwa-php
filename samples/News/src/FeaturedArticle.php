<?php

namespace Samples\News;

use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\FontWeight;
use Spwa\UI\Pseudo;
use Spwa\UI\Router;
use Spwa\UI\Shadow;
use Spwa\UI\UI;
use Spwa\UI\Unit;
use Spwa\VNode\StatelessComponent;
use Spwa\VNode\VNode;

/**
 * Hero card at the top of the body — full-width image with overlaid
 * category, headline, excerpt, and date. Clicking the card navigates
 * to the article's detail route.
 */
class FeaturedArticle extends StatelessComponent
{
    public function __construct(private Article $article) {}

    protected function build(): VNode
    {
        $a = $this->article;

        return UI::column()
            ->background(Color::white())
            ->rounded(Unit::rem(0.5))
            ->overflow()
            ->shadow(Shadow::Small)
            ->shadow(Shadow::Large, Pseudo::hover())
            ->animated()
            ->clickable()
            ->on('click', fn() => Router::navigate(new ArticleRoute($a->slug())))
            ->content(
                UI::image($a->coverImage, $a->title)
                    ->width(Unit::full())
                    ->height(Unit::px(220))
                    ->height(Unit::px(320), Pseudo::md())
                    ->height(Unit::px(380), Pseudo::lg())
                    ->objectCover(),
                UI::column()
                    ->padding(Unit::rem(1))
                    ->padding(Unit::rem(1.5), Pseudo::md())
                    ->gap(Unit::rem(0.75))
                    ->content(
                        News::categoryLabel($a->category),
                        UI::text($a->title)
                            ->fontSize(FontSize::ExtraLarge)
                            ->fontSize(FontSize::TwoXL, Pseudo::md())
                            ->fontSize(FontSize::ThreeXL, Pseudo::lg())
                            ->weight(FontWeight::Bold)
                            ->color(Color::gray(900))
                            ->color(Color::red(600), Pseudo::hover()),
                        UI::text($a->excerpt)
                            ->fontSize(FontSize::Small)
                            ->fontSize(FontSize::Base, Pseudo::md())
                            ->color(Color::gray(600)),
                        News::articleMeta($a->formattedDate()),
                    )
            );
    }
}
