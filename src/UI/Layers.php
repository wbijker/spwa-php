<?php

namespace BrickPHP\UI;

/**
 * Stacked layers — content positioned on top of each other, sized by
 * a single primary layer. Modeled after QuestPDF's Layers API:
 * https://www.questpdf.com/api-reference/layers.html
 *
 * Exactly one layer is the `primary()` — it sits in normal flow and
 * dictates the size of the entire stack. Every other `layer()` is
 * wrapped in `position:absolute; inset:0` so it overlays the primary
 * without contributing to layout. Stacking follows call order: layers
 * added BEFORE `primary()` paint underneath it; layers added AFTER
 * paint on top.
 *
 * Usage:
 *   UI::layers()
 *       ->layer(UI::container()->background(Color::yellow(200)))  // behind
 *       ->primary(
 *           UI::column()->padding(Unit::rem(1))->content(
 *               UI::text('Main content')
 *           )
 *       )
 *       ->layer(UI::text('Watermark')->color(Color::red(500)));   // in front
 */
class Layers extends UIElementContent
{
    private bool $primarySet = false;

    public function __construct()
    {
        parent::__construct('div');
        $this->relative();
    }

    /**
     * The single layer in normal flow — its intrinsic size becomes
     * the size of the whole stack. Throws if called more than once.
     */
    public function primary(UIElement $element): static
    {
        if ($this->primarySet) {
            throw new \LogicException('Layers can have only one primary layer');
        }
        $this->primarySet = true;
        $this->children[] = $element;
        return $this;
    }

    /**
     * Add an overlay layer. Called before `primary()` → paints behind
     * it; called after → paints in front. The layer is wrapped in an
     * `absolute; inset:0` container so it fills the primary's box
     * without contributing to layout.
     */
    public function layer(UIElement $element): static
    {
        $this->children[] = UI::container()
            ->absolute()
            ->inset(Unit::none())
            ->content($element);
        return $this;
    }
}
