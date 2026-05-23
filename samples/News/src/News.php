<?php

namespace Samples\News;

use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\FontWeight;
use Spwa\UI\Pseudo;
use Spwa\UI\Router;
use Spwa\UI\Shadow;
use Spwa\UI\UI;
use Spwa\UI\UIElement;
use Spwa\UI\Unit;
use Spwa\VNode\Component;
use Spwa\VNode\VNode;

/**
 * News front-page clone — header nav, featured article,
 * "What's Next" strip, latest news list, Industry News sidebar, footer.
 *
 * Routing is delegated to the framework Router using typed BaseRoute
 * subclasses:
 *
 *   NewsListRoute        → list view at /
 *   ArticleRoute($slug)  → detail view at /article/<slug>
 *
 * Click handlers call Router::navigate(new ArticleRoute(...)) — the URL
 * derivation lives on the route class, not at the call site.
 */
class News extends Component
{
    protected function build(): VNode
    {
        return UI::column()
            ->width(Unit::full())
            ->background(Color::gray(50))
            ->content(
                $this->header(),

                Router::router()
                    ->register(NewsListRoute::class, fn() => $this->body())
                    ->register(ArticleRoute::class, function (ArticleRoute $route) {
                        $article = NewsData::findBySlug($route->slug);
                        return $article !== null ? $this->detailView($article) : $this->body();
                    })
                    ->fallback($this->body()),
                $this->footer(),
            );
    }

    // ============================================================
    // Header
    // ============================================================

    private function header(): UIElement
    {
        return UI::container()
            ->background(Color::white())
            ->shadow(Shadow::Small)
            ->content(
                UI::row()
                    ->alignBetween()
                    ->alignMiddle()
                    ->maxWidth(Unit::px(1200))
                    ->width(Unit::full())
                    ->marginHorizontal(Unit::auto())
                    ->paddingVertical(Unit::rem(1))
                    ->content(
                        UI::row()
                            ->alignMiddle()
                            ->gap(Unit::rem(0.5))
                            ->content(
                                UI::text('My')
                                    ->fontSize(FontSize::TwoXL)
                                    ->weight(FontWeight::Bold)
                                    ->color(Color::gray(900)),
                                UI::text('News')
                                    ->fontSize(FontSize::TwoXL)
                                    ->weight(FontWeight::Bold)
                                    ->color(Color::red(600))
                            ),
                        UI::row()
                            ->gap(Unit::rem(1.5))
                            ->content(
                                $this->navLink('News', true),
                                $this->navLink('Industry'),
                                $this->navLink('Mobile'),
                                $this->navLink('Internet'),
                                $this->navLink('Business'),
                                $this->navLink('Banking'),
                                $this->navLink('Reviews'),
                            )
                    )
            );
    }

    private function navLink(string $label, bool $active = false): UIElement
    {
        $base = UI::text($label)
            ->fontSize(FontSize::Small)
            ->weight(FontWeight::SemiBold)
            ->clickable()
            ->paddingVertical(Unit::rem(0.25))
            ->color(Color::red(600), Pseudo::hover());

        if ($active) {
            return $base
                ->color(Color::red(600))
                ->borderBottom(2)
                ->borderColor(Color::red(600));
        }

        return $base->color(Color::gray(700));
    }

    // ============================================================
    // Body — 2-column grid (articles + sidebar)
    // ============================================================

    private function body(): UIElement
    {
        return UI::row()
            ->maxWidth(Unit::px(1200))
            ->width(Unit::full())
            ->marginHorizontal(Unit::auto())
            ->paddingHorizontal(Unit::rem(1.5))
            ->paddingVertical(Unit::rem(2))
            ->gap(Unit::rem(2))
            ->alignTop()
            ->content(
                // Main column
                UI::column()
                    ->grow(2)
                    ->gap(Unit::rem(2))
                    ->content(
                        $this->featuredArticle(NewsData::featured()),
                        $this->whatsNextStrip(NewsData::whatsNext()),
                        $this->articleList(NewsData::articles()),
                    ),

                // Sidebar
                UI::column()
                    ->grow(1)
                    ->minWidth(Unit::px(280))
                    ->maxWidth(Unit::px(340))
                    ->gap(Unit::rem(1.5))
                    ->content(
                        $this->industryNewsSidebar(NewsData::industryNews()),
                    )
            );
    }

    // ============================================================
    // Featured article
    // ============================================================

    private function featuredArticle(Article $article): UIElement
    {
        return UI::column()
            ->background(Color::white())
            ->rounded(Unit::rem(0.5))
            ->overflow()
            ->shadow(Shadow::Small)
            ->shadow(Shadow::Large, Pseudo::hover())
            ->animated()
            ->clickable()
            ->on('click', fn() => Router::navigate(new ArticleRoute($article->slug())))
            ->content(
                UI::image($article->coverImage, $article->title)
                    ->width(Unit::full())
                    ->height(Unit::px(380))
                    ->objectCover(),
                UI::column()
                    ->padding(Unit::rem(1.5))
                    ->gap(Unit::rem(0.75))
                    ->content(
                        $this->categoryLabel($article->category),
                        UI::text($article->title)
                            ->fontSize(FontSize::ThreeXL)
                            ->weight(FontWeight::Bold)
                            ->color(Color::gray(900))
                            ->color(Color::red(600), Pseudo::hover()),
                        UI::text($article->excerpt)
                            ->fontSize(FontSize::Base)
                            ->color(Color::gray(600)),
                        $this->articleMeta($article->formattedDate())
                    )
            );
    }

    // ============================================================
    // What's Next horizontal strip
    // ============================================================

    /** @param WhatsNextItem[] $items */
    private function whatsNextStrip(array $items): UIElement
    {
        $cards = array_map(fn(WhatsNextItem $i) => $this->whatsNextCard($i), $items);

        return UI::column()
            ->gap(Unit::rem(1))
            ->content(
                $this->sectionHeading('WHAT\'S NEXT'),
                UI::row()
                    ->gap(Unit::rem(1))
                    ->scrollable()
                    ->paddingBottom(Unit::rem(0.5))
                    ->content(...$cards)
            );
    }

    private function whatsNextCard(WhatsNextItem $item): UIElement
    {
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
                UI::image($item->coverImage, $item->title)
                    ->width(Unit::full())
                    ->height(Unit::px(120))
                    ->objectCover(),
                UI::column()
                    ->padding(Unit::rem(0.75))
                    ->gap(Unit::rem(0.375))
                    ->content(
                        $this->categoryLabel($item->category),
                        UI::text($item->title)
                            ->fontSize(FontSize::Small)
                            ->weight(FontWeight::SemiBold)
                            ->color(Color::gray(900))
                            ->color(Color::red(600), Pseudo::hover())
                    )
            );
    }

    // ============================================================
    // Article list
    // ============================================================

    /** @param Article[] $articles */
    private function articleList(array $articles): UIElement
    {
        $cards = array_map(fn(Article $a) => $this->articleCard($a), $articles);

        return UI::column()
            ->gap(Unit::rem(1))
            ->content(
                $this->sectionHeading('LATEST NEWS'),
                ...$cards
            );
    }

    private function articleCard(Article $article): UIElement
    {
        return UI::row()
            ->background(Color::white())
            ->rounded(Unit::rem(0.375))
            ->overflow()
            ->shadow(Shadow::Small)
            ->shadow(Shadow::Medium, Pseudo::hover())
            ->animated()
            ->clickable()
            ->alignTop()
            ->on('click', fn() => Router::navigate(new ArticleRoute($article->slug())))
            ->content(
                UI::image($article->coverImage, $article->title)
                    ->width(Unit::px(220))
                    ->height(Unit::px(160))
                    ->noShrink()
                    ->objectCover(),
                UI::column()
                    ->grow()
                    ->padding(Unit::rem(1.25))
                    ->gap(Unit::rem(0.5))
                    ->content(
                        $this->categoryLabel($article->category),
                        UI::text($article->title)
                            ->fontSize(FontSize::Large)
                            ->weight(FontWeight::SemiBold)
                            ->color(Color::gray(900))
                            ->color(Color::red(600), Pseudo::hover()),
                        UI::text($article->excerpt)
                            ->fontSize(FontSize::Small)
                            ->color(Color::gray(600)),
                        $this->articleMeta($article->formattedDate())
                    )
            );
    }

    // ============================================================
    // Industry News sidebar
    // ============================================================

    /** @param IndustryNewsItem[] $items */
    private function industryNewsSidebar(array $items): UIElement
    {
        $last = count($items) - 1;
        $rows = [];
        foreach ($items as $i => $item) {
            $rows[] = $this->industryNewsRow($item, $i === $last);
        }

        return UI::column()
            ->background(Color::white())
            ->rounded(Unit::rem(0.5))
            ->overflow()
            ->shadow(Shadow::Small)
            ->content(
                UI::container()
                    ->background(Color::red(600))
                    ->paddingHorizontal(Unit::rem(1))
                    ->paddingVertical(Unit::rem(0.75))
                    ->content(
                        UI::text('INDUSTRY NEWS')
                            ->fontSize(FontSize::Small)
                            ->weight(FontWeight::Bold)
                            ->color(Color::white())
                    ),
                UI::column()->content(...$rows)
            );
    }

    private function industryNewsRow(IndustryNewsItem $item, bool $isLast): UIElement
    {
        $row = UI::column()
            ->paddingHorizontal(Unit::rem(1))
            ->paddingVertical(Unit::rem(0.75))
            ->gap(Unit::rem(0.25))
            ->clickable()
            ->background(Color::gray(50), Pseudo::hover())
            ->content(
                UI::text($item->title)
                    ->fontSize(FontSize::Small)
                    ->weight(FontWeight::Medium)
                    ->color(Color::gray(800))
                    ->color(Color::red(600), Pseudo::hover()),
                UI::text($item->formattedDate())
                    ->fontSize(FontSize::ExtraSmall)
                    ->color(Color::gray(500))
            );

        if (!$isLast) {
            $row = $row->borderBottom(1)->borderColor(Color::gray(200));
        }

        return $row;
    }

    // ============================================================
    // Footer
    // ============================================================

    private function footer(): UIElement
    {
        return UI::container()
            ->background(Color::gray(900))
            ->paddingVertical(Unit::rem(2.5))
            ->paddingHorizontal(Unit::rem(1.5))
            ->content(
                UI::row()
                    ->maxWidth(Unit::px(1200))
                    ->width(Unit::full())
                    ->marginHorizontal(Unit::auto())
                    ->alignBetween()
                    ->alignMiddle()
                    ->content(
                        UI::text('© 2026. All rights reserved.')
                            ->fontSize(FontSize::Small)
                            ->color(Color::gray(400)),
                        UI::row()
                            ->gap(Unit::rem(1.5))
                            ->content(
                                $this->footerLink('About'),
                                $this->footerLink('Advertise'),
                                $this->footerLink('Contact'),
                                $this->footerLink('Privacy'),
                            )
                    )
            );
    }

    private function footerLink(string $label): UIElement
    {
        return UI::text($label)
            ->fontSize(FontSize::Small)
            ->color(Color::gray(400))
            ->color(Color::white(), Pseudo::hover())
            ->clickable();
    }

    // ============================================================
    // Shared helpers
    // ============================================================

    private function sectionHeading(string $label): UIElement
    {
        return UI::row()
            ->alignMiddle()
            ->paddingBottom(Unit::rem(0.5))
            ->borderBottom(2)
            ->borderColor(Color::red(600))
            ->content(
                UI::text($label)
                    ->fontSize(FontSize::Small)
                    ->weight(FontWeight::Bold)
                    ->color(Color::gray(900))
            );
    }

    private function categoryLabel(string $category): UIElement
    {
        return UI::text(strtoupper($category))
            ->fontSize(FontSize::ExtraSmall)
            ->weight(FontWeight::Bold)
            ->color($this->categoryColor($category));
    }

    private function articleMeta(string $date): UIElement
    {
        return UI::text($date)
            ->fontSize(FontSize::ExtraSmall)
            ->color(Color::gray(500));
    }

    private function categoryColor(string $category): Color
    {
        return match (strtolower($category)) {
            'banking', 'business' => Color::blue(600),
            'mobile', 'cellular' => Color::violet(600),
            'internet', 'fibre'   => Color::cyan(600),
            'cybersecurity'       => Color::red(600),
            'cloud'               => Color::indigo(600),
            'startups', 'funding' => Color::emerald(600),
            'government'          => Color::amber(700),
            'energy'              => Color::orange(600),
            'ai', 'telecoms'      => Color::purple(600),
            default               => Color::gray(600),
        };
    }

    // ============================================================
    // Detail view — /article/<slug>
    // ============================================================

    private function detailView(Article $article): UIElement
    {
        return UI::column()
            ->maxWidth(Unit::px(900))
            ->width(Unit::full())
            ->marginHorizontal(Unit::auto())
            ->paddingHorizontal(Unit::rem(1.5))
            ->paddingVertical(Unit::rem(2))
            ->gap(Unit::rem(1.5))
            ->content(
                $this->backLink(),
                UI::column()
                    ->background(Color::white())
                    ->rounded(Unit::rem(0.5))
                    ->overflow()
                    ->shadow(Shadow::Small)
                    ->content(
                        UI::image($article->coverImage, $article->title)
                            ->width(Unit::full())
                            ->height(Unit::px(440))
                            ->objectCover(),
                        UI::column()
                            ->padding(Unit::rem(2))
                            ->gap(Unit::rem(1))
                            ->content(
                                $this->categoryLabel($article->category),
                                UI::text($article->title)
                                    ->fontSize(FontSize::FourXL)
                                    ->weight(FontWeight::Bold)
                                    ->color(Color::gray(900)),
                                UI::text($article->formattedDate())
                                    ->fontSize(FontSize::Small)
                                    ->color(Color::gray(500)),
                                UI::text($article->excerpt)
                                    ->fontSize(FontSize::Large)
                                    ->weight(FontWeight::SemiBold)
                                    ->color(Color::gray(700))
                                    ->paddingTop(Unit::rem(0.5)),
                                UI::text($article->content)
                                    ->fontSize(FontSize::Base)
                                    ->color(Color::gray(700))
                            )
                    )
            );
    }

    private function backLink(): UIElement
    {
        return UI::text('← Back to all news')
            ->fontSize(FontSize::Small)
            ->weight(FontWeight::SemiBold)
            ->color(Color::red(600))
            ->color(Color::red(700), Pseudo::hover())
            ->clickable()
            ->on('click', fn() => Router::navigate(new NewsListRoute()));
    }
}
