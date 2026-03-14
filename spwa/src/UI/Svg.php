<?php

namespace Spwa\UI;

/**
 * SVG element for vector graphics.
 *
 * Usage:
 *   UI::svg()
 *       ->viewBox(0, 0, 24, 24)
 *       ->size(Unit::px(24))
 *       ->content(
 *           Svg::path("M12 2L2 7l10 5 10-5-10-5z")->fill("currentColor")
 *       )
 */
class Svg extends UIElement
{
    protected ?string $viewBoxValue = null;
    protected ?string $widthAttr = null;
    protected ?string $heightAttr = null;
    protected ?string $fill = null;
    protected ?string $stroke = null;
    protected ?string $strokeWidth = null;
    /** @var SvgElement[] */
    protected array $children = [];

    /**
     * Set the viewBox attribute.
     */
    public function viewBox(int $minX, int $minY, int $width, int $height): static
    {
        $this->viewBoxValue = "{$minX} {$minY} {$width} {$height}";
        return $this;
    }

    /**
     * Set SVG width attribute.
     */
    public function svgWidth(string $width): static
    {
        $this->widthAttr = $width;
        return $this;
    }

    /**
     * Set SVG height attribute.
     */
    public function svgHeight(string $height): static
    {
        $this->heightAttr = $height;
        return $this;
    }

    /**
     * Set default fill color.
     */
    public function fill(string $color): static
    {
        $this->fill = $color;
        return $this;
    }

    /**
     * Set default stroke color.
     */
    public function stroke(string $color): static
    {
        $this->stroke = $color;
        return $this;
    }

    /**
     * Set default stroke width.
     */
    public function strokeWidth(string $width): static
    {
        $this->strokeWidth = $width;
        return $this;
    }

    /**
     * Add SVG child elements.
     */
    public function content(SvgElement ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    /**
     * Create a path element.
     */
    public static function path(string $d): SvgPath
    {
        return new SvgPath($d);
    }

    /**
     * Create a circle element.
     */
    public static function circle(float $cx, float $cy, float $r): SvgCircle
    {
        return new SvgCircle($cx, $cy, $r);
    }

    /**
     * Create a rect element.
     */
    public static function rect(float $x, float $y, float $width, float $height): SvgRect
    {
        return new SvgRect($x, $y, $width, $height);
    }

    /**
     * Create a line element.
     */
    public static function line(float $x1, float $y1, float $x2, float $y2): SvgLine
    {
        return new SvgLine($x1, $y1, $x2, $y2);
    }

    /**
     * Create a polygon element.
     */
    public static function polygon(string $points): SvgPolygon
    {
        return new SvgPolygon($points);
    }

    /**
     * Create a polyline element.
     */
    public static function polyline(string $points): SvgPolyline
    {
        return new SvgPolyline($points);
    }

    /**
     * Create an ellipse element.
     */
    public static function ellipse(float $cx, float $cy, float $rx, float $ry): SvgEllipse
    {
        return new SvgEllipse($cx, $cy, $rx, $ry);
    }

    /**
     * Create a group element.
     */
    public static function g(): SvgGroup
    {
        return new SvgGroup();
    }

    public function build(): DomNode
    {
        $node = $this->dom()->setTag('svg')
            ->attr('xmlns', 'http://www.w3.org/2000/svg');

        if ($this->viewBoxValue !== null) {
            $node->attr('viewBox', $this->viewBoxValue);
        }

        if ($this->widthAttr !== null) {
            $node->attr('width', $this->widthAttr);
        }

        if ($this->heightAttr !== null) {
            $node->attr('height', $this->heightAttr);
        }

        if ($this->fill !== null) {
            $node->attr('fill', $this->fill);
        }

        if ($this->stroke !== null) {
            $node->attr('stroke', $this->stroke);
        }

        if ($this->strokeWidth !== null) {
            $node->attr('stroke-width', $this->strokeWidth);
        }

        foreach ($this->children as $child) {
            $node->children($child->toNode());
        }

        return $node;
    }
}
