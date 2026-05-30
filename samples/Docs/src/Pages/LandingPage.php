<?php

namespace Samples\Docs\Pages;

use BrickPHP\UI\Color;
use BrickPHP\UI\FontSize;
use BrickPHP\UI\FontWeight;
use BrickPHP\UI\Pseudo;
use BrickPHP\UI\Router;
use BrickPHP\UI\Shadow;
use BrickPHP\UI\UI;
use BrickPHP\UI\UIElement;
use BrickPHP\UI\Unit;
use BrickPHP\VNode\Component;
use BrickPHP\VNode\VNode;
use Samples\Docs\Components\BrickLogo;
use Samples\Docs\Components\BrowserFrame;
use Samples\Docs\Components\CodeWindow;
use Samples\Docs\Components\FeatureTile;
use Samples\Docs\Components\Icon;
use Samples\Docs\Components\PHPCode;

/**
 * Landing page — follows the Stitch design system mockup.
 *   1. Compact code-centric hero (left text, right CodeWindow).
 *   2. Single-paragraph "what is this" band.
 *   3. 7-tile feature grid.
 *   4. Reactive components section: copy + claims on the left, browser
 *      frame demoing the counter on the right.
 */
class LandingPage extends Component
{
    protected function build(): VNode
    {
        return UI::column()
            ->background(Color::white())
            ->content(
                $this->hero(),
                $this->summary(),
                $this->features(),
                $this->preview(),
            );
    }

    // ============================================================
    // Hero
    // ============================================================

    private function hero(): VNode
    {
        return UI::column()
            ->background(Color::slate(900))
            ->color(Color::white())
            ->paddingY(Unit::px(64))
            ->paddingX(Unit::px(24))
            ->borderBottom(1)
            ->borderColor(Color::slate(800))
            ->content(
                UI::row()
                    ->maxWidth(Unit::px(1100))
                    ->marginX(Unit::auto())
                    ->width(Unit::full())
                    ->gap(Unit::px(64))
                    ->alignMiddle()
                    ->direction(\BrickPHP\UI\Direction::column())
                    ->direction(\BrickPHP\UI\Direction::row(), Pseudo::lg())
                    ->content(
                        $this->heroText(),
                        $this->heroCode(),
                    ),
            );
    }

    private function heroText(): UIElement
    {
        return UI::column()
            ->grow()
            ->gap(Unit::px(24))
            ->alignLeft()
            ->content(
                UI::container()
                    ->padding(Unit::px(8))
                    ->rounded(Unit::roundedLg())
                    ->background(Color::slate(800))
                    ->bordered()
                    ->borderColor(Color::slate(700))
                    ->content((new BrickLogo())->size(48)),
                UI::text('The Modern ')
                    ->fontSize(FontSize::SixXL)
                    ->weight(FontWeight::Bold)
                    ->color(Color::white())
                    ->content(
                        UI::span('Server-Driven')->color(Color::orange(500)),
                        UI::text(' Framework.')
                            ->color(Color::white()),
                    ),
                UI::text('Build complex, interactive web applications with the simplicity of PHP. BrickPHP bridges the gap between server stability and client-side fluidity without the JS fatigue.')
                    ->fontSize(FontSize::Large)
                    ->color(Color::slate(400))
                    ->maxWidth(Unit::px(560)),
                UI::row()
                    ->gap(Unit::px(16))
                    ->paddingTop(Unit::px(8))
                    ->content(
                        UI::button('Get started')
                            ->background(Color::orange(500))
                            ->color(Color::white())
                            ->paddingX(Unit::px(32))
                            ->paddingY(Unit::px(16))
                            ->rounded(Unit::roundedLg())
                            ->fontSize(FontSize::Base)
                            ->weight(FontWeight::SemiBold)
                            ->borderNone()
                            ->clickable()
                            ->background(Color::orange(600), Pseudo::hover()),
                        UI::button('Browse API')
                            ->background(Color::transparent())
                            ->color(Color::white())
                            ->bordered()
                            ->borderColor(Color::slate(700))
                            ->paddingX(Unit::px(32))
                            ->paddingY(Unit::px(16))
                            ->rounded(Unit::roundedLg())
                            ->fontSize(FontSize::Base)
                            ->weight(FontWeight::SemiBold)
                            ->clickable()
                            ->background(Color::slate(800), Pseudo::hover())
                            ->onClick(fn() => Router::navigate('/api')),
                    ),
            );
    }

    private function heroCode(): UIElement
    {
        $php = <<<'PHP'
        <?php

        use BrickPHP\Brick;
        use App\App;

        require 'vendor/autoload.php';

        Brick::run(App::class);
        PHP;

        return UI::column()
            ->grow()
            ->width(Unit::full())
            ->maxWidth(Unit::px(540))
            ->content(
                (new CodeWindow())
                    ->tab('index.php', '🐘')
                    ->content(new PHPCode($php)),
            );
    }

    // ============================================================
    // Summary band
    // ============================================================

    private function summary(): VNode
    {
        return UI::column()
            ->background(Color::white())
            ->borderBottom(1)
            ->borderColor(Color::slate(200))
            ->paddingY(Unit::px(48))
            ->paddingX(Unit::px(24))
            ->content(
                UI::container()
                    ->maxWidth(Unit::px(1100))
                    ->marginX(Unit::auto())
                    ->width(Unit::full())
                    ->content(
                        UI::text('BrickPHP is a Server-Powered Web Applications framework. Everything runs natively on server: state, DOM diffing, CSS extraction — all. One coherent solution. No more npm install, no node_modules, no npm package updates, no separate building steps, no separate state management library, no CSS library, no JS framework. All just simple PHP.')
                            ->fontSize(FontSize::Large)
                            ->color(Color::slate(700))
                            ->center()
                            ->maxWidth(Unit::px(800))
                            ->marginX(Unit::auto()),
                    ),
            );
    }

    // ============================================================
    // Features
    // ============================================================

    private function features(): VNode
    {
        return UI::column()
            ->background(Color::slate(50))
            ->paddingY(Unit::px(96))
            ->paddingX(Unit::px(24))
            ->content(
                UI::container()
                    ->maxWidth(Unit::px(1100))
                    ->marginX(Unit::auto())
                    ->width(Unit::full())
                    ->content(
                        UI::grid(1)
                            ->columns(2, Pseudo::md())
                            ->columns(3, Pseudo::lg())
                            ->gap(Unit::px(32))
                            ->content(
                                $this->allInOneFeature(),
                                $this->noGlueFeature(),
                                $this->hmrFeature(),
                                $this->stylingFeature(),
                                $this->wireframeFeature(),
                                $this->debuggingFeature(),
                                $this->uiElementsFeature(),
                            ),
                    ),
            );
    }

    private function allInOneFeature(): VNode
    {
        $code = <<<'PHP'
        public function state() { ... }
        public function styling() { ... }
        public function render() { ... }
        PHP;
        return (new FeatureTile(
            'dataset',
            'All in one place',
            'Routing, state, components, events, styling — every concern lives in one PHP codebase. No context-switching between languages or repos.',
        ))->preview(new PHPCode($code));
    }

    private function noGlueFeature(): VNode
    {
        $code = <<<'PHP'
        UI::div(
            $db->query('SELECT * FROM users')
        );
        PHP;
        return (new FeatureTile(
            'link_off',
            'No glue — all PHP',
            'Skip the API layer. Skip the serialization. Skip the type duplication. Your UI talks to your data directly because it lives in the same process.',
        ))->preview(new PHPCode($code));
    }

    private function hmrFeature(): VNode
    {
        $indicator = UI::row()
            ->gap(Unit::px(8))
            ->alignMiddle()
            ->content(
                UI::container()
                    ->width(Unit::px(8))
                    ->height(Unit::px(8))
                    ->roundedFull()
                    ->background(Color::orange(500)),
                UI::text('HMR Active: src/Pages/Home.php')
                    ->fontSize(FontSize::ExtraSmall)
                    ->color(Color::orange(400)),
            );

        return (new FeatureTile(
            'bolt',
            'Hot module reloading',
            'Save a PHP file, watch the browser update in place — without losing state. HMR built in, no Vite or Webpack config required.',
        ))->preview($indicator);
    }

    private function stylingFeature(): VNode
    {
        $code = <<<'PHP'
        UI::div()
          ->padding(Unit::large())
          ->color(Color::orange());
        PHP;
        return (new FeatureTile(
            'palette',
            'Styling out of the box',
            'A complete utility CSS system is bundled and lex-scanned from your source. Write semantic methods like padding(Unit::large()).',
        ))->preview(new PHPCode($code));
    }

    private function wireframeFeature(): VNode
    {
        $wireframe = UI::column()
            ->bordered()
            ->dashed()
            ->borderColor(Color::orange(400))
            ->padding(Unit::px(8))
            ->content(
                UI::row()->content(
                    UI::container()
                        ->background(Color::orange(500))
                        ->color(Color::white())
                        ->paddingX(Unit::px(4))
                        ->fontSize(FontSize::ExtraSmall)
                        ->content(UI::text('Button.php:24')),
                ),
                UI::container()
                    ->marginTop(Unit::px(8))
                    ->width(Unit::full())
                    ->height(Unit::px(32))
                    ->bordered()
                    ->borderColor(Color::orange(400))
                    ->background(Color::orange(500)),
            );

        return (new FeatureTile(
            'grid_view',
            'Wireframe inspector',
            'Toggle an overlay that outlines every UI element with its name and source location. Click straight through to the file from the browser.',
        ))->preview($wireframe);
    }

    private function debuggingFeature(): VNode
    {
        $log = UI::column()
            ->gap(Unit::px(4))
            ->content(
                UI::text('# Server Log 14:02:11')
                    ->fontSize(FontSize::ExtraSmall)
                    ->color(Color::slate(400)),
                UI::row()->gap(Unit::px(8))->alignMiddle()->content(
                    UI::text('[POST] /counter/update')
                        ->fontSize(FontSize::ExtraSmall)
                        ->color(Color::slate(300)),
                    UI::text('200 OK')
                        ->fontSize(FontSize::ExtraSmall)
                        ->color(Color::emerald(500)),
                ),
                UI::row()->gap(Unit::px(8))->alignMiddle()->content(
                    UI::text('Delta:')
                        ->fontSize(FontSize::ExtraSmall)
                        ->color(Color::slate(300)),
                    UI::text('+1')
                        ->fontSize(FontSize::ExtraSmall)
                        ->color(Color::orange(400)),
                    UI::text('(Morphing DOM)')
                        ->fontSize(FontSize::ExtraSmall)
                        ->color(Color::slate(300)),
                ),
            );

        return (new FeatureTile(
            'bug_report',
            'Debugging made easy',
            'Xdebug works out of the box. Server-rendered patches surface in the browser console so you can see exactly what changed between renders.',
        ))->preview($log);
    }

    private function uiElementsFeature(): VNode
    {
        $code = <<<'PHP'
        class HeaderElement extends UIElement {
          public string $title;
        }
        PHP;
        return (new FeatureTile(
            'view_quilt',
            'UI elements, not JS + CSS',
            'Express your interface through typed UIElements. No className strings, no template languages, no JSX. Refactor with IDE confidence.',
        ))->preview(new PHPCode($code));
    }

    // ============================================================
    // Preview section — Reactive Components
    // ============================================================

    private function preview(): VNode
    {
        return UI::column()
            ->background(Color::white())
            ->paddingY(Unit::px(96))
            ->paddingX(Unit::px(24))
            ->content(
                UI::row()
                    ->maxWidth(Unit::px(1100))
                    ->marginX(Unit::auto())
                    ->width(Unit::full())
                    ->gap(Unit::px(64))
                    ->alignMiddle()
                    ->direction(\BrickPHP\UI\Direction::column())
                    ->direction(\BrickPHP\UI\Direction::row(), Pseudo::lg())
                    ->content(
                        $this->previewCopy(),
                        $this->previewFrame(),
                    ),
            );
    }

    private function previewCopy(): UIElement
    {
        return UI::column()
            ->grow()
            ->gap(Unit::px(24))
            ->content(
                UI::text('Reactive Components,')
                    ->fontSize(FontSize::FourXL)
                    ->weight(FontWeight::Bold)
                    ->color(Color::slate(900))
                    ->content(
                        \BrickPHP\UI\UI::br(),
                        UI::span('No JavaScript Required')
                            ->color(Color::orange(500)),
                        UI::text('.')->color(Color::slate(900)),
                    ),
                UI::text('The Counter example below is rendered entirely on the server. When you click a button, BrickPHP calculates the delta and patches the DOM over a persistent WebSocket connection.')
                    ->fontSize(FontSize::Large)
                    ->color(Color::slate(500)),
                UI::column()
                    ->gap(Unit::px(16))
                    ->content(
                        $this->previewBullet('Automatic DOM patching with Morphdom integration.'),
                        $this->previewBullet('Native support for form validation and error states.'),
                    ),
            );
    }

    private function previewBullet(string $text): UIElement
    {
        return UI::row()
            ->gap(Unit::px(12))
            ->alignTop()
            ->content(
                (new Icon('check_circle'))->color(Color::orange(500))->size(20),
                UI::text($text)
                    ->fontSize(FontSize::Small)
                    ->color(Color::slate(700))
                    ->paddingTop(Unit::px(2)),
            );
    }

    private function previewFrame(): UIElement
    {
        return UI::column()
            ->grow()
            ->width(Unit::full())
            ->content(
                (new BrowserFrame())
                    ->url('localhost:8080/demo/counter')
                    ->content($this->counterDemo()),
            );
    }

    private function counterDemo(): UIElement
    {
        return UI::column()
            ->paddingY(Unit::px(48))
            ->paddingX(Unit::px(48))
            ->alignCenter()
            ->minHeight(Unit::px(320))
            ->content(
                UI::column()
                    ->maxWidth(Unit::px(280))
                    ->width(Unit::full())
                    ->alignCenter()
                    ->gap(Unit::px(16))
                    ->padding(Unit::px(32))
                    ->rounded(Unit::roundedXl())
                    ->bordered()
                    ->borderColor(Color::slate(100))
                    ->background(Color::slate(50))
                    ->content(
                        UI::text('BRICKPHP COMPONENT')
                            ->fontSize(FontSize::ExtraSmall)
                            ->weight(FontWeight::SemiBold)
                            ->color(Color::slate(500))
                            ->uppercase(),
                        UI::text('0')
                            ->fontSize(FontSize::SixXL)
                            ->weight(FontWeight::Bold)
                            ->color(Color::slate(900)),
                        UI::row()
                            ->gap(Unit::px(16))
                            ->alignMiddle()
                            ->content(
                                $this->counterButton('remove'),
                                $this->counterButton('add'),
                            ),
                    ),
            );
    }

    private function counterButton(string $icon): UIElement
    {
        return UI::button('')
            ->width(Unit::px(48))
            ->height(Unit::px(48))
            ->roundedFull()
            ->bordered()
            ->borderColor(Color::slate(200))
            ->background(Color::white())
            ->color(Color::slate(700))
            ->clickable()
            ->background(Color::orange(500), Pseudo::hover())
            ->color(Color::white(), Pseudo::hover())
            ->borderColor(Color::orange(500), Pseudo::hover())
            ->content((new Icon($icon))->size(20));
    }
}
