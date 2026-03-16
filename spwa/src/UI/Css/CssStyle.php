<?php

namespace Spwa\UI\Css;

/**
 * Fluent CSS style builder with compression support.
 *
 * Usage:
 *   CssStyle::make('bg-red-500')->backgroundColor('#ef4444')
 *   CssStyle::make('hover:bg-blue-500')->hover()->backgroundColor('#3b82f6')
 *   CssStyle::make('md:flex')->md()->display(CssValue::Flex)
 */
class CssStyle
{
    private string $name;
    private ?CssBreakpoint $breakpoint = null;
    private ?CssColorScheme $colorScheme = null;
    /** @var CssPseudo[] */
    private array $pseudos = [];
    /** @var array<CssProperty|string, CssValue|string> */
    private array $properties = [];

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Create a new CSS style.
     */
    public static function make(string $name): self
    {
        return new self($name);
    }

    // ============================================================
    // Breakpoint modifiers
    // ============================================================

    public function sm(): self
    {
        $this->breakpoint = CssBreakpoint::Sm;
        return $this;
    }

    public function md(): self
    {
        $this->breakpoint = CssBreakpoint::Md;
        return $this;
    }

    public function lg(): self
    {
        $this->breakpoint = CssBreakpoint::Lg;
        return $this;
    }

    public function xl(): self
    {
        $this->breakpoint = CssBreakpoint::Xl;
        return $this;
    }

    public function xxl(): self
    {
        $this->breakpoint = CssBreakpoint::Xxl;
        return $this;
    }

    // ============================================================
    // Color scheme modifiers
    // ============================================================

    public function dark(): self
    {
        $this->colorScheme = CssColorScheme::Dark;
        return $this;
    }

    public function light(): self
    {
        $this->colorScheme = CssColorScheme::Light;
        return $this;
    }

    // ============================================================
    // Pseudo-class modifiers
    // ============================================================

    public function hover(): self
    {
        $this->pseudos[] = CssPseudo::Hover;
        return $this;
    }

    public function active(): self
    {
        $this->pseudos[] = CssPseudo::Active;
        return $this;
    }

    public function focus(): self
    {
        $this->pseudos[] = CssPseudo::Focus;
        return $this;
    }

    public function focusVisible(): self
    {
        $this->pseudos[] = CssPseudo::FocusVisible;
        return $this;
    }

    public function focusWithin(): self
    {
        $this->pseudos[] = CssPseudo::FocusWithin;
        return $this;
    }

    public function visited(): self
    {
        $this->pseudos[] = CssPseudo::Visited;
        return $this;
    }

    public function disabled(): self
    {
        $this->pseudos[] = CssPseudo::Disabled;
        return $this;
    }

    public function enabled(): self
    {
        $this->pseudos[] = CssPseudo::Enabled;
        return $this;
    }

    public function checked(): self
    {
        $this->pseudos[] = CssPseudo::Checked;
        return $this;
    }

    public function required(): self
    {
        $this->pseudos[] = CssPseudo::Required;
        return $this;
    }

    public function valid(): self
    {
        $this->pseudos[] = CssPseudo::Valid;
        return $this;
    }

    public function invalid(): self
    {
        $this->pseudos[] = CssPseudo::Invalid;
        return $this;
    }

    public function placeholder(): self
    {
        $this->pseudos[] = CssPseudo::Placeholder;
        return $this;
    }

    public function first(): self
    {
        $this->pseudos[] = CssPseudo::First;
        return $this;
    }

    public function last(): self
    {
        $this->pseudos[] = CssPseudo::Last;
        return $this;
    }

    public function only(): self
    {
        $this->pseudos[] = CssPseudo::Only;
        return $this;
    }

    public function odd(): self
    {
        $this->pseudos[] = CssPseudo::Odd;
        return $this;
    }

    public function even(): self
    {
        $this->pseudos[] = CssPseudo::Even;
        return $this;
    }

    public function empty(): self
    {
        $this->pseudos[] = CssPseudo::Empty;
        return $this;
    }

    // ============================================================
    // CSS Properties - Layout
    // ============================================================

    public function display(CssValue|string $value): self
    {
        return $this->set(CssProperty::Display, $value);
    }

    public function flexDirection(CssValue|string $value): self
    {
        return $this->set(CssProperty::FlexDirection, $value);
    }

    public function justifyContent(CssValue|string $value): self
    {
        return $this->set(CssProperty::JustifyContent, $value);
    }

    public function alignItems(CssValue|string $value): self
    {
        return $this->set(CssProperty::AlignItems, $value);
    }

    public function alignSelf(CssValue|string $value): self
    {
        return $this->set(CssProperty::AlignSelf, $value);
    }

    public function justifySelf(CssValue|string $value): self
    {
        return $this->set(CssProperty::JustifySelf, $value);
    }

    public function flexWrap(CssValue|string $value): self
    {
        return $this->set(CssProperty::FlexWrap, $value);
    }

    public function flexGrow(CssValue|string $value): self
    {
        return $this->set(CssProperty::FlexGrow, $value);
    }

    public function flexShrink(CssValue|string $value): self
    {
        return $this->set(CssProperty::FlexShrink, $value);
    }

    public function flexBasis(CssValue|string $value): self
    {
        return $this->set(CssProperty::FlexBasis, $value);
    }

    public function gap(CssValue|string $value): self
    {
        return $this->set(CssProperty::Gap, $value);
    }

    public function columnGap(CssValue|string $value): self
    {
        return $this->set(CssProperty::ColumnGap, $value);
    }

    public function rowGap(CssValue|string $value): self
    {
        return $this->set(CssProperty::RowGap, $value);
    }

    public function gridTemplateColumns(string $value): self
    {
        return $this->set(CssProperty::GridTemplateColumns, $value);
    }

    // ============================================================
    // CSS Properties - Sizing
    // ============================================================

    public function width(CssValue|string $value): self
    {
        return $this->set(CssProperty::Width, $value);
    }

    public function height(CssValue|string $value): self
    {
        return $this->set(CssProperty::Height, $value);
    }

    public function minWidth(CssValue|string $value): self
    {
        return $this->set(CssProperty::MinWidth, $value);
    }

    public function maxWidth(CssValue|string $value): self
    {
        return $this->set(CssProperty::MaxWidth, $value);
    }

    public function minHeight(CssValue|string $value): self
    {
        return $this->set(CssProperty::MinHeight, $value);
    }

    public function maxHeight(CssValue|string $value): self
    {
        return $this->set(CssProperty::MaxHeight, $value);
    }

    // ============================================================
    // CSS Properties - Spacing
    // ============================================================

    public function padding(CssValue|string $value): self
    {
        return $this->set(CssProperty::Padding, $value);
    }

    public function paddingTop(CssValue|string $value): self
    {
        return $this->set(CssProperty::PaddingTop, $value);
    }

    public function paddingRight(CssValue|string $value): self
    {
        return $this->set(CssProperty::PaddingRight, $value);
    }

    public function paddingBottom(CssValue|string $value): self
    {
        return $this->set(CssProperty::PaddingBottom, $value);
    }

    public function paddingLeft(CssValue|string $value): self
    {
        return $this->set(CssProperty::PaddingLeft, $value);
    }

    public function margin(CssValue|string $value): self
    {
        return $this->set(CssProperty::Margin, $value);
    }

    public function marginTop(CssValue|string $value): self
    {
        return $this->set(CssProperty::MarginTop, $value);
    }

    public function marginRight(CssValue|string $value): self
    {
        return $this->set(CssProperty::MarginRight, $value);
    }

    public function marginBottom(CssValue|string $value): self
    {
        return $this->set(CssProperty::MarginBottom, $value);
    }

    public function marginLeft(CssValue|string $value): self
    {
        return $this->set(CssProperty::MarginLeft, $value);
    }

    // ============================================================
    // CSS Properties - Colors
    // ============================================================

    public function backgroundColor(CssValue|string $value): self
    {
        return $this->set(CssProperty::BackgroundColor, $value);
    }

    public function color(CssValue|string $value): self
    {
        return $this->set(CssProperty::Color, $value);
    }

    // ============================================================
    // CSS Properties - Border
    // ============================================================

    public function borderRadius(CssValue|string $value): self
    {
        return $this->set(CssProperty::BorderRadius, $value);
    }

    public function borderWidth(CssValue|string $value): self
    {
        return $this->set(CssProperty::BorderWidth, $value);
    }

    public function borderStyle(CssValue|string $value): self
    {
        return $this->set(CssProperty::BorderStyle, $value);
    }

    public function borderColor(CssValue|string $value): self
    {
        return $this->set(CssProperty::BorderColor, $value);
    }

    // ============================================================
    // CSS Properties - Typography
    // ============================================================

    public function fontSize(CssValue|string $value): self
    {
        return $this->set(CssProperty::FontSize, $value);
    }

    public function fontWeight(CssValue|string $value): self
    {
        return $this->set(CssProperty::FontWeight, $value);
    }

    public function lineHeight(CssValue|string $value): self
    {
        return $this->set(CssProperty::LineHeight, $value);
    }

    public function textAlign(CssValue|string $value): self
    {
        return $this->set(CssProperty::TextAlign, $value);
    }

    public function textDecoration(CssValue|string $value): self
    {
        return $this->set(CssProperty::TextDecoration, $value);
    }

    // ============================================================
    // CSS Properties - Position
    // ============================================================

    public function position(CssValue|string $value): self
    {
        return $this->set(CssProperty::Position, $value);
    }

    public function top(CssValue|string $value): self
    {
        return $this->set(CssProperty::Top, $value);
    }

    public function right(CssValue|string $value): self
    {
        return $this->set(CssProperty::Right, $value);
    }

    public function bottom(CssValue|string $value): self
    {
        return $this->set(CssProperty::Bottom, $value);
    }

    public function left(CssValue|string $value): self
    {
        return $this->set(CssProperty::Left, $value);
    }

    public function zIndex(CssValue|string $value): self
    {
        return $this->set(CssProperty::ZIndex, $value);
    }

    // ============================================================
    // CSS Properties - Visual
    // ============================================================

    public function boxShadow(string $value): self
    {
        return $this->set(CssProperty::BoxShadow, $value);
    }

    public function opacity(CssValue|string $value): self
    {
        return $this->set(CssProperty::Opacity, $value);
    }

    public function visibility(CssValue|string $value): self
    {
        return $this->set(CssProperty::Visibility, $value);
    }

    public function overflow(CssValue|string $value): self
    {
        return $this->set(CssProperty::Overflow, $value);
    }

    public function overflowX(CssValue|string $value): self
    {
        return $this->set(CssProperty::OverflowX, $value);
    }

    public function overflowY(CssValue|string $value): self
    {
        return $this->set(CssProperty::OverflowY, $value);
    }

    public function cursor(CssValue|string $value): self
    {
        return $this->set(CssProperty::Cursor, $value);
    }

    public function objectFit(CssValue|string $value): self
    {
        return $this->set(CssProperty::ObjectFit, $value);
    }

    public function objectPosition(CssValue|string $value): self
    {
        return $this->set(CssProperty::ObjectPosition, $value);
    }

    // ============================================================
    // CSS Properties - Transitions & Transforms
    // ============================================================

    public function transitionProperty(CssValue|string $value): self
    {
        return $this->set(CssProperty::TransitionProperty, $value);
    }

    public function transitionDuration(string $value): self
    {
        return $this->set(CssProperty::TransitionDuration, $value);
    }

    public function transitionTimingFunction(string $value): self
    {
        return $this->set(CssProperty::TransitionTimingFunction, $value);
    }

    public function transform(string $value): self
    {
        return $this->set(CssProperty::Transform, $value);
    }

    // ============================================================
    // Generic setter
    // ============================================================

    /**
     * Set a property with value (enum or string).
     */
    public function set(CssProperty|string $property, CssValue|string $value): self
    {
        $this->properties[$property instanceof CssProperty ? $property : $property] = $value;
        return $this;
    }

    // ============================================================
    // Output methods
    // ============================================================

    /**
     * Get the full class name including modifiers.
     */
    public function getClassName(): string
    {
        $parts = [];

        if ($this->breakpoint !== null) {
            $parts[] = $this->breakpoint->toPrefix();
        }

        if ($this->colorScheme !== null) {
            $parts[] = $this->colorScheme->toPrefix();
        }

        foreach ($this->pseudos as $pseudo) {
            $parts[] = $pseudo->toPrefix();
        }

        $parts[] = $this->name;

        return implode(':', $parts);
    }

    /**
     * Get raw CSS rule string.
     */
    public function toCss(): string
    {
        $selector = '.' . self::escapeClassName($this->getClassName());

        // Add pseudo selectors
        foreach ($this->pseudos as $pseudo) {
            $selector .= $pseudo->toSelector();
        }

        // Build properties
        $props = [];
        foreach ($this->properties as $prop => $value) {
            $propName = $prop instanceof CssProperty ? $prop->toCss() : $prop;
            $propValue = $value instanceof CssValue ? $value->toCss() : $value;
            $props[] = $propName . ':' . $propValue;
        }

        $rule = $selector . '{' . implode(';', $props) . '}';

        // Wrap in media query if needed
        $mediaConditions = [];
        if ($this->breakpoint !== null) {
            $mediaConditions[] = $this->breakpoint->toMediaQuery();
        }
        if ($this->colorScheme !== null) {
            $mediaConditions[] = $this->colorScheme->toMediaQuery();
        }

        if (!empty($mediaConditions)) {
            $rule = '@media ' . implode(' and ', $mediaConditions) . '{' . $rule . '}';
        }

        return $rule;
    }

    /**
     * Get compressed representation for transmission.
     * Returns a flat array: [modifiers, prop1, val1, prop2, val2, ...]
     *
     * Modifiers byte format:
     *   - First 3 bits: breakpoint (0-4, 7 = none)
     *   - Next 2 bits: color scheme (0-1, 3 = none)
     *   - Remaining: pseudo count follows
     *
     * Or simplified: [breakpoint|null, colorScheme|null, [pseudos], prop1, val1, ...]
     */
    public function toCompressed(): array
    {
        $result = [];

        // Modifiers: [breakpoint, colorScheme, pseudos[]]
        $result[] = $this->breakpoint?->value;
        $result[] = $this->colorScheme?->value;
        $result[] = array_map(fn($p) => $p->value, $this->pseudos);

        // Properties as alternating [prop, value, prop, value, ...]
        foreach ($this->properties as $prop => $value) {
            // Property: use index if enum, string otherwise
            $result[] = $prop instanceof CssProperty ? $prop->value : $prop;
            // Value: use index if enum, string otherwise
            $result[] = $value instanceof CssValue ? $value->value : $value;
        }

        return $result;
    }

    /**
     * Get legacy format (className => [prop => value]).
     */
    public function toLegacy(): array
    {
        $props = [];
        foreach ($this->properties as $prop => $value) {
            $propName = $prop instanceof CssProperty ? $prop->toCss() : $prop;
            $propValue = $value instanceof CssValue ? $value->toCss() : $value;
            $props[$propName] = $propValue;
        }
        return [$this->getClassName() => $props];
    }

    /**
     * Escape class name for CSS selector.
     */
    private static function escapeClassName(string $class): string
    {
        return preg_replace('/([.:[\]\/()>,+~])/', '\\\\$1', $class);
    }

    /**
     * Generate JS arrays for the decompression runtime.
     */
    public static function generateJsRuntime(): string
    {
        $P = json_encode(CssProperty::toArray());
        $V = json_encode(CssValue::toArray());
        $B = json_encode(CssBreakpoint::toArray());
        $C = json_encode(CssColorScheme::toArray());
        $X = json_encode(CssPseudo::toArray());

        return "var P={$P};var V={$V};var B={$B};var C={$C};var X={$X};";
    }
}
