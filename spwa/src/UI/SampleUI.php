<?php

namespace Spwa\UI;

/**
 * Sample UI demonstrating the QuestPDF-inspired fluent UI API.
 *
 * This shows all the capabilities of the UI system:
 * - Stateful values (hover, dark, breakpoints)
 * - Layout components (rows, columns, grid, stack, layers, inlined)
 * - Typography and colors
 * - Responsive design
 * - QuestPDF-style methods (Extend, Shrink, Transform, Alignment)
 */
class SampleUI
{
    /**
     * Main sample demonstrating the complete UI API.
     */
    public static function build(): BaseElement
    {
        return UI::decoration()
            ->before(self::header())
            ->content(self::mainContent())
            ->after(self::footer())
            ->fullScreen();
    }

    /**
     * Header with navigation using QuestPDF Row-style layout.
     */
    private static function header(): BaseElement
    {
        return UI::row()
            ->justifyBetween()
            ->itemsCenter()
            ->padding(Unit::sizeMd())
            ->background(
                Color::blue(600),
                Color::blue(700)->hover(),
                Color::blue(800)->dark()
            )
            ->children(
                // Logo - auto item (takes only needed space)
                UI::text("SPWA")
                    ->text2xl()
                    ->bold()
                    ->color(Color::white()),

                // Navigation - relative item (grows)
                UI::inlined()
                    ->spacing(Unit::sizeMd())
                    ->alignCenter()
                    ->children(
                        UI::link("Home", "/")->color(Color::white())->hoverUnderline(),
                        UI::link("About", "/about")->color(Color::white())->hoverUnderline(),
                        UI::link("Contact", "/contact")->color(Color::white())->hoverUnderline()
                    ),

                // Action button - auto item
                UI::button("Sign In")
                    ->background(Color::white(), Color::blue(100)->hover())
                    ->color(Color::blue(600))
                    ->padding(Unit::sizeSm())
                    ->paddingX(Unit::sizeMd())
                    ->rounded(Unit::rounded())
                    ->medium()
            );
    }

    /**
     * Main content area with cards.
     */
    private static function mainContent(): BaseElement
    {
        return UI::column()
            ->gap(Unit::sizeXl())
            ->padding(Unit::sizeLg())
            ->grow()
            ->children(
                self::heroSection(),
                self::featureCardsSection(),
                self::layersExample(),
                self::transformsExample(),
                self::inlinedExample(),
                self::tableSection()
            );
    }

    /**
     * Hero section using Layers for overlay effect.
     */
    private static function heroSection(): BaseElement
    {
        return UI::layers()
            ->primaryLayer(
                UI::aspectRatio()
                    ->video()
                    ->background(Color::gradient())
            )
            ->layer(
                UI::center()
                    ->extend()
                    ->children(
                        UI::column()
                            ->gap(Unit::sizeMd())
                            ->itemsCenter()
                            ->children(
                                UI::h1("Welcome to SPWA")
                                    ->text4xl()
                                    ->bold()
                                    ->color(Color::white())
                                    ->textCenter(),

                                UI::p("A fluent PHP UI framework inspired by QuestPDF")
                                    ->textLg()
                                    ->color(Color::white()->opacity(80))
                                    ->textCenter(),

                                UI::row()
                                    ->gap(Unit::sizeSm())
                                    ->children(
                                        UI::button("Get Started")->primary(),
                                        UI::button("Learn More")->outline()->color(Color::white())
                                    )
                            )
                    )
            )
            ->rounded(Unit::roundedLg())
            ->overflowHidden();
    }

    /**
     * Feature cards in a responsive grid.
     */
    private static function featureCardsSection(): BaseElement
    {
        return UI::column()
            ->gap(Unit::sizeMd())
            ->children(
                UI::h2("Features")->text2xl()->bold()->color(Color::gray(800)),

                UI::grid(1)
                    ->colsSm(2)
                    ->colsLg(3)
                    ->gap(Unit::sizeLg())
                    ->children(
                        self::featureCard("Fluent API", "Chain methods naturally like QuestPDF."),
                        self::featureCard("Stateful Values", "Handle hover, dark mode, breakpoints elegantly."),
                        self::featureCard("Type Safe", "Full static typing with enums and classes."),
                        self::featureCard("Transforms", "Rotate, scale, translate, and flip elements."),
                        self::featureCard("Layers", "Stack content with absolute positioning."),
                        self::featureCard("Inlined", "Flow layout with automatic wrapping.")
                    )
            );
    }

    /**
     * Individual feature card using QuestPDF styling.
     */
    private static function featureCard(string $title, string $description): BaseElement
    {
        return UI::card()
            ->background(
                Color::white(),
                Color::gray(50)->hover(),
                Color::gray(800)->dark()
            )
            ->transition()
            ->duration(200)
            ->children(
                UI::column()
                    ->gap(Unit::sizeSm())
                    ->children(
                        UI::h3($title)->textXl()->semiBold()->color(Color::gray(900)),
                        UI::p($description)->color(Color::gray(600))
                    )
            );
    }

    /**
     * Layers example demonstrating overlay capabilities.
     */
    private static function layersExample(): BaseElement
    {
        return UI::column()
            ->gap(Unit::sizeMd())
            ->children(
                UI::h2("Layers Example")->text2xl()->bold(),

                UI::layers()
                    ->height(Unit::px(200))
                    ->primaryLayer(
                        UI::element()
                            ->extend()
                            ->background(Color::blue(100))
                            ->rounded(Unit::roundedLg())
                    )
                    ->layer(
                        UI::element()
                            ->absolute()
                            ->top('4')
                            ->left('4')
                            ->padding(Unit::sizeMd())
                            ->background(Color::blue(500))
                            ->rounded(Unit::rounded())
                            ->children(UI::text("Top Left")->color(Color::white()))
                    )
                    ->layer(
                        UI::element()
                            ->absolute()
                            ->bottom('4')
                            ->right('4')
                            ->padding(Unit::sizeMd())
                            ->background(Color::green(500))
                            ->rounded(Unit::rounded())
                            ->children(UI::text("Bottom Right")->color(Color::white()))
                    )
                    ->layer(
                        UI::center()
                            ->extend()
                            ->children(
                                UI::text("Centered Overlay")
                                    ->text2xl()
                                    ->bold()
                                    ->color(Color::blue(800))
                            )
                    )
            );
    }

    /**
     * Transform examples (rotate, scale, flip).
     */
    private static function transformsExample(): BaseElement
    {
        return UI::column()
            ->gap(Unit::sizeMd())
            ->children(
                UI::h2("Transforms")->text2xl()->bold(),

                UI::row()
                    ->gap(Unit::sizeLg())
                    ->wrap()
                    ->children(
                        // Rotate example
                        UI::column()
                            ->gap(Unit::sizeSm())
                            ->itemsCenter()
                            ->children(
                                UI::element()
                                    ->size(Unit::px(64))
                                    ->background(Color::red(500))
                                    ->rounded(Unit::rounded())
                                    ->rotate(45)
                                    ->transform(),
                                UI::text("Rotate 45°")->textSm()
                            ),

                        // Scale example
                        UI::column()
                            ->gap(Unit::sizeSm())
                            ->itemsCenter()
                            ->children(
                                UI::element()
                                    ->size(Unit::px(64))
                                    ->background(Color::green(500))
                                    ->rounded(Unit::rounded())
                                    ->scale(75)
                                    ->transform(),
                                UI::text("Scale 75%")->textSm()
                            ),

                        // Flip example
                        UI::column()
                            ->gap(Unit::sizeSm())
                            ->itemsCenter()
                            ->children(
                                UI::element()
                                    ->size(Unit::px(64))
                                    ->background(Color::blue(500))
                                    ->rounded(Unit::rounded())
                                    ->flipHorizontal()
                                    ->transform()
                                    ->children(UI::text("→")->textXl()->color(Color::white())),
                                UI::text("Flip H")->textSm()
                            ),

                        // Translate example
                        UI::column()
                            ->gap(Unit::sizeSm())
                            ->itemsCenter()
                            ->children(
                                UI::element()
                                    ->size(Unit::px(64))
                                    ->background(Color::purple(500))
                                    ->rounded(Unit::rounded())
                                    ->translateX('4')
                                    ->translateY('-2')
                                    ->transform(),
                                UI::text("Translate")->textSm()
                            )
                    )
            );
    }

    /**
     * Inlined flow layout example.
     */
    private static function inlinedExample(): BaseElement
    {
        $tags = ['PHP', 'Tailwind', 'QuestPDF', 'Fluent API', 'Components', 'Responsive', 'Dark Mode'];

        $tagElements = array_map(
            fn($tag) => UI::pill($tag),
            $tags
        );

        return UI::column()
            ->gap(Unit::sizeMd())
            ->children(
                UI::h2("Inlined Layout")->text2xl()->bold(),

                UI::inlined()
                    ->spacing(Unit::sizeSm())
                    ->alignLeft()
                    ->baselineMiddle()
                    ->children(...$tagElements)
            );
    }

    /**
     * Table section example.
     */
    private static function tableSection(): BaseElement
    {
        return UI::column()
            ->gap(Unit::sizeMd())
            ->children(
                UI::h2("Data Table")->text2xl()->bold(),

                UI::table()
                    ->bordered()
                    ->fullWidth()
                    ->header("Name", "Email", "Role")
                    ->row("John Doe", "john@example.com", "Admin")
                    ->row("Jane Smith", "jane@example.com", "User")
                    ->row("Bob Wilson", "bob@example.com", "Editor")
            );
    }

    /**
     * Footer section.
     */
    private static function footer(): BaseElement
    {
        return UI::row()
            ->justifyBetween()
            ->itemsCenter()
            ->padding(Unit::sizeMd())
            ->background(Color::gray(100), Color::gray(800)->dark())
            ->children(
                UI::text("© 2024 SPWA Framework")->textSm()->color(Color::gray(600)),

                UI::row()
                    ->gap(Unit::sizeMd())
                    ->children(
                        UI::link("Privacy", "/privacy")->textSm()->color(Color::gray(600)),
                        UI::link("Terms", "/terms")->textSm()->color(Color::gray(600))
                    )
            );
    }

    // ============================================================
    // Additional Examples
    // ============================================================

    /**
     * Example demonstrating QuestPDF Row items (Auto, Relative, Constant).
     */
    public static function rowItemsExample(): BaseElement
    {
        return UI::row()
            ->gap(Unit::sizeMd())
            ->padding(Unit::sizeMd())
            ->background(Color::gray(100))
            ->autoItem(
                UI::element()
                    ->padding(Unit::sizeSm())
                    ->background(Color::blue(200))
                    ->children(UI::text("Auto")->color(Color::blue(800)))
            )
            ->relativeItem(
                UI::element()
                    ->padding(Unit::sizeSm())
                    ->background(Color::green(200))
                    ->children(UI::text("Relative (grows)")->color(Color::green(800))),
                2
            )
            ->relativeItem(
                UI::element()
                    ->padding(Unit::sizeSm())
                    ->background(Color::green(300))
                    ->children(UI::text("Relative (grows)")->color(Color::green(800)))
            )
            ->constantItem(
                UI::element()
                    ->padding(Unit::sizeSm())
                    ->background(Color::red(200))
                    ->children(UI::text("Constant 100px")->color(Color::red(800))),
                100
            );
    }

    /**
     * Example demonstrating all text styling options.
     */
    public static function textStylingExample(): BaseElement
    {
        return UI::column()
            ->gap(Unit::sizeMd())
            ->children(
                // Font weights
                UI::row()->gap(Unit::sizeMd())->wrap()->children(
                    UI::text("Thin")->thin(),
                    UI::text("Light")->light(),
                    UI::text("Normal")->normalWeight(),
                    UI::text("Medium")->medium(),
                    UI::text("SemiBold")->semiBold(),
                    UI::text("Bold")->bold(),
                    UI::text("ExtraBold")->extraBold(),
                    UI::text("Black")->black()
                ),

                // Decorations
                UI::row()->gap(Unit::sizeMd())->wrap()->children(
                    UI::text("Underline")->underline(),
                    UI::text("Strikethrough")->strikethrough(),
                    UI::text("Overline")->overline(),
                    UI::text("Wavy")->underline()->decorationWavy()->decorationColor(Color::red(500))
                ),

                // Script positions
                UI::row()->gap(Unit::sizeSm())->itemsBaseline()->children(
                    UI::text("H"),
                    UI::text("2")->subscript(),
                    UI::text("O and E=mc"),
                    UI::text("2")->superscript()
                ),

                // Text transforms
                UI::row()->gap(Unit::sizeMd())->children(
                    UI::text("uppercase")->uppercase(),
                    UI::text("lowercase")->lowercase(),
                    UI::text("capitalize")->capitalize()
                )
            );
    }

    /**
     * Example demonstrating semantic HTML elements.
     */
    public static function semanticElementsExample(): BaseElement
    {
        return UI::column()
            ->gap(Unit::sizeMd())
            ->children(
                UI::strong("This is strong text"),
                UI::em("This is emphasized text"),
                UI::mark("This is highlighted text"),
                UI::del("This text was deleted"),
                UI::ins("This text was inserted"),
                UI::code("const x = 42;"),
                UI::kbd("Ctrl"),
                UI::text(" + ")->inline(),
                UI::kbd("C"),
                UI::blockquote("This is a blockquote with some inspiring text."),
                UI::small("This is small print.")
            );
    }

    /**
     * Example demonstrating AspectRatio component.
     */
    public static function aspectRatioExample(): BaseElement
    {
        return UI::row()
            ->gap(Unit::sizeMd())
            ->children(
                UI::column()->gap(Unit::sizeSm())->width(Unit::px(150))->children(
                    UI::aspectRatio()->square()->background(Color::blue(200))->children(
                        UI::center()->extend()->children(UI::text("1:1"))
                    ),
                    UI::text("Square")->textSm()->textCenter()
                ),

                UI::column()->gap(Unit::sizeSm())->width(Unit::px(200))->children(
                    UI::aspectRatio()->video()->background(Color::green(200))->children(
                        UI::center()->extend()->children(UI::text("16:9"))
                    ),
                    UI::text("Video")->textSm()->textCenter()
                ),

                UI::column()->gap(Unit::sizeSm())->width(Unit::px(150))->children(
                    UI::aspectRatio()->portrait()->background(Color::purple(200))->children(
                        UI::center()->extend()->children(UI::text("3:4"))
                    ),
                    UI::text("Portrait")->textSm()->textCenter()
                )
            );
    }

    /**
     * Example demonstrating Line elements.
     */
    public static function lineExample(): BaseElement
    {
        return UI::column()
            ->gap(Unit::sizeMd())
            ->children(
                UI::text("Horizontal Lines")->bold(),
                UI::lineHorizontal()->lineColor(Color::gray(300)),
                UI::lineHorizontal()->thickness(2)->lineColor(Color::blue(500)),
                UI::lineHorizontal()->dashed()->lineColor(Color::red(500)),
                UI::lineHorizontal()->dotted()->lineColor(Color::green(500)),

                UI::text("With Vertical Line")->bold(),
                UI::row()
                    ->height(Unit::px(100))
                    ->gap(Unit::sizeMd())
                    ->children(
                        UI::element()->extend()->background(Color::gray(100)),
                        UI::lineVertical()->lineColor(Color::gray(400)),
                        UI::element()->extend()->background(Color::gray(100))
                    )
            );
    }

    /**
     * Debug/Placeholder example.
     */
    public static function debugExample(): BaseElement
    {
        return UI::column()
            ->gap(Unit::sizeMd())
            ->children(
                UI::text("Debug Placeholders")->bold(),

                UI::row()
                    ->gap(Unit::sizeMd())
                    ->children(
                        UI::placeholder("Header"),
                        UI::placeholder("Content")->grow(),
                        UI::placeholder("Sidebar")
                    )
            );
    }
}
