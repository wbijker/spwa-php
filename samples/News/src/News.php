<?php

namespace Samples\News;

use Spwa\UI\Color;
use Spwa\UI\Direction;
use Spwa\UI\FontSize;
use Spwa\UI\FontWeight;
use Spwa\UI\Pseudo;
use Spwa\UI\Router;
use Spwa\UI\Shadow;
use Spwa\UI\UI;
use Spwa\UI\UIElement;
use Spwa\UI\Unit;
use Spwa\UI\ValueMap;
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
        // Outer column fills at least the viewport, so when the routed content
        // is short the grow()'d inner column expands and pushes the footer to
        // the bottom of the screen. When content is taller than the viewport
        // the page scrolls and the footer sits below it naturally.
        return UI::column()
            ->minHeight(Unit::vh(100))
            ->width(Unit::full())
            ->clipX()
            ->background(Color::gray(50))
            ->content(
                $this->header(),

                UI::column()
                    ->grow()
                    ->width(Unit::full())
                    ->minWidth(Unit::none())
                    ->content(
                        Router::router()
                            ->register(NewsListRoute::class, fn() => $this->body())
                            ->register(ArticleRoute::class, function (ArticleRoute $route) {
                                $article = NewsData::findBySlug($route->slug);
                                return $article !== null ? $this->detailView($article) : $this->body();
                            })
                            ->fallback($this->body()),
                    ),

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
                    ->wrap()
                    ->alignBetween()
                    ->alignMiddle()
                    ->gap(Unit::rem(0.75))
                    ->maxWidth(Unit::px(1200))
                    ->width(Unit::full())
                    ->marginX(Unit::auto())
                    ->padding(x: Unit::rem(1),   y: Unit::rem(0.75))
                    ->padding(x: Unit::rem(1.5), y: Unit::rem(1), pseudo: Pseudo::md())
                    ->content(
                        UI::row()
                            ->alignMiddle()
                            ->gap(Unit::rem(0.5))
                            ->content(
                                UI::text('My')
                                    ->fontSize(FontSize::ExtraLarge)
                                    ->fontSize(FontSize::TwoXL, Pseudo::md())
                                    ->weight(FontWeight::Bold)
                                    ->color(Color::gray(900)),
                                UI::text('News')
                                    ->fontSize(FontSize::ExtraLarge)
                                    ->fontSize(FontSize::TwoXL, Pseudo::md())
                                    ->weight(FontWeight::Bold)
                                    ->color(Color::red(600))
                            ),
                        UI::row()
                            ->wrap()
                            ->gap(Unit::rem(0.75))
                            ->gap(Unit::rem(1.25), Pseudo::md())
                            ->gap(Unit::rem(1.5), Pseudo::lg())
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
            ->paddingY(Unit::rem(0.25))
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
        // Stacks vertically on small screens; switches to side-by-side
        // (main + sidebar) at lg (>=1024px).
        return UI::row()
            ->direction(Direction::column())
            ->direction(Direction::row(), Pseudo::lg())
            ->maxWidth(Unit::px(1200))
            ->width(Unit::full())
            ->marginX(Unit::auto())
            ->padding(x: Unit::rem(1),   y: Unit::rem(1.25))
            ->padding(x: Unit::rem(1.5), y: Unit::rem(2), pseudo: Pseudo::md())
            ->gap(Unit::rem(1.5))
            ->gap(Unit::rem(2), Pseudo::md())
            ->alignTop()
            ->content(
                // Main column. minWidth(none) overrides the flex-item
                // default of min-width:auto so the scrollable What's Next
                // strip can scroll inside the column instead of pushing
                // the page wider on narrow viewports.
                UI::column()
                    ->grow(2)
                    ->width(Unit::full())
                    ->minWidth(Unit::none())
                    ->gap(Unit::rem(1.5))
                    ->gap(Unit::rem(2), Pseudo::md())
                    ->content(
                        $this->featuredArticle(NewsData::featured()),
                        $this->whatsNextStrip(NewsData::whatsNext()),
                        $this->articleList(NewsData::articles()),
                    ),

                // Sidebar — hidden below md; full width on md, constrained at lg.
                UI::column()
                    ->hidden()
                    ->flex(Pseudo::md())
                    ->grow(1)
                    ->width(Unit::full())
                    ->minWidth(Unit::none())
                    ->minWidth(Unit::px(280), Pseudo::lg())
                    ->maxWidth(Unit::px(340), Pseudo::lg())
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
                    ->height(Unit::px(220))
                    ->height(Unit::px(320), Pseudo::md())
                    ->height(Unit::px(380), Pseudo::lg())
                    ->objectCover(),
                UI::column()
                    ->padding(Unit::rem(1))
                    ->padding(Unit::rem(1.5), Pseudo::md())
                    ->gap(Unit::rem(0.75))
                    ->content(
                        $this->categoryLabel($article->category),
                        UI::text($article->title)
                            ->fontSize(FontSize::ExtraLarge)
                            ->fontSize(FontSize::TwoXL, Pseudo::md())
                            ->fontSize(FontSize::ThreeXL, Pseudo::lg())
                            ->weight(FontWeight::Bold)
                            ->color(Color::gray(900))
                            ->color(Color::red(600), Pseudo::hover()),
                        UI::text($article->excerpt)
                            ->fontSize(FontSize::Small)
                            ->fontSize(FontSize::Base, Pseudo::md())
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
        // Stacks (image above text) on small screens; image-left layout on md+.
        return UI::row()
            ->direction(Direction::column())
            ->direction(Direction::row(), Pseudo::md())
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
                    ->width(Unit::full())
                    ->width(Unit::px(220), Pseudo::md())
                    ->height(Unit::px(180))
                    ->height(Unit::px(160), Pseudo::md())
                    ->noShrink(Pseudo::md())
                    ->objectCover(),
                UI::column()
                    ->grow()
                    ->padding(Unit::rem(1))
                    ->padding(Unit::rem(1.25), Pseudo::md())
                    ->gap(Unit::rem(0.5))
                    ->content(
                        $this->categoryLabel($article->category),
                        UI::text($article->title)
                            ->fontSize(FontSize::Base)
                            ->fontSize(FontSize::Large, Pseudo::md())
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
                    ->padding(x: Unit::rem(1), y: Unit::rem(0.75))
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
            ->padding(x: Unit::rem(1), y: Unit::rem(0.75))
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
            ->padding(x: Unit::rem(1),   y: Unit::rem(2))
            ->padding(x: Unit::rem(1.5), y: Unit::rem(2.5), pseudo: Pseudo::md())
            ->content(
                // Stacks on small screens; row with space-between on md+.
                UI::row()
                    ->direction(Direction::column())
                    ->direction(Direction::row(), Pseudo::md())
                    ->maxWidth(Unit::px(1200))
                    ->width(Unit::full())
                    ->marginX(Unit::auto())
                    ->alignBetween()
                    ->alignMiddle()
                    ->gap(Unit::rem(1))
                    ->content(
                        UI::text('© 2026. All rights reserved.')
                            ->fontSize(FontSize::Small)
                            ->color(Color::gray(400)),
                        UI::row()
                            ->wrap()
                            ->alignCenter()
                            ->gap(Unit::rem(1))
                            ->gap(Unit::rem(1.5), Pseudo::md())
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
            ->color(ValueMap::create([
                'banking'       => Color::blue(600),
                'business'      => Color::blue(600),
                'mobile'        => Color::violet(600),
                'cellular'      => Color::violet(600),
                'internet'      => Color::cyan(600),
                'fibre'         => Color::cyan(600),
                'cybersecurity' => Color::red(600),
                'cloud'         => Color::indigo(600),
                'startups'      => Color::emerald(600),
                'funding'       => Color::emerald(600),
                'government'    => Color::amber(700),
                'energy'        => Color::orange(600),
                'ai'            => Color::purple(600),
                'telecoms'      => Color::purple(600),
            ], Color::gray(600), strtolower($category)));
    }

    private function articleMeta(string $date): UIElement
    {
        return UI::text($date)
            ->fontSize(FontSize::ExtraSmall)
            ->color(Color::gray(500));
    }

    // ============================================================
    // Detail view — /article/<slug>
    // ============================================================

    private function detailView(Article $article): UIElement
    {
        return UI::column()
            ->maxWidth(Unit::px(900))
            ->width(Unit::full())
            ->marginX(Unit::auto())
            ->padding(x: Unit::rem(1),   y: Unit::rem(1.25))
            ->padding(x: Unit::rem(1.5), y: Unit::rem(2), pseudo: Pseudo::md())
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
                            ->height(Unit::px(240))
                            ->height(Unit::px(360), Pseudo::md())
                            ->height(Unit::px(440), Pseudo::lg())
                            ->objectCover(),
                        UI::column()
                            ->padding(Unit::rem(1.25))
                            ->padding(Unit::rem(2), Pseudo::md())
                            ->gap(Unit::rem(1))
                            ->content(
                                $this->categoryLabel($article->category),
                                UI::text($article->title)
                                    ->fontSize(FontSize::TwoXL)
                                    ->fontSize(FontSize::ThreeXL, Pseudo::md())
                                    ->fontSize(FontSize::FourXL, Pseudo::lg())
                                    ->weight(FontWeight::Bold)
                                    ->color(Color::gray(900)),
                                UI::text($article->formattedDate())
                                    ->fontSize(FontSize::Small)
                                    ->color(Color::gray(500)),
                                UI::text($article->excerpt)
                                    ->fontSize(FontSize::Base)
                                    ->fontSize(FontSize::Large, Pseudo::md())
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
