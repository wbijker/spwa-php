<?php

namespace Spwa\UI;

/**
 * Text element with typography styling.
 *
 * Usage:
 *   UI::text("Hello World")
 *       ->size(FontSize::Large)
 *       ->weight(FontWeight::Bold)
 *       ->color(Color::blue(600))
 */
class Text extends UIElementContent
{
    private string $tag = '';

    public function __construct(string $content)
    {
        parent::__construct('span');
        $this->content($content);
    }

    // ============================================================
    // Font size
    // ============================================================

    /**
     * Set font size.
     */
    public function fontSize(FontSize $size): static
    {
        $this->addStyle($size->toClass(), ['font-size' => $size->getCssValue()]);
        return $this;
    }

    // ============================================================
    // Font weight
    // ============================================================

    /**
     * Set font weight.
     */
    public function weight(FontWeight $weight): static
    {
        $this->addStyle($weight->toClass(), ['font-weight' => $weight->getCssValue()]);
        return $this;
    }

    /**
     * Bold text.
     */
    public function bold(): static
    {
        return $this->weight(FontWeight::Bold);
    }

    /**
     * Semi-bold text.
     */
    public function semibold(): static
    {
        return $this->weight(FontWeight::SemiBold);
    }

    /**
     * Medium weight text.
     */
    public function medium(): static
    {
        return $this->weight(FontWeight::Medium);
    }

    /**
     * Light text.
     */
    public function light(): static
    {
        return $this->weight(FontWeight::Light);
    }

    // ============================================================
    // Text decoration
    // ============================================================

    /**
     * Underline text.
     */
    public function underline(): static
    {
        $this->addStyle('underline', ['text-decoration' => 'underline']);
        return $this;
    }

    /**
     * Strikethrough text.
     */
    public function strikethrough(): static
    {
        $this->addStyle('line-through', ['text-decoration' => 'line-through']);
        return $this;
    }

    /**
     * Italic text.
     */
    public function italic(): static
    {
        $this->addStyle('italic', ['font-style' => 'italic']);
        return $this;
    }

    // ============================================================
    // Text transform
    // ============================================================

    /**
     * Uppercase text.
     */
    public function uppercase(): static
    {
        $this->addStyle('uppercase', ['text-transform' => 'uppercase']);
        return $this;
    }

    /**
     * Lowercase text.
     */
    public function lowercase(): static
    {
        $this->addStyle('lowercase', ['text-transform' => 'lowercase']);
        return $this;
    }

    /**
     * Capitalize text.
     */
    public function capitalize(): static
    {
        $this->addStyle('capitalize', ['text-transform' => 'capitalize']);
        return $this;
    }

    // ============================================================
    // Text alignment
    // ============================================================

    /**
     * Align text left.
     */
    public function left(): static
    {
        $this->addStyle('text-left', ['text-align' => 'left']);
        return $this;
    }

    /**
     * Center text.
     */
    public function center(): static
    {
        $this->addStyle('text-center', ['text-align' => 'center']);
        return $this;
    }

    /**
     * Align text right.
     */
    public function right(): static
    {
        $this->addStyle('text-right', ['text-align' => 'right']);
        return $this;
    }

    /**
     * Justify text.
     */
    public function justify(): static
    {
        $this->addStyle('text-justify', ['text-align' => 'justify']);
        return $this;
    }

    // ============================================================
    // Truncation
    // ============================================================

    /**
     * Truncate with ellipsis.
     */
    public function truncate(): static
    {
        $this->addStyle('truncate', ['overflow' => 'hidden', 'text-overflow' => 'ellipsis', 'white-space' => 'nowrap']);
        return $this;
    }

    /**
     * Limit to number of lines.
     */
    public function lines(int $count): static
    {
        $this->addStyle('line-clamp-' . $count, [
            'display' => '-webkit-box',
            '-webkit-line-clamp' => (string)$count,
            '-webkit-box-orient' => 'vertical',
            'overflow' => 'hidden',
        ]);
        return $this;
    }

    // ============================================================
    // Whitespace
    // ============================================================

    /**
     * Preserve whitespace.
     */
    public function preserveWhitespace(): static
    {
        $this->addStyle('whitespace-pre', ['white-space' => 'pre']);
        return $this;
    }

    /**
     * Prevent wrapping.
     */
    public function nowrap(): static
    {
        $this->addStyle('whitespace-nowrap', ['white-space' => 'nowrap']);
        return $this;
    }

    // ============================================================
    // Font family
    // ============================================================

    /**
     * Sans-serif font.
     */
    public function sans(): static
    {
        $this->addStyle('font-sans', ['font-family' => 'ui-sans-serif, system-ui, sans-serif']);
        return $this;
    }

    /**
     * Serif font.
     */
    public function serif(): static
    {
        $this->addStyle('font-serif', ['font-family' => 'ui-serif, Georgia, serif']);
        return $this;
    }

    /**
     * Monospace font.
     */
    public function mono(): static
    {
        $this->addStyle('font-mono', ['font-family' => 'ui-monospace, monospace']);
        return $this;
    }
}
