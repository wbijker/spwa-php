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

    public static function column(): Column
    {
        return new Column();
    }

    public static function row(): Row
    {
        return new Row();
    }

    public static function layers(): Layers
    {
        return new Layers();
    }

    public static function grid(int $columns = 1): Grid
    {
        return new Grid($columns);
    }

    public static function inlined(): Inlined
    {
        return new Inlined();
    }

    public static function container(): Container
    {
        return new Container();
    }

    // ============================================================
    // Semantic Elements
    // ============================================================

    public static function section(): Section
    {
        return new Section();
    }

    public static function article(): Article
    {
        return new Article();
    }

    public static function aside(): Aside
    {
        return new Aside();
    }

    public static function nav(): Nav
    {
        return new Nav();
    }

    public static function header(): Header
    {
        return new Header();
    }

    public static function footer(): Footer
    {
        return new Footer();
    }

    public static function main(): Main
    {
        return new Main();
    }

    public static function figure(): Figure
    {
        return new Figure();
    }

    public static function details(): Details
    {
        return new Details();
    }

    public static function dialog(): Dialog
    {
        return new Dialog();
    }

    public static function address(): Address
    {
        return new Address();
    }

    public static function time(string $content): Time
    {
        return new Time($content);
    }

    // ============================================================
    // Content Components
    // ============================================================

    public static function text(string $content): Text
    {
        return new Text($content);
    }

    public static function heading(string $content, int $level = 1): Text
    {
        return new Text($content);
    }

    public static function paragraph(string $content): Text
    {
        return new Text($content);
    }

    public static function image(string $src, string $alt = ''): Image
    {
        return new Image($src, $alt);
    }

    public static function button(string $label): Button
    {
        return new Button($label);
    }

    public static function link(string $label, string $href): Link
    {
        return new Link($label, $href);
    }

    public static function a(?string $href = null): Anchor
    {
        return new Anchor($href);
    }

    public static function svg(): Svg
    {
        return new Svg();
    }

    // ============================================================
    // Table Components
    // ============================================================

    public static function table(): Table
    {
        return new Table();
    }

    public static function tableRow(TableCell|TableHeading ...$cells): TableRow
    {
        return new TableRow(...$cells);
    }

    // ============================================================
    // Form Components
    // ============================================================

    public static function form(): Form
    {
        return new Form();
    }

    public static function input(): Input
    {
        return new Input();
    }

    public static function textarea(): Textarea
    {
        return new Textarea();
    }

    public static function select(): Select
    {
        return new Select();
    }

    public static function option(string $label, ?string $value = null): Option
    {
        return new Option($label, $value);
    }

    public static function optgroup(string $label): Optgroup
    {
        return new Optgroup($label);
    }

    public static function label(?string $text = null): Label
    {
        return new Label($text);
    }

    public static function fieldset(): Fieldset
    {
        return new Fieldset();
    }

    public static function datalist(): Datalist
    {
        return new Datalist();
    }

    public static function output(): Output
    {
        return new Output();
    }

    public static function progress(): Progress
    {
        return new Progress();
    }

    public static function meter(): Meter
    {
        return new Meter();
    }

    // ============================================================
    // List Components
    // ============================================================

    public static function ul(): Ul
    {
        return new Ul();
    }

    public static function ol(): Ol
    {
        return new Ol();
    }

    public static function li(string|UIElement|null $content = null): Li
    {
        return new Li($content);
    }

    public static function dl(): Dl
    {
        return new Dl();
    }

    public static function dt(string|UIElement|null $content = null): Dt
    {
        return new Dt($content);
    }

    public static function dd(string|UIElement|null $content = null): Dd
    {
        return new Dd($content);
    }

    public static function menu(): Menu
    {
        return new Menu();
    }

    // ============================================================
    // Media Components
    // ============================================================

    public static function video(): Video
    {
        return new Video();
    }

    public static function audio(): Audio
    {
        return new Audio();
    }

    public static function source(string $src): Source
    {
        return new Source($src);
    }

    public static function track(string $src): Track
    {
        return new Track($src);
    }

    public static function iframe(): Iframe
    {
        return new Iframe();
    }

    public static function canvas(): Canvas
    {
        return new Canvas();
    }

    public static function picture(): Picture
    {
        return new Picture();
    }

    // ============================================================
    // Inline/Text Components
    // ============================================================

    public static function code(string $content = ''): Code
    {
        return new Code($content);
    }

    public static function pre(?string $content = null): Pre
    {
        return new Pre($content);
    }

    public static function blockquote(?string $content = null): Blockquote
    {
        return new Blockquote($content);
    }

    public static function q(string $content): Q
    {
        return new Q($content);
    }

    public static function cite(string $content): Cite
    {
        return new Cite($content);
    }

    public static function mark(string $content): Mark
    {
        return new Mark($content);
    }

    public static function del(string $content): Del
    {
        return new Del($content);
    }

    public static function ins(string $content): Ins
    {
        return new Ins($content);
    }

    public static function abbr(string $content): Abbr
    {
        return new Abbr($content);
    }

    public static function kbd(string $content): Kbd
    {
        return new Kbd($content);
    }

    public static function samp(string $content): Samp
    {
        return new Samp($content);
    }

    public static function var(string $content): VarElement
    {
        return new VarElement($content);
    }

    public static function small(string $content): Small
    {
        return new Small($content);
    }

    public static function sub(string $content): Sub
    {
        return new Sub($content);
    }

    public static function sup(string $content): Sup
    {
        return new Sup($content);
    }

    public static function strong(string $content): Strong
    {
        return new Strong($content);
    }

    public static function em(string $content): Em
    {
        return new Em($content);
    }

    public static function b(string $content): B
    {
        return new B($content);
    }

    public static function i(string $content): I
    {
        return new I($content);
    }

    public static function u(string $content): U
    {
        return new U($content);
    }

    public static function s(string $content): S
    {
        return new S($content);
    }

    // ============================================================
    // Misc Elements
    // ============================================================

    public static function br(): Br
    {
        return new Br();
    }

    public static function wbr(): Wbr
    {
        return new Wbr();
    }

    public static function hr(): Hr
    {
        return new Hr();
    }

    public static function span(string|UIElement|null $content = null): Span
    {
        return new Span($content);
    }

    public static function div(): Div
    {
        return new Div();
    }

    public static function template(): Template
    {
        return new Template();
    }

    public static function slot(): Slot
    {
        return new Slot();
    }

    public static function data(string $content, string $value): Data
    {
        return new Data($content, $value);
    }

    public static function bdi(string $content): Bdi
    {
        return new Bdi($content);
    }

    public static function bdo(string $content): Bdo
    {
        return new Bdo($content);
    }

    public static function ruby(string $base, string $annotation): Ruby
    {
        return new Ruby($base, $annotation);
    }

    public static function noscript(): Noscript
    {
        return new Noscript();
    }

    public static function embed(string $src): Embed
    {
        return new Embed($src);
    }

    public static function object(): ObjectElement
    {
        return new ObjectElement();
    }

    // ============================================================
    // Utility Components
    // ============================================================

    public static function spacer(): Container
    {
        $spacer = new Container();
        $spacer->extend();
        return $spacer;
    }

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

    public static function card(): Container
    {
        $card = new Container();
        $card->background(Color::white());
        $card->rounded(Unit::roundedLg());
        $card->shadow(Shadow::Medium);
        $card->padding(Unit::medium());
        return $card;
    }

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

    public static function avatar(string $src, string $alt = ''): Image
    {
        return (new Image($src, $alt))
            ->roundedFull()
            ->cover();
    }

    public static function center(): Row
    {
        return (new Row())->center();
    }

    // ============================================================
    // Routing
    // ============================================================

    public static function router(): Router
    {
        return new Router();
    }

    // ============================================================
    // Style Output
    // ============================================================

    public static function printStyles(array $styles): string
    {
        return StyleGenerator::from($styles)->toCSS();
    }
}
