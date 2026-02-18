<?php

namespace Spwa\UI;

/**
 * Text element with font styling support.
 *
 * Usage:
 *   UI::text("Hello World")
 *       ->size(FontSize::xl())
 *       ->weight(FontWeight::bold())
 *       ->color(Color::blue(600), Color::blue(400)->dark())
 */
class TextElement extends BaseStyledElement
{
    public function __construct(
        protected string $text,
        protected string $tag = 'span'
    ) {
    }

    /**
     * Set font size.
     */
    public function size(FontSize ...$sizes): static
    {
        foreach ($sizes as $size) {
            $this->addStateValue($size);
        }
        return $this;
    }

    /**
     * Set font weight.
     */
    public function weight(FontWeight ...$weights): static
    {
        foreach ($weights as $weight) {
            $this->addStateValue($weight);
        }
        return $this;
    }

    /**
     * Set font family.
     */
    public function family(FontFamily ...$families): static
    {
        foreach ($families as $family) {
            $this->addStateValue($family);
        }
        return $this;
    }

    /**
     * Set text alignment.
     */
    public function align(TextAlign ...$alignments): static
    {
        foreach ($alignments as $alignment) {
            $this->addStateValue($alignment);
        }
        return $this;
    }

    // Shorthand methods for common sizes
    public function textXs(): static
    {
        $this->addClass('text-xs');
        return $this;
    }

    public function textSm(): static
    {
        $this->addClass('text-sm');
        return $this;
    }

    public function textBase(): static
    {
        $this->addClass('text-base');
        return $this;
    }

    public function textLg(): static
    {
        $this->addClass('text-lg');
        return $this;
    }

    public function textXl(): static
    {
        $this->addClass('text-xl');
        return $this;
    }

    public function text2xl(): static
    {
        $this->addClass('text-2xl');
        return $this;
    }

    public function text3xl(): static
    {
        $this->addClass('text-3xl');
        return $this;
    }

    public function text4xl(): static
    {
        $this->addClass('text-4xl');
        return $this;
    }

    // Shorthand methods for common weights
    public function fontThin(): static
    {
        $this->addClass('font-thin');
        return $this;
    }

    public function fontLight(): static
    {
        $this->addClass('font-light');
        return $this;
    }

    public function fontNormal(): static
    {
        $this->addClass('font-normal');
        return $this;
    }

    public function fontMedium(): static
    {
        $this->addClass('font-medium');
        return $this;
    }

    public function fontSemibold(): static
    {
        $this->addClass('font-semibold');
        return $this;
    }

    public function fontBold(): static
    {
        $this->addClass('font-bold');
        return $this;
    }

    public function fontBlack(): static
    {
        $this->addClass('font-black');
        return $this;
    }

    // Text decoration
    public function underline(): static
    {
        $this->addClass('underline');
        return $this;
    }

    public function lineThrough(): static
    {
        $this->addClass('line-through');
        return $this;
    }

    public function noUnderline(): static
    {
        $this->addClass('no-underline');
        return $this;
    }

    // Text transform
    public function uppercase(): static
    {
        $this->addClass('uppercase');
        return $this;
    }

    public function lowercase(): static
    {
        $this->addClass('lowercase');
        return $this;
    }

    public function capitalize(): static
    {
        $this->addClass('capitalize');
        return $this;
    }

    public function normalCase(): static
    {
        $this->addClass('normal-case');
        return $this;
    }

    // Font style
    public function italic(): static
    {
        $this->addClass('italic');
        return $this;
    }

    public function notItalic(): static
    {
        $this->addClass('not-italic');
        return $this;
    }

    // Line height
    public function leading(string $value): static
    {
        $this->addClass('leading-' . $value);
        return $this;
    }

    public function leadingNone(): static
    {
        return $this->leading('none');
    }

    public function leadingTight(): static
    {
        return $this->leading('tight');
    }

    public function leadingNormal(): static
    {
        return $this->leading('normal');
    }

    public function leadingRelaxed(): static
    {
        return $this->leading('relaxed');
    }

    public function leadingLoose(): static
    {
        return $this->leading('loose');
    }

    // Letter spacing
    public function tracking(string $value): static
    {
        $this->addClass('tracking-' . $value);
        return $this;
    }

    public function trackingTight(): static
    {
        return $this->tracking('tight');
    }

    public function trackingNormal(): static
    {
        return $this->tracking('normal');
    }

    public function trackingWide(): static
    {
        return $this->tracking('wide');
    }

    // Text alignment
    public function textLeft(): static
    {
        $this->addClass('text-left');
        return $this;
    }

    public function textCenter(): static
    {
        $this->addClass('text-center');
        return $this;
    }

    public function textRight(): static
    {
        $this->addClass('text-right');
        return $this;
    }

    public function textJustify(): static
    {
        $this->addClass('text-justify');
        return $this;
    }

    // Truncation
    public function truncate(): static
    {
        $this->addClass('truncate');
        return $this;
    }

    public function textEllipsis(): static
    {
        $this->addClass('text-ellipsis');
        return $this;
    }

    // Whitespace
    public function whitespaceNormal(): static
    {
        $this->addClass('whitespace-normal');
        return $this;
    }

    public function whitespaceNowrap(): static
    {
        $this->addClass('whitespace-nowrap');
        return $this;
    }

    public function whitespacePre(): static
    {
        $this->addClass('whitespace-pre');
        return $this;
    }

    public function whitespacePreWrap(): static
    {
        $this->addClass('whitespace-pre-wrap');
        return $this;
    }

    public function whitespacePreLine(): static
    {
        $this->addClass('whitespace-pre-line');
        return $this;
    }

    public function whitespaceBreakSpaces(): static
    {
        $this->addClass('whitespace-break-spaces');
        return $this;
    }

    // ============================================================
    // QuestPDF Font Weights (complete set)
    // ============================================================

    public function thin(): static
    {
        $this->addClass('font-thin');
        return $this;
    }

    public function extraLight(): static
    {
        $this->addClass('font-extralight');
        return $this;
    }

    public function light(): static
    {
        $this->addClass('font-light');
        return $this;
    }

    public function normalWeight(): static
    {
        $this->addClass('font-normal');
        return $this;
    }

    public function medium(): static
    {
        $this->addClass('font-medium');
        return $this;
    }

    public function semiBold(): static
    {
        $this->addClass('font-semibold');
        return $this;
    }

    public function bold(): static
    {
        $this->addClass('font-bold');
        return $this;
    }

    public function extraBold(): static
    {
        $this->addClass('font-extrabold');
        return $this;
    }

    public function black(): static
    {
        $this->addClass('font-black');
        return $this;
    }

    // ============================================================
    // Text Decorations (QuestPDF style)
    // ============================================================

    public function strikethrough(): static
    {
        $this->addClass('line-through');
        return $this;
    }

    public function overline(): static
    {
        $this->addClass('overline');
        return $this;
    }

    public function decorationSolid(): static
    {
        $this->addClass('decoration-solid');
        return $this;
    }

    public function decorationDouble(): static
    {
        $this->addClass('decoration-double');
        return $this;
    }

    public function decorationDotted(): static
    {
        $this->addClass('decoration-dotted');
        return $this;
    }

    public function decorationDashed(): static
    {
        $this->addClass('decoration-dashed');
        return $this;
    }

    public function decorationWavy(): static
    {
        $this->addClass('decoration-wavy');
        return $this;
    }

    public function decorationColor(Color $color): static
    {
        $this->addStateValue($color->asDecoration());
        return $this;
    }

    public function decorationThickness(int $value): static
    {
        $this->addClass('decoration-' . $value);
        return $this;
    }

    public function underlineOffset(int $value): static
    {
        $this->addClass('underline-offset-' . $value);
        return $this;
    }

    // ============================================================
    // Superscript/Subscript (QuestPDF)
    // ============================================================

    public function subscript(): static
    {
        $this->addClass('align-sub');
        $this->addClass('text-xs');
        return $this;
    }

    public function superscript(): static
    {
        $this->addClass('align-super');
        $this->addClass('text-xs');
        return $this;
    }

    // ============================================================
    // Line height (QuestPDF style numeric)
    // ============================================================

    public function lineHeight(float $value): static
    {
        // Tailwind supports leading-[value] for arbitrary values
        $this->addClass('leading-[' . $value . ']');
        return $this;
    }

    // ============================================================
    // Letter spacing (QuestPDF style)
    // ============================================================

    public function letterSpacing(string $value): static
    {
        $this->addClass('tracking-[' . $value . ']');
        return $this;
    }

    public function trackingTighter(): static
    {
        $this->addClass('tracking-tighter');
        return $this;
    }

    public function trackingWidest(): static
    {
        $this->addClass('tracking-widest');
        return $this;
    }

    // ============================================================
    // Word spacing
    // ============================================================

    public function wordSpacing(string $value): static
    {
        // Arbitrary value for word spacing
        $this->addClass('[word-spacing:' . $value . ']');
        return $this;
    }

    // ============================================================
    // Additional text sizes
    // ============================================================

    public function text5xl(): static
    {
        $this->addClass('text-5xl');
        return $this;
    }

    public function text6xl(): static
    {
        $this->addClass('text-6xl');
        return $this;
    }

    public function text7xl(): static
    {
        $this->addClass('text-7xl');
        return $this;
    }

    public function text8xl(): static
    {
        $this->addClass('text-8xl');
        return $this;
    }

    public function text9xl(): static
    {
        $this->addClass('text-9xl');
        return $this;
    }

    // ============================================================
    // Word break
    // ============================================================

    public function breakNormal(): static
    {
        $this->addClass('break-normal');
        return $this;
    }

    public function breakWords(): static
    {
        $this->addClass('break-words');
        return $this;
    }

    public function breakAll(): static
    {
        $this->addClass('break-all');
        return $this;
    }

    public function breakKeep(): static
    {
        $this->addClass('break-keep');
        return $this;
    }

    // ============================================================
    // Hyphens
    // ============================================================

    public function hyphensNone(): static
    {
        $this->addClass('hyphens-none');
        return $this;
    }

    public function hyphensManual(): static
    {
        $this->addClass('hyphens-manual');
        return $this;
    }

    public function hyphensAuto(): static
    {
        $this->addClass('hyphens-auto');
        return $this;
    }

    // ============================================================
    // Text overflow
    // ============================================================

    public function textClip(): static
    {
        $this->addClass('text-clip');
        return $this;
    }

    // ============================================================
    // Line clamp
    // ============================================================

    public function lineClamp(int $lines): static
    {
        $this->addClass('line-clamp-' . $lines);
        return $this;
    }

    public function lineClampNone(): static
    {
        $this->addClass('line-clamp-none');
        return $this;
    }

    // ============================================================
    // Text indent
    // ============================================================

    public function indent(string $value): static
    {
        $this->addClass('indent-' . $value);
        return $this;
    }

    // ============================================================
    // Vertical align
    // ============================================================

    public function verticalAlignBaseline(): static
    {
        $this->addClass('align-baseline');
        return $this;
    }

    public function verticalAlignTop(): static
    {
        $this->addClass('align-top');
        return $this;
    }

    public function verticalAlignMiddle(): static
    {
        $this->addClass('align-middle');
        return $this;
    }

    public function verticalAlignBottom(): static
    {
        $this->addClass('align-bottom');
        return $this;
    }

    public function verticalAlignTextTop(): static
    {
        $this->addClass('align-text-top');
        return $this;
    }

    public function verticalAlignTextBottom(): static
    {
        $this->addClass('align-text-bottom');
        return $this;
    }

    // ============================================================
    // Font family shortcuts
    // ============================================================

    public function fontSans(): static
    {
        $this->addClass('font-sans');
        return $this;
    }

    public function fontSerif(): static
    {
        $this->addClass('font-serif');
        return $this;
    }

    public function fontMono(): static
    {
        $this->addClass('font-mono');
        return $this;
    }

    // ============================================================
    // Font numeric features
    // ============================================================

    public function numericNormal(): static
    {
        $this->addClass('normal-nums');
        return $this;
    }

    public function numericOrdinal(): static
    {
        $this->addClass('ordinal');
        return $this;
    }

    public function numericSlashedZero(): static
    {
        $this->addClass('slashed-zero');
        return $this;
    }

    public function numericLining(): static
    {
        $this->addClass('lining-nums');
        return $this;
    }

    public function numericOldstyle(): static
    {
        $this->addClass('oldstyle-nums');
        return $this;
    }

    public function numericProportional(): static
    {
        $this->addClass('proportional-nums');
        return $this;
    }

    public function numericTabular(): static
    {
        $this->addClass('tabular-nums');
        return $this;
    }

    public function numericDiagonalFractions(): static
    {
        $this->addClass('diagonal-fractions');
        return $this;
    }

    public function numericStackedFractions(): static
    {
        $this->addClass('stacked-fractions');
        return $this;
    }

    // ============================================================
    // Text wrap
    // ============================================================

    public function textWrap(): static
    {
        $this->addClass('text-wrap');
        return $this;
    }

    public function textNowrap(): static
    {
        $this->addClass('text-nowrap');
        return $this;
    }

    public function textBalance(): static
    {
        $this->addClass('text-balance');
        return $this;
    }

    public function textPretty(): static
    {
        $this->addClass('text-pretty');
        return $this;
    }

    public function render(): void
    {
        $classAttr = $this->buildClassAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';

        echo "<{$this->tag}{$classHtml}>";
        echo htmlspecialchars($this->text, ENT_QUOTES, 'UTF-8');
        echo "</{$this->tag}>";
    }
}
