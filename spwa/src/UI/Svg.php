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

    public function render(): Node
    {
        $node = $this->node('svg')
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

/**
 * Base class for SVG child elements.
 */
abstract class SvgElement
{
    protected ?string $fill = null;
    protected ?string $stroke = null;
    protected ?string $strokeWidth = null;
    protected ?string $strokeLinecap = null;
    protected ?string $strokeLinejoin = null;
    protected ?string $opacity = null;
    protected ?string $transform = null;

    public function fill(string $color): static
    {
        $this->fill = $color;
        return $this;
    }

    public function stroke(string $color): static
    {
        $this->stroke = $color;
        return $this;
    }

    public function strokeWidth(string $width): static
    {
        $this->strokeWidth = $width;
        return $this;
    }

    public function strokeLinecap(string $cap): static
    {
        $this->strokeLinecap = $cap;
        return $this;
    }

    public function strokeLinejoin(string $join): static
    {
        $this->strokeLinejoin = $join;
        return $this;
    }

    public function opacity(string $value): static
    {
        $this->opacity = $value;
        return $this;
    }

    public function transform(string $value): static
    {
        $this->transform = $value;
        return $this;
    }

    protected function applyCommonAttrs(Node $node): void
    {
        if ($this->fill !== null) {
            $node->attr('fill', $this->fill);
        }
        if ($this->stroke !== null) {
            $node->attr('stroke', $this->stroke);
        }
        if ($this->strokeWidth !== null) {
            $node->attr('stroke-width', $this->strokeWidth);
        }
        if ($this->strokeLinecap !== null) {
            $node->attr('stroke-linecap', $this->strokeLinecap);
        }
        if ($this->strokeLinejoin !== null) {
            $node->attr('stroke-linejoin', $this->strokeLinejoin);
        }
        if ($this->opacity !== null) {
            $node->attr('opacity', $this->opacity);
        }
        if ($this->transform !== null) {
            $node->attr('transform', $this->transform);
        }
    }

    abstract public function toNode(): Node;
}

class SvgPath extends SvgElement
{
    public function __construct(protected string $d)
    {
    }

    public function toNode(): Node
    {
        $node = Node::el('path')->attr('d', $this->d);
        $this->applyCommonAttrs($node);
        return $node;
    }
}

class SvgCircle extends SvgElement
{
    public function __construct(
        protected float $cx,
        protected float $cy,
        protected float $r
    ) {
    }

    public function toNode(): Node
    {
        $node = Node::el('circle')
            ->attr('cx', (string)$this->cx)
            ->attr('cy', (string)$this->cy)
            ->attr('r', (string)$this->r);
        $this->applyCommonAttrs($node);
        return $node;
    }
}

class SvgRect extends SvgElement
{
    protected ?float $rx = null;
    protected ?float $ry = null;

    public function __construct(
        protected float $x,
        protected float $y,
        protected float $width,
        protected float $height
    ) {
    }

    public function rounded(float $rx, ?float $ry = null): static
    {
        $this->rx = $rx;
        $this->ry = $ry;
        return $this;
    }

    public function toNode(): Node
    {
        $node = Node::el('rect')
            ->attr('x', (string)$this->x)
            ->attr('y', (string)$this->y)
            ->attr('width', (string)$this->width)
            ->attr('height', (string)$this->height);

        if ($this->rx !== null) {
            $node->attr('rx', (string)$this->rx);
        }
        if ($this->ry !== null) {
            $node->attr('ry', (string)$this->ry);
        }

        $this->applyCommonAttrs($node);
        return $node;
    }
}

class SvgLine extends SvgElement
{
    public function __construct(
        protected float $x1,
        protected float $y1,
        protected float $x2,
        protected float $y2
    ) {
    }

    public function toNode(): Node
    {
        $node = Node::el('line')
            ->attr('x1', (string)$this->x1)
            ->attr('y1', (string)$this->y1)
            ->attr('x2', (string)$this->x2)
            ->attr('y2', (string)$this->y2);
        $this->applyCommonAttrs($node);
        return $node;
    }
}

class SvgPolygon extends SvgElement
{
    public function __construct(protected string $points)
    {
    }

    public function toNode(): Node
    {
        $node = Node::el('polygon')->attr('points', $this->points);
        $this->applyCommonAttrs($node);
        return $node;
    }
}

class SvgPolyline extends SvgElement
{
    public function __construct(protected string $points)
    {
    }

    public function toNode(): Node
    {
        $node = Node::el('polyline')->attr('points', $this->points);
        $this->applyCommonAttrs($node);
        return $node;
    }
}

class SvgEllipse extends SvgElement
{
    public function __construct(
        protected float $cx,
        protected float $cy,
        protected float $rx,
        protected float $ry
    ) {
    }

    public function toNode(): Node
    {
        $node = Node::el('ellipse')
            ->attr('cx', (string)$this->cx)
            ->attr('cy', (string)$this->cy)
            ->attr('rx', (string)$this->rx)
            ->attr('ry', (string)$this->ry);
        $this->applyCommonAttrs($node);
        return $node;
    }
}

class SvgGroup extends SvgElement
{
    /** @var SvgElement[] */
    protected array $children = [];

    public function content(SvgElement ...$children): static
    {
        $this->children = array_merge($this->children, $children);
        return $this;
    }

    public function toNode(): Node
    {
        $node = Node::el('g');
        $this->applyCommonAttrs($node);

        foreach ($this->children as $child) {
            $node->children($child->toNode());
        }

        return $node;
    }
}
