# UI Development Skill

## Core Principles

### Fluent Builder Pattern
All UI elements must be designed using a fluent builder pattern. Methods chain together to construct the final element.

```php
UI::card()
    ->background(Color::white())
    ->padding(Spacing::md())
    ->rounded(Radius::lg());
```

### No CSS Mimicry
This framework does **not** mimic CSS behavior directly. The API describes UI layout programmatically using semantic method names:

- `alignLeft()`, `alignCenter()`, `alignRight()` - not `textAlign('left')`
- `extend()` - not `width('100%')`
- `background()` - not `backgroundColor()`

Methods describe **what** the UI should do, not **how** CSS implements it.

### Programmatic Layout Access
All layouts must be accessible through descriptive methods:

```php
UI::cols(
    UI::text('Left'),
    UI::text('Right')
)->gap(Spacing::md())
 ->alignCenter();
```

### Responsive Design via Chainable Parameters
Responsive behavior, CSS pseudo-elements, and selectors are enabled through chainable parameters on value objects:

```php
$el->color([Color::red()->hover(), Color::blue(200)]);
// Generates: "hover:text-red-600 text-blue-200"

$el->padding([Spacing::sm(), Spacing::lg()->md(), Spacing::xl()->lg()]);
// Generates: "p-2 md:p-4 lg:p-6"

$el->display([Display::hidden(), Display::block()->sm()]);
// Generates: "hidden sm:block"
```

### Strongly Typed Inputs
**No literal strings anywhere.** All inputs must use typed enums, value objects, or dedicated classes:

```php
// CORRECT
->color(Color::blue(500))
->spacing(Spacing::md())
->direction(Direction::cols())

// WRONG - never do this
->color('blue-500')
->spacing('4')
->direction('flex-col')
```

### UI Factory Class
`UI` is the static factory class where most components are created:

```php
UI::text('Hello')
UI::button('Click me')
UI::card()
UI::cols(...)
UI::rows(...)
UI::stack(Direction::cols())
UI::image(src: $url)
```

### Method Overloads for Behavior Variants
Use specific methods for common cases, with generic methods accepting parameters for flexibility:

```php
// Shorthand methods
UI::cols($item1, $item2);  // Flex row (horizontal columns)
UI::rows($item1, $item2);  // Flex column (vertical rows)

// Generic method with direction parameter
UI::stack(Direction::cols()->md());  // Columns on md+ screens

// Under the hood, cols() uses stack()
public static function cols(UIElement ...$children): Stack
{
    return self::stack(Direction::cols(), ...$children);
}
```

### Shared Functionality via UIElement
`UIElement` is the base class containing common functionality for all elements:

- Layout methods: `padding()`, `margin()`, `gap()`
- Appearance: `background()`, `color()`, `border()`, `rounded()`
- Sizing: `width()`, `height()`, `extend()`, `shrink()`
- Positioning: `alignLeft()`, `alignCenter()`, `alignRight()`
- Responsive: All methods accept arrays for responsive values

```php
abstract class UIElement
{
    public function padding(Spacing|array $spacing): static;
    public function background(Color|array $color): static;
    public function rounded(Radius|array $radius): static;
    // ... shared across all elements
}
```

## Value Object Patterns

### Colors
```php
Color::red()        // red-500 (default)
Color::red(600)     // red-600
Color::red()->hover()      // hover:text-red-500
Color::red(700)->focus()   // focus:text-red-700
Color::white()
Color::black()
Color::transparent()
```

### Spacing
```php
Spacing::none()     // 0
Spacing::xs()       // 1
Spacing::sm()       // 2
Spacing::md()       // 4
Spacing::lg()       // 6
Spacing::xl()       // 8
Spacing::custom(12) // 12

// Responsive
Spacing::sm()->md()  // md:p-2 (sm spacing at md breakpoint)
```

### Breakpoints (Chainable)
```php
->sm()   // sm: prefix
->md()   // md: prefix
->lg()   // lg: prefix
->xl()   // xl: prefix
->xxl()  // 2xl: prefix
```

### Pseudo States (Chainable)
```php
->hover()
->focus()
->active()
->disabled()
->first()
->last()
```

### Children via `content()` Method
Children are passed as variadic parameters to the `content()` method:

```php
$card->content(
    UI::text('First child'),
    UI::text('Second child'),
    UI::button('Third child')
);

// Or inline with chaining
UI::card()
    ->padding(Spacing::md())
    ->content(
        UI::text('Hello'),
        UI::text('World')
    );
```

## Example Component

```php
UI::card()
    ->background([Color::white(), Color::gray(100)->hover()])
    ->padding([Spacing::md(), Spacing::lg()->md()])
    ->rounded(Radius::lg())
    ->shadow(Shadow::md())
    ->content(
        UI::cols(
            UI::image(src: $album->cover)
                ->rounded(Radius::md())
                ->width(Size::fixed(48)),
            UI::rows(
                UI::text($album->title)
                    ->font(Font::bold())
                    ->size(TextSize::lg()),
                UI::text($album->artist)
                    ->color(Color::gray(600))
            )->gap(Spacing::xs())
        )->gap(Spacing::md())
         ->alignCenter()
    );
```
