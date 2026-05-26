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
                new Header(),

                UI::column()
                    ->grow()
                    ->width(Unit::full())
                    ->minWidth(Unit::none())
                    ->content(
                        Router::router()
                            ->register(NewsListRoute::class, fn() => $this->body())
                            ->register(ArticleRoute::class, function (ArticleRoute $route) {
                                $article = NewsData::findBySlug($route->slug);
                                return $article !== null ? new DetailView($article) : $this->body();
                            })
                            ->fallback($this->body()),
                    ),

                new Footer(),
            );
    }

    // ============================================================
    // Body — 2-column grid (articles + sidebar)
    // ============================================================

    private function body(): UIElement
    {

        $leaflet = new Leaflet('story-map', Landmarks::all()[0]['coords']);

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
                        new FeaturedArticle(NewsData::featured()),
                        $leaflet,
                        $this->landmarkRow($leaflet),
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
    // Landmark picker — click a name to pan the story map.
    // ============================================================

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
                ->on('click', fn() => $leaflet->setView($lm['coords'], 14)),
            Landmarks::all(),
        );

        return UI::row()
            ->wrap()
            ->gap(Unit::rem(0.5))
            ->paddingY(Unit::rem(0.5))
            ->content(...$chips);
    }

    // ============================================================
    // What's Next horizontal strip
    // ============================================================

    /** @param WhatsNextItem[] $items */
    private function whatsNextStrip(array $items): UIElement
    {
        $cards = array_map(fn(WhatsNextItem $i) => new WhatsNextCard($i), $items);

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

    // ============================================================
    // Article list
    // ============================================================

    /** @param Article[] $articles */
    private function articleList(array $articles): UIElement
    {
        $cards = array_map(fn(Article $a) => new ArticleCard($a), $articles);

        return UI::column()
            ->gap(Unit::rem(1))
            ->content(
                $this->sectionHeading('LATEST NEWS'),
                ...$cards
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

    public static function categoryLabel(string $category): UIElement
    {
        return UI::text(strtoupper($category))
            ->fontSize(FontSize::ExtraSmall)
            ->weight(FontWeight::Bold)
            ->color(self::categoryColor($category));
    }

    public static function articleMeta(string $date): UIElement
    {
        return UI::text($date)
            ->fontSize(FontSize::ExtraSmall)
            ->color(Color::gray(500));
    }

    public static function categoryColor(string $category): Color
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

}
