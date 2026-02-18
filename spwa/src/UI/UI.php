<?php

namespace Spwa\UI;

/**
 * Static factory class for creating UI elements.
 *
 * Usage:
 *   UI::element()           // Basic div element
 *   UI::rows()              // Flex column (vertical stack)
 *   UI::columns()           // Flex row (horizontal stack)
 *   UI::stack()             // Absolute positioned stack
 *   UI::text("Hello")       // Text element
 *   UI::image("src.jpg")    // Image element
 *   UI::table()             // Table element
 */
class UI
{
    /**
     * Create a basic block element (div).
     */
    public static function element(string $tag = 'div'): Element
    {
        return new Element($tag);
    }

    /**
     * Create a div element.
     */
    public static function div(): Element
    {
        return new Element('div');
    }

    /**
     * Create a section element.
     */
    public static function section(): Element
    {
        return new Element('section');
    }

    /**
     * Create an article element.
     */
    public static function article(): Element
    {
        return new Element('article');
    }

    /**
     * Create a header element.
     */
    public static function header(): Element
    {
        return new Element('header');
    }

    /**
     * Create a footer element.
     */
    public static function footer(): Element
    {
        return new Element('footer');
    }

    /**
     * Create a nav element.
     */
    public static function nav(): Element
    {
        return new Element('nav');
    }

    /**
     * Create a main element.
     */
    public static function main(): Element
    {
        return new Element('main');
    }

    /**
     * Create an aside element.
     */
    public static function aside(): Element
    {
        return new Element('aside');
    }

    /**
     * Create a flex container with specified direction.
     */
    public static function flex(?DirectionValue $direction = null): FlexElement
    {
        return new FlexElement($direction);
    }

    /**
     * Create a vertical flex container (column direction).
     * Items are stacked vertically.
     */
    public static function rows(?DirectionValue $direction = null): FlexElement
    {
        $element = new FlexElement();
        $element->column();
        if ($direction !== null) {
            $element->direction($direction);
        }
        return $element;
    }

    /**
     * Create a vertical flex container (alias for rows).
     */
    public static function column(?DirectionValue $direction = null): FlexElement
    {
        return self::rows($direction);
    }

    /**
     * Create a vertical flex container (alias for rows).
     */
    public static function col(?DirectionValue $direction = null): FlexElement
    {
        return self::rows($direction);
    }

    /**
     * Create a horizontal flex container (row direction).
     * Items are stacked horizontally.
     */
    public static function columns(?DirectionValue $direction = null): FlexElement
    {
        $element = new FlexElement();
        $element->row();
        if ($direction !== null) {
            $element->direction($direction);
        }
        return $element;
    }

    /**
     * Create a horizontal flex container (alias for columns).
     */
    public static function row(?DirectionValue $direction = null): FlexElement
    {
        return self::columns($direction);
    }

    /**
     * Create an absolutely positioned stack container.
     * Children can be positioned relative to this container.
     */
    public static function stack(): Element
    {
        return (new Element('div'))->relative();
    }

    /**
     * Create a centered container (flex with center alignment).
     */
    public static function center(): FlexElement
    {
        return (new FlexElement())->center();
    }

    /**
     * Create a text element.
     */
    public static function text(string $text): TextElement
    {
        return new TextElement($text);
    }

    /**
     * Create a paragraph element.
     */
    public static function p(string $text): TextElement
    {
        return new TextElement($text, 'p');
    }

    /**
     * Create a heading element.
     */
    public static function heading(string $text, int $level = 1): TextElement
    {
        return new TextElement($text, 'h' . min(max($level, 1), 6));
    }

    /**
     * Create h1 element.
     */
    public static function h1(string $text): TextElement
    {
        return new TextElement($text, 'h1');
    }

    /**
     * Create h2 element.
     */
    public static function h2(string $text): TextElement
    {
        return new TextElement($text, 'h2');
    }

    /**
     * Create h3 element.
     */
    public static function h3(string $text): TextElement
    {
        return new TextElement($text, 'h3');
    }

    /**
     * Create h4 element.
     */
    public static function h4(string $text): TextElement
    {
        return new TextElement($text, 'h4');
    }

    /**
     * Create h5 element.
     */
    public static function h5(string $text): TextElement
    {
        return new TextElement($text, 'h5');
    }

    /**
     * Create h6 element.
     */
    public static function h6(string $text): TextElement
    {
        return new TextElement($text, 'h6');
    }

    /**
     * Create an image element.
     */
    public static function image(string $src, ?string $alt = null): ImageElement
    {
        return new ImageElement($src, $alt);
    }

    /**
     * Create an image element (alias).
     */
    public static function img(string $src, ?string $alt = null): ImageElement
    {
        return self::image($src, $alt);
    }

    /**
     * Create a button element.
     */
    public static function button(string $text): ButtonElement
    {
        return new ButtonElement($text);
    }

    /**
     * Create a link element.
     */
    public static function link(string $text, string $href = '#'): LinkElement
    {
        return new LinkElement($text, $href);
    }

    /**
     * Create a link element (alias).
     */
    public static function a(string $text, string $href = '#'): LinkElement
    {
        return self::link($text, $href);
    }

    /**
     * Create a table element.
     */
    public static function table(): TableElement
    {
        return new TableElement();
    }

    /**
     * Create a grid container.
     */
    public static function grid(int $cols = 1): GridElement
    {
        return new GridElement($cols);
    }

    /**
     * Create a spacer element for adding space in flex layouts.
     */
    public static function spacer(): Element
    {
        return (new Element('div'))->grow();
    }

    /**
     * Create a divider/separator element.
     */
    public static function divider(): Element
    {
        return (new Element('hr'));
    }

    // ============================================================
    // QuestPDF Layout Elements
    // ============================================================

    /**
     * Create a layers element for stacking content.
     * (QuestPDF: Layers)
     */
    public static function layers(): LayersElement
    {
        return new LayersElement();
    }

    /**
     * Create a decoration element with before/content/after sections.
     * (QuestPDF: Decoration)
     */
    public static function decoration(): DecorationElement
    {
        return new DecorationElement();
    }

    /**
     * Create an inlined element (flow layout with wrapping).
     * (QuestPDF: Inlined)
     */
    public static function inlined(): InlinedElement
    {
        return new InlinedElement();
    }

    /**
     * Create an aspect ratio container.
     * (QuestPDF: AspectRatio)
     */
    public static function aspectRatio(?float $ratio = null): AspectRatioElement
    {
        return new AspectRatioElement($ratio);
    }

    /**
     * Create a horizontal line.
     * (QuestPDF: LineHorizontal)
     */
    public static function lineHorizontal(): LineElement
    {
        return new LineElement(false);
    }

    /**
     * Create a vertical line.
     * (QuestPDF: LineVertical)
     */
    public static function lineVertical(): LineElement
    {
        return new LineElement(true);
    }

    /**
     * Create a horizontal rule (alias for lineHorizontal).
     */
    public static function hr(): LineElement
    {
        return self::lineHorizontal();
    }

    // ============================================================
    // QuestPDF Container Elements
    // ============================================================

    /**
     * Create a container that constrains its content.
     * (QuestPDF: Constrained)
     */
    public static function constrained(): Element
    {
        return (new Element('div'))->maxWidth(Unit::screen());
    }

    /**
     * Create a container with max-width for content.
     */
    public static function container(): Element
    {
        return (new Element('div'))->container()->centerX();
    }

    /**
     * Create a box element with common padding.
     */
    public static function box(): Element
    {
        return new Element('div');
    }

    // ============================================================
    // Semantic HTML Elements
    // ============================================================

    /**
     * Create a span element.
     */
    public static function span(string $text = ''): TextElement
    {
        return new TextElement($text, 'span');
    }

    /**
     * Create a strong element.
     */
    public static function strong(string $text): TextElement
    {
        return (new TextElement($text, 'strong'))->bold();
    }

    /**
     * Create an em (emphasis) element.
     */
    public static function em(string $text): TextElement
    {
        return (new TextElement($text, 'em'))->italic();
    }

    /**
     * Create a small element.
     */
    public static function small(string $text): TextElement
    {
        return (new TextElement($text, 'small'))->textSm();
    }

    /**
     * Create a mark (highlight) element.
     */
    public static function mark(string $text): TextElement
    {
        return (new TextElement($text, 'mark'))->background(Color::yellow(200));
    }

    /**
     * Create a del (deleted text) element.
     */
    public static function del(string $text): TextElement
    {
        return (new TextElement($text, 'del'))->strikethrough();
    }

    /**
     * Create an ins (inserted text) element.
     */
    public static function ins(string $text): TextElement
    {
        return (new TextElement($text, 'ins'))->underline();
    }

    /**
     * Create a sub (subscript) element.
     */
    public static function sub(string $text): TextElement
    {
        return (new TextElement($text, 'sub'))->subscript();
    }

    /**
     * Create a sup (superscript) element.
     */
    public static function sup(string $text): TextElement
    {
        return (new TextElement($text, 'sup'))->superscript();
    }

    /**
     * Create a code element.
     */
    public static function code(string $text): TextElement
    {
        return (new TextElement($text, 'code'))
            ->fontMono()
            ->background(Color::gray(100))
            ->padding(Unit::scale1())
            ->rounded(Unit::roundedSm());
    }

    /**
     * Create a pre element.
     */
    public static function pre(string $text): TextElement
    {
        return (new TextElement($text, 'pre'))
            ->fontMono()
            ->whitespacePre();
    }

    /**
     * Create a kbd (keyboard input) element.
     */
    public static function kbd(string $text): TextElement
    {
        return (new TextElement($text, 'kbd'))
            ->fontMono()
            ->textSm()
            ->background(Color::gray(100))
            ->border()
            ->borderColor(Color::gray(300))
            ->padding(Unit::scale1())
            ->rounded(Unit::roundedSm());
    }

    /**
     * Create a blockquote element.
     */
    public static function blockquote(string $text): Element
    {
        return (new Element('blockquote'))
            ->borderLeft(4)
            ->borderColor(Color::gray(300))
            ->paddingLeft(Unit::sizeMd())
            ->italic()
            ->children(UI::text($text));
    }

    /**
     * Create a cite element.
     */
    public static function cite(string $text): TextElement
    {
        return (new TextElement($text, 'cite'))->italic();
    }

    /**
     * Create an abbr (abbreviation) element.
     */
    public static function abbr(string $text, string $title): Element
    {
        $el = new Element('abbr');
        // Note: Title attribute would need to be added in a real implementation
        return $el->children(UI::text($text)->underline()->decorationDotted());
    }

    /**
     * Create a time element.
     */
    public static function time(string $text): TextElement
    {
        return new TextElement($text, 'time');
    }

    // ============================================================
    // Form Elements
    // ============================================================

    /**
     * Create a form element.
     */
    public static function form(): Element
    {
        return new Element('form');
    }

    /**
     * Create a label element.
     */
    public static function label(string $text): TextElement
    {
        return new TextElement($text, 'label');
    }

    /**
     * Create a fieldset element.
     */
    public static function fieldset(): Element
    {
        return (new Element('fieldset'))->border()->padding(Unit::sizeMd())->rounded(Unit::rounded());
    }

    /**
     * Create a legend element.
     */
    public static function legend(string $text): TextElement
    {
        return (new TextElement($text, 'legend'))->fontMedium();
    }

    // ============================================================
    // List Elements
    // ============================================================

    /**
     * Create an unordered list.
     */
    public static function ul(): Element
    {
        return (new Element('ul'))->listDisc()->listInside();
    }

    /**
     * Create an ordered list.
     */
    public static function ol(): Element
    {
        return (new Element('ol'))->listDecimal()->listInside();
    }

    /**
     * Create a list item.
     */
    public static function li(string $text = ''): Element
    {
        $el = new Element('li');
        if ($text !== '') {
            $el->children(UI::text($text));
        }
        return $el;
    }

    /**
     * Create a description list.
     */
    public static function dl(): Element
    {
        return new Element('dl');
    }

    /**
     * Create a description term.
     */
    public static function dt(string $text): TextElement
    {
        return (new TextElement($text, 'dt'))->fontMedium();
    }

    /**
     * Create a description details.
     */
    public static function dd(string $text): TextElement
    {
        return (new TextElement($text, 'dd'))->marginLeft(Unit::sizeMd());
    }

    // ============================================================
    // Media Elements
    // ============================================================

    /**
     * Create a figure element.
     */
    public static function figure(): Element
    {
        return new Element('figure');
    }

    /**
     * Create a figcaption element.
     */
    public static function figcaption(string $text): TextElement
    {
        return (new TextElement($text, 'figcaption'))->textSm()->color(Color::gray(600));
    }

    /**
     * Create a video placeholder element.
     */
    public static function video(): AspectRatioElement
    {
        return (new AspectRatioElement())->video()->background(Color::black());
    }

    // ============================================================
    // Card and Panel Components
    // ============================================================

    /**
     * Create a card element with common styling.
     */
    public static function card(): Element
    {
        return (new Element('div'))
            ->background(Color::white())
            ->rounded(Unit::roundedLg())
            ->shadow('md')
            ->padding(Unit::sizeMd());
    }

    /**
     * Create a panel element.
     */
    public static function panel(): Element
    {
        return (new Element('div'))
            ->border()
            ->borderColor(Color::gray(200))
            ->rounded(Unit::rounded())
            ->padding(Unit::sizeMd());
    }

    /**
     * Create an alert/callout element.
     */
    public static function alert(): Element
    {
        return (new Element('div'))
            ->padding(Unit::sizeMd())
            ->rounded(Unit::rounded())
            ->border()
            ->borderColor(Color::blue(200))
            ->background(Color::blue(50));
    }

    /**
     * Create a badge element.
     */
    public static function badge(string $text): TextElement
    {
        return (new TextElement($text, 'span'))
            ->textXs()
            ->fontMedium()
            ->padding(Unit::scale1())
            ->paddingX(Unit::sizeSm())
            ->rounded(Unit::roundedFull())
            ->background(Color::gray(100))
            ->color(Color::gray(800));
    }

    /**
     * Create a pill element.
     */
    public static function pill(string $text): TextElement
    {
        return (new TextElement($text, 'span'))
            ->textSm()
            ->padding(Unit::sizeSm())
            ->paddingX(Unit::sizeMd())
            ->rounded(Unit::roundedFull())
            ->background(Color::blue(500))
            ->color(Color::white());
    }

    // ============================================================
    // Debug Elements
    // ============================================================

    /**
     * Create a placeholder element for debugging layouts.
     * (QuestPDF: Placeholder)
     */
    public static function placeholder(string $label = 'Placeholder'): Element
    {
        return (new Element('div'))
            ->background(Color::gray(200))
            ->border()
            ->borderColor(Color::gray(400))
            ->borderDashed()
            ->padding(Unit::sizeMd())
            ->children(
                UI::text($label)->color(Color::gray(600))->textCenter()
            );
    }

    /**
     * Create a debug border wrapper.
     */
    public static function debugBorder(): Element
    {
        return (new Element('div'))
            ->border()
            ->borderColor(Color::red(500));
    }

    /**
     * Create a debug background wrapper.
     */
    public static function debugBackground(): Element
    {
        return (new Element('div'))
            ->background(Color::red(100)->opacity(50));
    }
}
