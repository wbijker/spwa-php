<?php

namespace Spwa\UI\Examples;

use Spwa\Js\Console;
use Spwa\UI\Align;
use Spwa\UI\Color;
use Spwa\UI\Cursor;
use Spwa\UI\Direction;
use Spwa\UI\FontSize;
use Spwa\UI\FontWeight;
use Spwa\UI\GridColumns;
use Spwa\UI\Shadow;
use Spwa\UI\Table;
use Spwa\UI\UI;
use Spwa\UI\UIElement;
use Spwa\UI\Unit;
use Spwa\VNode\Component;
use Spwa\VNode\VNode;

/**
 * Complete showcase of all UI elements, properties, and pseudo classes.
 */
class Showcase extends Component
{
    private int $a = 0;
    private int $b = 0;

    protected function getState(): array
    {
        return ['a' => $this->a, 'b' => $this->b];
    }

    protected function setState(array $state): void
    {
        $this->a = $state['a'] ?? 0;
        $this->b = $state['b'] ?? 0;
    }

    protected function build(): VNode
    {
        return UI::column()
            ->gap(Unit::extraLarge())
            ->padding(Unit::large())
            ->background(Color::gray(50))
            ->content(
                new Counter($this->a, function (int $v) {
                    $this->a = $v;
                    Console::log("Showcase state updated: a={$this->a}, b={$this->b}");
                }),
                self::header(),
                new Counter($this->b, function (int $v) {
                    $this->b = $v;
                    Console::log("Showcase state updated: a={$this->a}, b={$this->b}");
                }),
                UI::text("Sum: " . ($this->a + $this->b))
                    ->fontSize(FontSize::TwoXL)
                    ->weight(FontWeight::Bold)
                    ->color(Color::indigo(600))
                    ->padding(Unit::medium()),
                self::colorPalette(),
                self::typography(),
                self::buttons(),
                self::cards(),
                self::tables(),
                self::layoutShowcase(),
                self::interactiveStates(),
                self::responsiveDemo(),
                self::footer()
            );
    }

    private static function header(): UIElement
    {
        return UI::column()
            ->alignCenter()
            ->gap(Unit::medium())
            ->padding(Unit::extraLarge())
            ->background(Color::indigo(600))
            ->rounded(Unit::roundedXl())
            ->content(
                UI::text("SPWA UI Showcase")
                    ->fontSize(FontSize::FourXL)
                    ->weight(FontWeight::Bold)
                    ->color(Color::white()),
                UI::text("A complete demonstration of all UI elements and features")
                    ->fontSize(FontSize::Large)
                    ->color(Color::indigo(200))
            );
    }

    private static function sectionTitle(string $title): UIElement
    {
        return UI::text($title)
            ->fontSize(FontSize::TwoXL)
            ->weight(FontWeight::SemiBold)
            ->color(Color::gray(800))
            ->paddingVertical(Unit::medium());
    }

    private static function colorPalette(): UIElement
    {
        $shades = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900];
        $colors = [
            'Red' => array_map(fn($s) => Color::red($s), $shades),
            'Orange' => array_map(fn($s) => Color::orange($s), $shades),
            'Amber' => array_map(fn($s) => Color::amber($s), $shades),
            'Yellow' => array_map(fn($s) => Color::yellow($s), $shades),
            'Lime' => array_map(fn($s) => Color::lime($s), $shades),
            'Green' => array_map(fn($s) => Color::green($s), $shades),
            'Emerald' => array_map(fn($s) => Color::emerald($s), $shades),
            'Teal' => array_map(fn($s) => Color::teal($s), $shades),
            'Cyan' => array_map(fn($s) => Color::cyan($s), $shades),
            'Sky' => array_map(fn($s) => Color::sky($s), $shades),
            'Blue' => array_map(fn($s) => Color::blue($s), $shades),
            'Indigo' => array_map(fn($s) => Color::indigo($s), $shades),
            'Violet' => array_map(fn($s) => Color::violet($s), $shades),
            'Purple' => array_map(fn($s) => Color::purple($s), $shades),
            'Fuchsia' => array_map(fn($s) => Color::fuchsia($s), $shades),
            'Pink' => array_map(fn($s) => Color::pink($s), $shades),
            'Rose' => array_map(fn($s) => Color::rose($s), $shades),
            'Slate' => array_map(fn($s) => Color::slate($s), $shades),
            'Gray' => array_map(fn($s) => Color::gray($s), $shades),
            'Zinc' => array_map(fn($s) => Color::zinc($s), $shades),
            'Neutral' => array_map(fn($s) => Color::neutral($s), $shades),
            'Stone' => array_map(fn($s) => Color::stone($s), $shades),
            'Taupe' => array_map(fn($s) => Color::taupe($s), $shades),
            'Mauve' => array_map(fn($s) => Color::mauve($s), $shades),
            'Mist' => array_map(fn($s) => Color::mist($s), $shades),
            'Olive' => array_map(fn($s) => Color::olive($s), $shades),
        ];

        $rows = [];
        foreach ($colors as $name => $shades) {
            $swatches = [];
            foreach ($shades as $color) {
                $swatches[] = UI::container()
                    ->size(Unit::value(12))
                    ->rounded(Unit::rounded())
                    ->background($color);
            }
            $rows[] = UI::row()
                ->gap(Unit::small())
                ->alignMiddle()
                ->content(
                    UI::text($name)
                        ->fontSize(FontSize::Small)
                        ->weight(FontWeight::Medium)
                        ->color(Color::gray(600))
                        ->width(Unit::value(20)),
                    ...$swatches
                );
        }

        return UI::column()
            ->gap(Unit::medium())
            ->content(
                self::sectionTitle("Color Palette"),
                UI::column()->gap(Unit::small())->content(...$rows)
            );
    }

    private static function typography(): UIElement
    {
        return UI::column()
            ->gap(Unit::medium())
            ->content(
                self::sectionTitle("Typography"),
                UI::column()
                    ->gap(Unit::small())
                    ->padding(Unit::large())
                    ->background(Color::white())
                    ->rounded(Unit::roundedLg())
                    ->shadow(Shadow::Small)
                    ->content(
                        UI::text("Heading 1 - Extra Large")->fontSize(FontSize::FourXL)->weight(FontWeight::Bold),
                        UI::text("Heading 2 - Large")->fontSize(FontSize::ThreeXL)->weight(FontWeight::SemiBold),
                        UI::text("Heading 3 - Medium")->fontSize(FontSize::TwoXL)->weight(FontWeight::Medium),
                        UI::text("Body text - Base size with normal weight. Lorem ipsum dolor sit amet, consectetur adipiscing elit.")->fontSize(FontSize::Base),
                        UI::text("Small text - Used for captions and labels")->fontSize(FontSize::Small)->color(Color::gray(500)),
                        UI::text("Extra small - Fine print")->fontSize(FontSize::ExtraSmall)->color(Color::gray(400)),
                        UI::row()->gap(Unit::medium())->content(
                            UI::text("Light")->weight(FontWeight::Light),
                            UI::text("Normal")->weight(FontWeight::Normal),
                            UI::text("Medium")->weight(FontWeight::Medium),
                            UI::text("Semibold")->weight(FontWeight::SemiBold),
                            UI::text("Bold")->weight(FontWeight::Bold),
                            UI::text("Black")->weight(FontWeight::Black)
                        )
                    )
            );
    }

    private static function buttons(): UIElement
    {
        return UI::column()
            ->gap(Unit::medium())
            ->content(
                self::sectionTitle("Buttons & Interactive Elements"),
                UI::row()
                    ->gap(Unit::medium())
                    ->wrap()
                    ->content(
                    // Primary button with hover
                        UI::button("Primary")
                            ->background(Color::blue(500), Color::blue(600)->hover())
                            ->color(Color::white())
                            ->padding(Unit::small())
                            ->paddingHorizontal(Unit::large())
                            ->rounded(Unit::rounded())
                            ->shadow(Shadow::Small),

                        // Secondary button
                        UI::button("Secondary")
                            ->background(Color::gray(200), Color::gray(300)->hover())
                            ->color(Color::gray(800))
                            ->padding(Unit::small())
                            ->paddingHorizontal(Unit::large())
                            ->rounded(Unit::rounded()),

                        // Success button
                        UI::button("Success")
                            ->background(Color::green(500), Color::green(600)->hover())
                            ->color(Color::white())
                            ->padding(Unit::small())
                            ->paddingHorizontal(Unit::large())
                            ->rounded(Unit::rounded()),

                        // Danger button
                        UI::button("Danger")
                            ->background(Color::red(500), Color::red(600)->hover())
                            ->color(Color::white())
                            ->padding(Unit::small())
                            ->paddingHorizontal(Unit::large())
                            ->rounded(Unit::rounded()),

                        // Outline button
                        UI::button("Outline")
                            ->background(Color::transparent(), Color::indigo(50)->hover())
                            ->color(Color::indigo(600))
                            ->padding(Unit::small())
                            ->paddingHorizontal(Unit::large())
                            ->rounded(Unit::rounded()),

                        // Pill button
                        UI::button("Pill Button")
                            ->background(Color::purple(500), Color::purple(600)->hover())
                            ->color(Color::white())
                            ->padding(Unit::small())
                            ->paddingHorizontal(Unit::extraLarge())
                            ->roundedFull()
                    ),
                UI::row()
                    ->gap(Unit::medium())
                    ->content(
                        UI::link("Regular Link", "#")
                            ->color(Color::blue(600))
                            ->underline(),
                        UI::link("Hover Underline", "#")
                            ->color(Color::blue(600))
                            ->noUnderline()
                            ->hoverUnderline()
                    )
            );
    }

    private static function cards(): UIElement
    {
        return UI::column()
            ->gap(Unit::medium())
            ->content(
                self::sectionTitle("Cards & Containers"),
                UI::grid(3)
                    ->gap(Unit::large())
                    ->content(
                    // Simple card
                        UI::column()
                            ->gap(Unit::medium())
                            ->padding(Unit::large())
                            ->background(Color::white())
                            ->rounded(Unit::roundedLg())
                            ->shadow(Shadow::Medium)
                            ->content(
                                UI::text("Simple Card")->weight(FontWeight::SemiBold)->fontSize(FontSize::Large),
                                UI::text("A basic card with padding, rounded corners, and shadow.")
                                    ->color(Color::gray(600))
                                    ->fontSize(FontSize::Small)
                            ),


                        // Card with image
                        UI::column()
                            ->background(Color::white())
                            ->rounded(Unit::roundedLg())
                            ->shadow(Shadow::Medium)
                            ->overflow()
                            ->content(
                                UI::container()
                                    ->height(Unit::value(32))
                                    ->background(Color::gradient()),
                                UI::column()
                                    ->gap(Unit::small())
                                    ->padding(Unit::large())
                                    ->content(
                                        UI::text("Featured")->weight(FontWeight::SemiBold)->fontSize(FontSize::Large),
                                        UI::text("Card with gradient header area.")
                                            ->color(Color::gray(600))
                                            ->fontSize(FontSize::Small)
                                    )
                            ),

                        // Elevated card
                        UI::column()
                            ->gap(Unit::medium())
                            ->padding(Unit::large())
                            ->background(Color::white())
                            ->rounded(Unit::roundedXl())
                            ->shadow(Shadow::ExtraLarge)
                            ->content(
                                UI::text("Elevated")->weight(FontWeight::SemiBold)->fontSize(FontSize::Large),
                                UI::text("Extra large shadow for depth.")
                                    ->color(Color::gray(600))
                                    ->fontSize(FontSize::Small),
                                UI::badge("NEW")
                            )
                    )
            );
    }

    private static function tables(): UIElement
    {
        return UI::column()
            ->gap(Unit::medium())
            ->content(
                self::sectionTitle("Tables"),
                UI::container()
                    ->background(Color::white())
                    ->rounded(Unit::roundedLg())
                    ->shadow(Shadow::Small)
                    ->overflow()
                    ->content(
                        UI::table()
                            ->head(
                                Table::row(
                                    Table::heading()->content(UI::text("Name")),
                                    Table::heading()->content(UI::text("Email")),
                                    Table::heading()->content(UI::text("Role")),
                                    Table::heading()->content(UI::text("Status"))
                                )
                                    ->background(Color::gray(50))
                            )
                            ->body(
                                Table::row(
                                    Table::cell()->content("John Doe"),
                                    Table::cell()->content("john@example.com"),
                                    Table::cell()->content("Admin"),
                                    Table::cell()->content(
                                        UI::text("Active")
                                            ->fontSize(FontSize::ExtraSmall)
                                            ->padding(Unit::xs())
                                            ->paddingHorizontal(Unit::small())
                                            ->roundedFull()
                                            ->background(Color::green(100))
                                            ->color(Color::green(800))
                                    )
                                )
                                    ->borderColor(Color::gray(200))
                                    ->bordered(),
                                Table::row(
                                    Table::cell()->content("Jane Smith"),
                                    Table::cell()->content("jane@example.com"),
                                    Table::cell()->content("Editor"),
                                    Table::cell()->content(
                                        UI::text("Active")
                                            ->fontSize(FontSize::ExtraSmall)
                                            ->padding(Unit::xs())
                                            ->paddingHorizontal(Unit::small())
                                            ->roundedFull()
                                            ->background(Color::green(100))
                                            ->color(Color::green(800))
                                    )
                                )
                                    ->borderColor(Color::gray(200))
                                    ->bordered(),
                                Table::row(
                                    Table::cell()->content("Bob Wilson"),
                                    Table::cell()->content("bob@example.com"),
                                    Table::cell()->content("Viewer"),
                                    Table::cell()->content(
                                        UI::text("Inactive")
                                            ->fontSize(FontSize::ExtraSmall)
                                            ->padding(Unit::xs())
                                            ->paddingHorizontal(Unit::small())
                                            ->roundedFull()
                                            ->background(Color::gray(100))
                                            ->color(Color::gray(600))
                                    )
                                )
                                    ->borderColor(Color::gray(200))
                                    ->bordered()
                            )
                    )
            );
    }

    private static function layoutShowcase(): UIElement
    {
        return UI::column()
            ->gap(Unit::medium())
            ->content(
                self::sectionTitle("Layout Components"),

                // Row layout
                UI::text("Row Layout")->weight(FontWeight::Medium)->color(Color::gray(700)),
                UI::row()
                    ->gap(Unit::medium())
                    ->padding(Unit::medium())
                    ->background(Color::white())
                    ->rounded(Unit::rounded())
                    ->content(
                        UI::container()->size(Unit::value(16))->background(Color::blue(400))->rounded(Unit::rounded()),
                        UI::container()->size(Unit::value(16))->background(Color::blue(500))->rounded(Unit::rounded()),
                        UI::container()->size(Unit::value(16))->background(Color::blue(600))->rounded(Unit::rounded())
                    ),

                // Column layout
                UI::text("Column Layout")->weight(FontWeight::Medium)->color(Color::gray(700)),
                UI::column()
                    ->gap(Unit::small())
                    ->padding(Unit::medium())
                    ->background(Color::white())
                    ->rounded(Unit::rounded())
                    ->content(
                        UI::container()->height(Unit::value(8))->extendHorizontal()->background(Color::green(400))->rounded(Unit::rounded()),
                        UI::container()->height(Unit::value(8))->extendHorizontal()->background(Color::green(500))->rounded(Unit::rounded()),
                        UI::container()->height(Unit::value(8))->extendHorizontal()->background(Color::green(600))->rounded(Unit::rounded())
                    ),

                // Grid layout
                UI::text("Grid Layout")->weight(FontWeight::Medium)->color(Color::gray(700)),
                UI::grid(4)
                    ->gap(Unit::small())
                    ->padding(Unit::medium())
                    ->background(Color::white())
                    ->rounded(Unit::rounded())
                    ->content(
                        UI::container()->height(Unit::value(12))->background(Color::purple(300))->rounded(Unit::rounded()),
                        UI::container()->height(Unit::value(12))->background(Color::purple(400))->rounded(Unit::rounded()),
                        UI::container()->height(Unit::value(12))->background(Color::purple(500))->rounded(Unit::rounded()),
                        UI::container()->height(Unit::value(12))->background(Color::purple(600))->rounded(Unit::rounded()),
                        UI::container()->height(Unit::value(12))->background(Color::purple(400))->rounded(Unit::rounded()),
                        UI::container()->height(Unit::value(12))->background(Color::purple(500))->rounded(Unit::rounded()),
                        UI::container()->height(Unit::value(12))->background(Color::purple(600))->rounded(Unit::rounded()),
                        UI::container()->height(Unit::value(12))->background(Color::purple(700))->rounded(Unit::rounded())
                    ),

                // Inlined layout
                UI::text("Inlined (Wrapping) Layout")->weight(FontWeight::Medium)->color(Color::gray(700)),
                UI::inlined()
                    ->spacing(Unit::small())
                    ->padding(Unit::medium())
                    ->background(Color::white())
                    ->rounded(Unit::rounded())
                    ->content(
                        UI::badge("PHP"),
                        UI::badge("JavaScript"),
                        UI::badge("TypeScript"),
                        UI::badge("HTML"),
                        UI::badge("CSS"),
                        UI::badge("React"),
                        UI::badge("Vue"),
                        UI::badge("Laravel"),
                        UI::badge("Tailwind")
                    )
            );
    }

    private static function interactiveStates(): UIElement
    {
        return UI::column()
            ->gap(Unit::medium())
            ->content(
                self::sectionTitle("Pseudo Classes & Interactive States"),
                UI::row()
                    ->gap(Unit::large())
                    ->wrap()
                    ->content(
                    // Hover state
                        UI::column()
                            ->gap(Unit::small())
                            ->alignCenter()
                            ->content(
                                UI::container()
                                    ->cursor(Cursor::Pointer)
                                    ->background(Color::blue(400), Color::blue(600)->hover())
                                    ->size(Unit::value(20))
                                    ->rounded(Unit::roundedLg())
                                    ->shadow(Shadow::Small),
                                UI::text("Hover me")
                                    ->fontSize(FontSize::Small)
                                    ->color(Color::gray(600))
                            ),

                        // Active state
                        UI::column()
                            ->gap(Unit::small())
                            ->alignCenter()
                            ->content(
                                UI::container()
                                    ->size(Unit::value(20))
                                    ->background(Color::green(400), Color::green(700)->active())
                                    ->rounded(Unit::roundedLg())
                                    ->shadow(Shadow::Small),
                                UI::text("Click me")->fontSize(FontSize::Small)->color(Color::gray(600))
                            ),

                        // Focus state
                        UI::column()
                            ->gap(Unit::small())
                            ->alignCenter()
                            ->content(
                                UI::button("Focus")
                                    ->background(Color::purple(400), Color::purple(600)->focus())
                                    ->color(Color::white())
                                    ->padding(Unit::medium())
                                    ->paddingHorizontal(Unit::large())
                                    ->rounded(Unit::rounded()),
                                UI::text("Tab to focus")->fontSize(FontSize::Small)->color(Color::gray(600))
                            ),

                        // Combined states
                        UI::column()
                            ->gap(Unit::small())
                            ->alignCenter()
                            ->content(
                                UI::button("Multi-state")
                                    ->background(
                                        Color::orange(400),
                                        Color::orange(500)->hover(),
                                        Color::orange(700)->active()
                                    )
                                    ->color(Color::white())
                                    ->padding(Unit::medium())
                                    ->paddingHorizontal(Unit::large())
                                    ->rounded(Unit::rounded()),
                                UI::text("Hover + Active")->fontSize(FontSize::Small)->color(Color::gray(600))
                            )
                    ),

                // Dark mode demo
                UI::text("Dark Mode Support")->weight(FontWeight::Medium)->color(Color::gray(700))->paddingTop(Unit::medium()),
                UI::row()
                    ->gap(Unit::medium())
                    ->padding(Unit::large())
                    ->background(Color::white(), Color::gray(800)->dark())
                    ->rounded(Unit::roundedLg())
                    ->content(
                        UI::text("This text adapts to dark mode")
                            ->color(Color::gray(800), Color::gray(100)->dark()),
                        UI::container()
                            ->size(Unit::value(8))
                            ->background(Color::blue(500), Color::blue(400)->dark())
                            ->roundedFull()
                    )
            );
    }

    private static function responsiveDemo(): UIElement
    {
        return UI::column()
            ->gap(Unit::medium())
            ->content(
                self::sectionTitle("Responsive Design"),
                UI::text("Resize your browser to see changes")->fontSize(FontSize::Small)->color(Color::gray(500)),
                UI::column()
                    ->direction(Direction::row()->md())
                    ->gap(Unit::medium(), Unit::large()->md())
                    ->padding(Unit::medium(), Unit::large()->lg())
                    ->background(Color::white())
                    ->rounded(Unit::roundedLg())
                    ->shadow(Shadow::Small)
                    ->content(
                        UI::container()
                            ->extendHorizontal()
                            ->height(Unit::value(24))
                            ->background(Color::cyan(400))
                            ->rounded(Unit::rounded()),
                        UI::container()
                            ->extendHorizontal()
                            ->height(Unit::value(24))
                            ->background(Color::cyan(500))
                            ->rounded(Unit::rounded()),
                        UI::container()
                            ->extendHorizontal()
                            ->height(Unit::value(24))
                            ->background(Color::cyan(600))
                            ->rounded(Unit::rounded())
                    ),
                UI::grid(1)
                    ->columns(1, GridColumns::count(2)->md(), GridColumns::count(4)->lg())
                    ->gap(Unit::medium())
                    ->content(
                        UI::container()->height(Unit::value(16))->background(Color::teal(400))->rounded(Unit::rounded()),
                        UI::container()->height(Unit::value(16))->background(Color::teal(500))->rounded(Unit::rounded()),
                        UI::container()->height(Unit::value(16))->background(Color::teal(600))->rounded(Unit::rounded()),
                        UI::container()->height(Unit::value(16))->background(Color::teal(700))->rounded(Unit::rounded())
                    )
            );
    }

    private static function footer(): UIElement
    {
        return UI::row()
            ->alignCenter()
            ->alignMiddle()
            ->padding(Unit::large())
            ->background(Color::gray(800))
            ->rounded(Unit::roundedLg())
            ->content(
                UI::text("Built with SPWA UI Framework")
                    ->color(Color::gray(400))
                    ->fontSize(FontSize::Small)
            );
    }
}
