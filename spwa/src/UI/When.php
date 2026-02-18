<?php

namespace Spwa\UI;

enum Combinator: string
{
    case Descendant = ' ';
    case Child = '>';
    case Adjacent = '+';
    case Sibling = '~';
}

enum PseudoClass: string
{
    case Hover = 'hover';
    case Active = 'active';
    case Focus = 'focus';
    case FocusVisible = 'focus-visible';
    case FocusWithin = 'focus-within';
    case Visited = 'visited';
    case Link = 'link';
    case FirstChild = 'first-child';
    case LastChild = 'last-child';
    case OnlyChild = 'only-child';
    case NthChild = 'nth-child';
    case NthLastChild = 'nth-last-child';
    case FirstOfType = 'first-of-type';
    case LastOfType = 'last-of-type';
    case OddChild = 'nth-child(odd)';
    case EvenChild = 'nth-child(even)';
    case Disabled = 'disabled';
    case Enabled = 'enabled';
    case Checked = 'checked';
    case Required = 'required';
    case Optional = 'optional';
    case Valid = 'valid';
    case Invalid = 'invalid';
    case Empty = 'empty';
    case Not = 'not';
    case Is = 'is';
    case Where = 'where';
    case Has = 'has';
}

enum Breakpoint: string
{
    case Small = 'sm';      // 640px
    case Medium = 'md';     // 768px
    case Large = 'lg';      // 1024px
    case ExtraLarge = 'xl'; // 1280px
    case TwoXL = '2xl';     // 1536px
}

enum ColorScheme: string
{
    case Dark = 'dark';
    case Light = 'light';
}

enum PseudoElement: string
{
    case Before = 'before';
    case After = 'after';
    case FirstLine = 'first-line';
    case FirstLetter = 'first-letter';
    case Placeholder = 'placeholder';
    case Selection = 'selection';
    case Marker = 'marker';
}

/**
 * Fluent style condition builder with chainable API.
 *
 * @method static When hover()
 * @method static When active()
 * @method static When focus()
 * @method static When focusVisible()
 * @method static When focusWithin()
 * @method static When visited()
 * @method static When disabled()
 * @method static When enabled()
 * @method static When checked()
 * @method static When required()
 * @method static When valid()
 * @method static When invalid()
 * @method static When firstChild()
 * @method static When lastChild()
 * @method static When onlyChild()
 * @method static When oddChild()
 * @method static When evenChild()
 * @method static When empty()
 * @method static When sm()
 * @method static When md()
 * @method static When lg()
 * @method static When xl()
 * @method static When xxl()
 * @method static When dark()
 * @method static When light()
 * @method static When before()
 * @method static When after()
 * @method static When placeholder()
 * @method static When selection()
 * @method static When marker()
 * @method static When firstLine()
 * @method static When firstLetter()
 */
class When
{
    /** @var PseudoClass[] */
    public array $pseudoClasses = [];
    public ?string $pseudoClassArg = null;
    public ?PseudoElement $pseudoElement = null;
    public ?Breakpoint $breakpoint = null;
    public ?ColorScheme $colorScheme = null;
    public ?Combinator $combinator = null;

    private static array $pseudoClassMap = [
        'hover' => PseudoClass::Hover,
        'active' => PseudoClass::Active,
        'focus' => PseudoClass::Focus,
        'focusVisible' => PseudoClass::FocusVisible,
        'focusWithin' => PseudoClass::FocusWithin,
        'visited' => PseudoClass::Visited,
        'disabled' => PseudoClass::Disabled,
        'enabled' => PseudoClass::Enabled,
        'checked' => PseudoClass::Checked,
        'required' => PseudoClass::Required,
        'valid' => PseudoClass::Valid,
        'invalid' => PseudoClass::Invalid,
        'firstChild' => PseudoClass::FirstChild,
        'lastChild' => PseudoClass::LastChild,
        'onlyChild' => PseudoClass::OnlyChild,
        'oddChild' => PseudoClass::OddChild,
        'evenChild' => PseudoClass::EvenChild,
        'empty' => PseudoClass::Empty,
    ];

    private static array $breakpointMap = [
        'sm' => Breakpoint::Small,
        'md' => Breakpoint::Medium,
        'lg' => Breakpoint::Large,
        'xl' => Breakpoint::ExtraLarge,
        'xxl' => Breakpoint::TwoXL,
    ];

    private static array $colorSchemeMap = [
        'dark' => ColorScheme::Dark,
        'light' => ColorScheme::Light,
    ];

    private static array $pseudoElementMap = [
        'before' => PseudoElement::Before,
        'after' => PseudoElement::After,
        'placeholder' => PseudoElement::Placeholder,
        'selection' => PseudoElement::Selection,
        'marker' => PseudoElement::Marker,
        'firstLine' => PseudoElement::FirstLine,
        'firstLetter' => PseudoElement::FirstLetter,
    ];

    private function with(callable $modifier): self
    {
        $clone = clone $this;
        $modifier($clone);
        return $clone;
    }

    public static function __callStatic(string $name, array $arguments): self
    {
        return (new self)->$name(...$arguments);
    }

    public function __call(string $name, array $arguments): self
    {
        if (isset(self::$pseudoClassMap[$name])) {
            return $this->with(fn($s) => $s->pseudoClasses[] = self::$pseudoClassMap[$name]);
        }

        if (isset(self::$breakpointMap[$name])) {
            return $this->with(fn($s) => $s->breakpoint = self::$breakpointMap[$name]);
        }

        if (isset(self::$colorSchemeMap[$name])) {
            return $this->with(fn($s) => $s->colorScheme = self::$colorSchemeMap[$name]);
        }

        if (isset(self::$pseudoElementMap[$name])) {
            return $this->with(fn($s) => $s->pseudoElement = self::$pseudoElementMap[$name]);
        }

        throw new \BadMethodCallException("Method $name does not exist on When");
    }

    public static function nthChild(string $expr): self
    {
        $s = new self;
        $s->pseudoClasses[] = PseudoClass::NthChild;
        $s->pseudoClassArg = $expr;
        return $s;
    }

    public static function nthLastChild(string $expr): self
    {
        $s = new self;
        $s->pseudoClasses[] = PseudoClass::NthLastChild;
        $s->pseudoClassArg = $expr;
        return $s;
    }
}
