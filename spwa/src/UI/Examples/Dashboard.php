<?php

namespace Spwa\UI\Examples;

use Spwa\UI\Color;
use Spwa\UI\FontSize;
use Spwa\UI\FontWeight;
use Spwa\UI\GridColumns;
use Spwa\UI\Shadow;
use Spwa\UI\UI;
use Spwa\UI\UIElement;
use Spwa\UI\Unit;

/**
 * Dashboard example demonstrating the complete UI API.
 */
class Dashboard
{
    public static function build(): UIElement
    {
        return UI::column()
            ->gap(Unit::extraLarge())
            ->padding(Unit::large())
            ->content(
                self::header(),
                self::statsGrid(),
                self::mainContent()
            );
    }

    private static function header(): UIElement
    {
        return UI::row()
            ->alignBetween()
            ->alignMiddle()
            ->padding(Unit::medium())
            ->background(
                Color::blue(600),
                Color::blue(800)->dark()
            )
            ->rounded(Unit::roundedLg())
            ->content(
                // Logo
                UI::text("Dashboard")
                    ->size(FontSize::TwoXL)
                    ->bold()
                    ->color(Color::white()),

                // Navigation
                UI::row()
                    ->gap(Unit::medium())
                    ->content(
                        UI::link("Home", "/")->color(Color::white())->hoverUnderline(),
                        UI::link("Reports", "/reports")->color(Color::white())->hoverUnderline(),
                        UI::link("Settings", "/settings")->color(Color::white())->hoverUnderline()
                    ),

                // User
                UI::button("Sign Out")
                    ->secondary()
                    ->padding(Unit::small())
                    ->paddingHorizontal(Unit::medium())
                    ->rounded(Unit::rounded())
            );
    }

    private static function statsGrid(): UIElement
    {
        return UI::grid(1)
            ->columns(1, GridColumns::count(2)->sm(), GridColumns::count(4)->lg())
            ->gap(Unit::medium())
            ->content(
                self::statCard("Total Users", "12,345", Color::blue(500)),
                self::statCard("Revenue", "$45,678", Color::green(500)),
                self::statCard("Orders", "1,234", Color::purple(500)),
                self::statCard("Conversion", "3.2%", Color::orange(500))
            );
    }

    private static function statCard(string $label, string $value, Color $accent): UIElement
    {
        return UI::card()
            ->content(
                UI::column()
                    ->gap(Unit::small())
                    ->content(
                        UI::text($label)
                            ->size(FontSize::Small)
                            ->color(Color::gray(600)),
                        UI::text($value)
                            ->size(FontSize::ThreeXL)
                            ->bold()
                            ->color($accent)
                    )
            );
    }

    private static function mainContent(): UIElement
    {
        return UI::row()
            ->gap(Unit::large())
            ->content(
                // Main panel
                UI::column()
                    ->extendHorizontal()
                    ->gap(Unit::medium())
                    ->content(
                        UI::heading("Recent Activity", 2)
                            ->size(FontSize::ExtraLarge)
                            ->bold(),

                        self::activityList()
                    ),

                // Sidebar
                UI::column()
                    ->width(Unit::size(80))
                    ->gap(Unit::medium())
                    ->content(
                        UI::heading("Quick Actions", 2)
                            ->size(FontSize::ExtraLarge)
                            ->bold(),

                        UI::button("New Report")
                            ->primary()
                            ->extendHorizontal()
                            ->padding(Unit::small()),

                        UI::button("Export Data")
                            ->outline()
                            ->extendHorizontal()
                            ->padding(Unit::small()),

                        UI::button("Settings")
                            ->ghost()
                            ->extendHorizontal()
                            ->padding(Unit::small())
                    )
            );
    }

    private static function activityList(): UIElement
    {
        return UI::column()
            ->gap(Unit::small())
            ->content(
                self::activityItem("John Doe", "Created a new report", "2 min ago"),
                self::activityItem("Jane Smith", "Updated settings", "15 min ago"),
                self::activityItem("Bob Wilson", "Exported data", "1 hour ago"),
                self::activityItem("Alice Brown", "Added new user", "3 hours ago")
            );
    }

    private static function activityItem(string $user, string $action, string $time): UIElement
    {
        return UI::row()
            ->gap(Unit::medium())
            ->alignMiddle()
            ->padding(Unit::small())
            ->background(
                Color::gray(50),
                Color::gray(800)->dark()
            )
            ->rounded(Unit::rounded())
            ->content(
                UI::avatar("/img/avatar.png", $user)
                    ->size(Unit::size(10)),

                UI::column()
                    ->content(
                        UI::row()
                            ->gap(Unit::xs())
                            ->content(
                                UI::text($user)->bold(),
                                UI::text($action)
                            ),
                        UI::text($time)
                            ->size(FontSize::Small)
                            ->color(Color::gray(500))
                    )
            );
    }
}
