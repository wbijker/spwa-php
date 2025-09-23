<div class="mx-auto flex max-w-sm items-center gap-x-4 rounded-xl bg-white p-6 shadow-lg
outline outline-black/5 dark:bg-slate-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
    <img class="size-12 shrink-0" src="/img/logo.svg" alt="ChitChat Logo"/>
    <div>
        <div class="text-xl font-medium text-black dark:text-white">ChitChat</div>
        <p class="text-gray-500 dark:text-gray-400">You have a new message!</p>
    </div>
</div>


<?php


class UI
{
    static function rows(): Element
    {
        return new Element();
    }

    static function cols(): Element
    {
        return new Element();
    }

    static function columns(): Element
    {
        return new Element();
    }

    static function image(string $src, ?string $alt = null): Element
    {
        return new Element();
    }

    static function text(string $content): TextElement
    {
        return new TextElement();
    }
}

class TextElement
{

    function color(Color ...$colors): static
    {
        return $this;
    }


    function fontMedium(): static
    {
        return $this;
    }

    function textXl(): static
    {
        return $this;
    }
}

class Style
{
    function hover(): static
    {
        return $this;
    }

    function dark(): static
    {
        return $this;
    }

    function break(Unit ...$units): static
    {
        return $this;
    }

//    function md(): static
//    {
//        return $this;
//    }
//
//    function lg(): static
//    {
//        return $this;
//    }
//
//    function xl(): static
//    {
//        return $this;
//    }
}

class Unit extends Style
{
    public static function none(): Unit
    {
        return new Unit();
    }

    static function sm(): Unit
    {
        return new Unit();
    }

    static function md(): Unit
    {
        return new Unit();
    }

    static function lg(): Unit
    {
        return new Unit();
    }

    static function xl(): Unit
    {
        return new Unit();

    }

    static function value(int $value): Unit
    {
        return new Unit();
    }

    public static function single(): Unit
    {
        return new Unit();
    }
}

class Color extends Style
{

    static function white(): Color
    {
        return new Color();
    }

    static function black($opacity = 100): Color
    {
        return new Color();
    }

    static function gray($shade): Color
    {
        return new Color();
    }

    static function slate($shade): Color
    {
        return new Color();
    }

    static function green($shade): Color
    {
        return new Color();
    }
}

class Element
{

    function alignCenter(): static
    {
        return $this;
    }

    function alignRight(): static
    {
        return $this;
    }

    function alignLeft(): static
    {
        return $this;
    }

    function alignTop(): static
    {
        return $this;

    }

    function alignMiddle(): static
    {
        return $this;

    }

    function alignBottom(): static
    {
        return $this;

    }

    function maxWidth(Unit ...$units): static
    {
        return $this;
    }

    function padding(Unit ...$unit): static
    {
        return $this;
    }

    function radius(Unit ...$units): static
    {
        return $this;
    }

    function shadow(Unit ...$units): static
    {
        return $this;
    }

    function background(Color ...$color): static
    {
        return $this;
    }

    function outline(Unit ...$units): static
    {
        return $this;
    }

    function outlineColor(Color ...$color): static
    {
        return $this;
    }

    function size(Unit ...$units): static
    {
        return $this;
    }

    function shrink(Unit ...$units): static
    {
        return $this;
    }

    function children(array $children): static
    {
        return $this;
    }

}


$ui = UI::rows()
        ->alignCenter()
        ->maxWidth(Unit::sm())
        ->padding(Unit::value(5))
        ->radius(Unit::xl())
        ->shadow(Unit::xl(), Unit::none()->dark())
        ->background(Color::white(), Color::slate(800)->dark(), Color::green(300)->hover())
        ->outline(Unit::single(), Unit::value(-1)->dark())
        ->outlineColor(Color::black(), Color::black(5)->dark())
        ->children([
                UI::image("/img/logo.svg", "ChitChat Logo")
                        ->size(Unit::value(12))
                        ->shrink(Unit::value(0)),

                Ui::cols()->children([
                        Ui::text("Chitchat")
                                ->textXl()
                                ->fontMedium()
                                ->color(Color::black(), dark: Color::white()),

                        Ui::text("You have a new message!")
                                ->color(Color::gray(500), dark: Color::gray(400))
                ])
        ]);
