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
class Text extends UIElement
{
    protected string $tag = 'span';

    public function __construct(
        protected string $content
    ) {
    }

    // ============================================================
    // Semantic tags
    // ============================================================

    /**
     * Render as paragraph.
     */
    public function paragraph(): static
    {
        $this->tag = 'p';
        return $this;
    }

    /**
     * Render as heading.
     */
    public function heading(int $level = 1): static
    {
        $this->tag = 'h' . min(max($level, 1), 6);
        return $this;
    }

    /**
     * Render as label.
     */
    public function label(): static
    {
        $this->tag = 'label';
        return $this;
    }

    // ============================================================
    // Font size
    // ============================================================

    /**
     * Set font size.
     */
//    public function size(FontSize $size): static
//    {
//        $this->classes[] = $size->toClass();
//        return $this;
//    }

    // ============================================================
    // Font weight
    // ============================================================

    /**
     * Set font weight.
     */
    public function weight(FontWeight $weight): static
    {
        $this->classes[] = $weight->toClass();
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
        $this->classes[] = 'underline';
        return $this;
    }

    /**
     * Strikethrough text.
     */
    public function strikethrough(): static
    {
        $this->classes[] = 'line-through';
        return $this;
    }

    /**
     * Italic text.
     */
    public function italic(): static
    {
        $this->classes[] = 'italic';
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
        $this->classes[] = 'uppercase';
        return $this;
    }

    /**
     * Lowercase text.
     */
    public function lowercase(): static
    {
        $this->classes[] = 'lowercase';
        return $this;
    }

    /**
     * Capitalize text.
     */
    public function capitalize(): static
    {
        $this->classes[] = 'capitalize';
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
        $this->classes[] = 'text-left';
        return $this;
    }

    /**
     * Center text.
     */
    public function center(): static
    {
        $this->classes[] = 'text-center';
        return $this;
    }

    /**
     * Align text right.
     */
    public function right(): static
    {
        $this->classes[] = 'text-right';
        return $this;
    }

    /**
     * Justify text.
     */
    public function justify(): static
    {
        $this->classes[] = 'text-justify';
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
        $this->classes[] = 'truncate';
        return $this;
    }

    /**
     * Limit to number of lines.
     */
    public function lines(int $count): static
    {
        $this->classes[] = 'line-clamp-' . $count;
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
        $this->classes[] = 'whitespace-pre';
        return $this;
    }

    /**
     * Prevent wrapping.
     */
    public function nowrap(): static
    {
        $this->classes[] = 'whitespace-nowrap';
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
        $this->classes[] = 'font-sans';
        return $this;
    }

    /**
     * Serif font.
     */
    public function serif(): static
    {
        $this->classes[] = 'font-serif';
        return $this;
    }

    /**
     * Monospace font.
     */
    public function mono(): static
    {
        $this->classes[] = 'font-mono';
        return $this;
    }

    public function render(): string
    {
        $classAttr = $this->classAttribute();
        $classHtml = $classAttr ? " class=\"{$classAttr}\"" : '';

        return "<{$this->tag}{$classHtml}>" . htmlspecialchars($this->content) . "</{$this->tag}>";
    }
}
