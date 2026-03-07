<?php

namespace Spwa\UI;

/**
 * Static factory for creating UI elements.
 *
 * Usage:
 *   UI::column()
 *       ->gap(Unit::base())
 *       ->content(
 *           UI::text("Hello")->bold(),
 *           UI::text("World")
 *       )
 */
class UI
{
    // ============================================================
    // Layout Components
    // ============================================================

    /**
     * Create a vertical stack (column layout).
     */
    public static function column(): Column
    {
        return new Column();
    }

    /**
     * Create a horizontal stack (row layout).
     */
    public static function row(): Row
    {
        return new Row();
    }

    /**
     * Create stacked layers (z-axis layout).
     */
    public static function layers(): Layers
    {
        return new Layers();
    }

    /**
     * Create a grid layout.
     */
    public static function grid(int $columns = 1): Grid
    {
        return new Grid($columns);
    }

    /**
     * Create an inline flow layout with wrapping.
     */
    public static function inlined(): Inlined
    {
        return new Inlined();
    }

    /**
     * Create a basic container.
     */
    public static function container(): Container
    {
        return new Container();
    }

    // ============================================================
    // Content Components
    // ============================================================

    /**
     * Create a text element.
     */
    public static function text(string $content): Text
    {
        return new Text($content);
    }

    /**
     * Create a heading.
     */
    public static function heading(string $content, int $level = 1): Text
    {
        return (new Text($content))->heading($level);
    }

    /**
     * Create a paragraph.
     */
    public static function paragraph(string $content): Text
    {
        return (new Text($content))->paragraph();
    }

    /**
     * Create an image element.
     */
    public static function image(string $src, string $alt = ''): Image
    {
        return new Image($src, $alt);
    }

    /**
     * Create a button.
     */
    public static function button(string $label): Button
    {
        return new Button($label);
    }

    /**
     * Create a link.
     */
    public static function link(string $label, string $href): Link
    {
        return new Link($label, $href);
    }

    /**
     * Create a table.
     */
    public static function table(): Table
    {
        return new Table();
    }

    /**
     * Create a table row.
     */
    public static function tableRow(TableCell|TableHeading ...$cells): TableRow
    {
        return new TableRow(...$cells);
    }

    // ============================================================
    // Form Components
    // ============================================================

    /**
     * Create an input element.
     */
    public static function input(): Input
    {
        return new Input();
    }

    /**
     * Create a textarea element.
     */
    public static function textarea(): Textarea
    {
        return new Textarea();
    }

    // ============================================================
    // HTML Elements
    // ============================================================

    /**
     * Create an anchor element (link with child content).
     */
    public static function a(?string $href = null): Anchor
    {
        return new Anchor($href);
    }

    /**
     * Create an SVG element.
     */
    public static function svg(): Svg
    {
        return new Svg();
    }

    // ============================================================
    // Utility Components
    // ============================================================

    /**
     * Create a spacer (flex grow element).
     */
    public static function spacer(): Container
    {
        $spacer = new Container();
        $spacer->extend();
        return $spacer;
    }

    /**
     * Create a divider line.
     */
    public static function divider(): Container
    {
        $divider = new Container();
        $divider->extendHorizontal();
        $divider->height(Unit::px(1));
        $divider->background(Color::gray(200));
        return $divider;
    }

    // ============================================================
    // Preset Components
    // ============================================================

    /**
     * Create a card container.
     */
    public static function card(): Container
    {
        $card = new Container();
        $card->background(Color::white());
        $card->rounded(Unit::roundedLg());
        $card->shadow(Shadow::Medium);
        $card->padding(Unit::medium());
        return $card;
    }

    /**
     * Create a badge/tag element.
     */
    public static function badge(string $text): Text
    {
        return (new Text($text))
            ->fontSize(FontSize::ExtraSmall)
            ->weight(FontWeight::Medium)
            ->padding(Unit::xs())
            ->paddingHorizontal(Unit::small())
            ->roundedFull()
            ->background(Color::gray(100))
            ->color(Color::gray(800));
    }

    /**
     * Create a pill element.
     */
    public static function pill(string $text): Text
    {
        return (new Text($text))
            ->fontSize(FontSize::Small)
            ->padding(Unit::small())
            ->paddingHorizontal(Unit::medium())
            ->roundedFull()
            ->background(Color::blue(500))
            ->color(Color::white());
    }

    /**
     * Create an avatar (circular image).
     */
    public static function avatar(string $src, string $alt = ''): Image
    {
        return (new Image($src, $alt))
            ->roundedFull()
            ->cover();
    }

    /**
     * Create a centered container.
     */
    public static function center(): Row
    {
        return (new Row())->center();
    }

    // ============================================================
    // Style Output
    // ============================================================

    /**
     * Generate CSS from collected styles.
     *
     * @param array<string, array<string, string>> $styles
     */
    public static function printStyles(array $styles): string
    {
        return StyleGenerator::from($styles)->toCSS();
    }
}
