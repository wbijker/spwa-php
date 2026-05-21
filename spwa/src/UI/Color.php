<?php

namespace Spwa\UI;

/**
 * Color property representing a color value only.
 * Context (background, text, border) is determined by the UIElement method.
 * Modifiers (hover, dark, sm, …) come from a Pseudo argument at the call
 * site — see Pseudo.
 *
 * Usage:
 *   Color::red(500)              // red-500
 *   Color::blue(300)->alpha(0.5) // blue-300 at 50% alpha
 */
class Color extends Property
{
    public function __construct(
        protected string $name,
        protected ?int $shade = null,
        protected ?int $opacity = null,
        protected ?float $alphaValue = null,
        protected ?int $r = null,
        protected ?int $g = null,
        protected ?int $b = null,
    ) {
    }

    /** @var string|null Memoized base() output — invariant for an immutable Color */
    private ?string $baseCache = null;

    /** @var array<string, string> Memoized withContext() results, keyed by context */
    private array $contextCache = [];

    /** @var string|null Memoized getValue() output */
    private ?string $valueCache = null;

    /**
     * Reset the memoization caches when this Color is cloned — derive()
     * (used by alpha(), hover(), opacity()…) produces a clone with a
     * mutation, so the parent's caches no longer apply.
     */
    public function __clone(): void
    {
        $this->baseCache = null;
        $this->contextCache = [];
        $this->valueCache = null;
    }

    protected function base(): string
    {
        if ($this->baseCache !== null) {
            return $this->baseCache;
        }

        if ($this->r !== null) {
            $class = "rgb-{$this->r}-{$this->g}-{$this->b}";
            if ($this->alphaValue !== null) {
                $class .= '/a' . (int) round($this->alphaValue * 100);
            }
            return $this->baseCache = $class;
        }

        $class = $this->name;

        if ($this->shade !== null) {
            $class .= '-' . $this->shade;
        }

        if ($this->opacity !== null) {
            $class .= '/' . $this->opacity;
        }

        if ($this->alphaValue !== null) {
            // /a25 etc. so each alpha gets its own class, even when the base
            // color is shared. Suffix is informational; not parsed.
            $class .= '/a' . (int) round($this->alphaValue * 100);
        }

        return $this->baseCache = $class;
    }

    /**
     * Get the color name (for use in UIElement methods).
     */
    public function getName(): string
    {
        return $this->base();
    }

    /**
     * Build class with specific context prefix (e.g. "bg", "text", "border").
     * No modifier prefix — that comes from the caller's Pseudo argument.
     */
    public function withContext(string $context): string
    {
        return $this->contextCache[$context]
            ??= $context . '-' . $this->base();
    }

    /**
     * Set opacity (0-100).
     */
    public function opacity(int $value): static
    {
        return $this->derive(fn($c) => $c->opacity = $value);
    }

    /**
     * Set alpha channel on the resolved CSS color (0.0–1.0). The lookup
     * still finds the base palette entry, then the hex/rgb is converted
     * to `rgba(r, g, b, alpha)` in getValue().
     *
     *   Color::black()->alpha(0.25)         // → rgba(0, 0, 0, 0.25)
     *   Color::red(500)->alpha(0.5)         // → rgba(239, 68, 68, 0.5)
     *   Color::hex('#abcdef')->alpha(0.1)   // → rgba(171, 205, 239, 0.1)
     */
    public function alpha(float $value): static
    {
        return $this->derive(fn($c) => $c->alphaValue = $value);
    }

    /**
     * Non-palette keywords + pure black/white. Resolved directly
     * without going through the OKLCH formula.
     */
    private const SPECIAL = [
        'transparent' => 'transparent',
        'current'     => 'currentColor',
        'inherit'     => 'inherit',
        'white'       => '#ffffff',
        'black'       => '#000000',
        'gradient'    => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    ];

    /**
     * Precomputed hex values for the standard Tailwind stops (50..900).
     * Originally generated from an OKLCH formula keyed by per-family
     * hue + chroma + a shared lightness curve. The formula has been
     * removed; tune values directly here.
     *
     * Shades outside this set resolve to `name-shade` and surface as
     * broken CSS — by design, so typos and out-of-range shades are
     * visible.
     */
    private const SHADES = [
        "red"     => [50=>"#fef2f2", 100=>"#ffe2e2", 200=>"#ffc9c9", 300=>"#ffa2a2", 400=>"#ff636b", 500=>"#fa2945", 600=>"#e7002b", 700=>"#c1001f", 800=>"#9f051a", 900=>"#82171a"],
        "orange"  => [50=>"#fbf4ec", 100=>"#f8e7d3", 200=>"#f5d2ae", 300=>"#f4b06f", 400=>"#f27b00", 500=>"#ee5000", 600=>"#de2d00", 700=>"#bb1f00", 800=>"#9b1c00", 900=>"#801f00"],
        "amber"   => [50=>"#f7f6ed", 100=>"#f0ead6", 200=>"#e6d8b2", 300=>"#dbbd75", 400=>"#cf9400", 500=>"#c97600", 600=>"#bb5e00", 700=>"#9f4b00", 800=>"#863d00", 900=>"#703400"],
        "yellow"  => [50=>"#f6f6ef", 100=>"#ebebda", 200=>"#dddbbb", 300=>"#cac386", 400=>"#b6a028", 500=>"#ab8700", 600=>"#9e7200", 700=>"#875c00", 800=>"#724b00", 900=>"#613f00"],
        "lime"    => [50=>"#f2f7f1", 100=>"#e1efde", 200=>"#c9e2c1", 300=>"#a7ce91", 400=>"#7ab342", 500=>"#65a000", 600=>"#5b8b00", 700=>"#527200", 800=>"#4a5d00", 900=>"#434d00"],
        "green"   => [50=>"#f0f8f2", 100=>"#ddefe2", 200=>"#c0e4ca", 300=>"#92d2a3", 400=>"#45ba6a", 500=>"#00a940", 600=>"#009620", 700=>"#007c13", 800=>"#006711", 900=>"#165514"],
        "emerald" => [50=>"#eff8f4", 100=>"#d9f0e7", 200=>"#b8e5d3", 300=>"#7fd5b3", 400=>"#00be88", 500=>"#00ae6b", 600=>"#009c56", 700=>"#008144", 800=>"#006b37", 900=>"#005930"],
        "teal"    => [50=>"#eff8f7", 100=>"#d9efed", 200=>"#b7e4de", 300=>"#7cd3c8", 400=>"#00bbab", 500=>"#00ab97", 600=>"#009984", 700=>"#007f6c", 800=>"#006958", 900=>"#005849"],
        "cyan"    => [50=>"#eef8f8", 100=>"#d8efef", 200=>"#b6e3e4", 300=>"#79d1d6", 400=>"#00b9c4", 500=>"#00a8b9", 600=>"#0095a9", 700=>"#007b8e", 800=>"#006676", 900=>"#005563"],
        "sky"     => [50=>"#eef8fa", 100=>"#d6eff5", 200=>"#b2e3ef", 300=>"#70d0e8", 400=>"#00b6e1", 500=>"#00a3db", 600=>"#008fcb", 700=>"#0077ab", 800=>"#00628d", 900=>"#005274"],
        "blue"    => [50=>"#f0f6fe", 100=>"#dcebff", 200=>"#bfdbff", 300=>"#92c2ff", 400=>"#519cff", 500=>"#297fff", 600=>"#1d68ff", 700=>"#2155dc", 800=>"#2447b3", 900=>"#253e91"],
        "indigo"  => [50=>"#f2f5ff", 100=>"#e2e9ff", 200=>"#ccd7ff", 300=>"#aabbff", 400=>"#8090ff", 500=>"#6a71ff", 600=>"#5c5aff", 700=>"#4d4ada", 800=>"#413fb2", 900=>"#383890"],
        "violet"  => [50=>"#f5f4fe", 100=>"#e8e7ff", 200=>"#d7d3ff", 300=>"#bfb4ff", 400=>"#a384ff", 500=>"#9461ff", 600=>"#8548f9", 700=>"#703bcf", 800=>"#5e33a9", 900=>"#4f3088"],
        "purple"  => [50=>"#f8f3fc", 100=>"#f0e5fb", 200=>"#e4cff9", 300=>"#d5acf9", 400=>"#c179f9", 500=>"#b353f6", 600=>"#a239e5", 700=>"#862fbf", 800=>"#6f2b9d", 900=>"#5c2a7f"],
        "fuchsia" => [50=>"#faf3fa", 100=>"#f5e3f6", 200=>"#efccf0", 300=>"#e5a7ea", 400=>"#d770e2", 500=>"#cc46db", 600=>"#b929ca", 700=>"#9a22a9", 800=>"#7e228b", 900=>"#682472"],
        "pink"    => [50=>"#fdf2f6", 100=>"#fde2ea", 200=>"#fcc9db", 300=>"#fba1c3", 400=>"#f663a5", 500=>"#ee2d92", 600=>"#db0081", 700=>"#b7006c", 800=>"#960a5a", 900=>"#7b184c"],
        "rose"    => [50=>"#fef2f2", 100=>"#ffe2e2", 200=>"#ffc9cb", 300=>"#ffa1a8", 400=>"#ff6278", 500=>"#f9275c", 600=>"#e5004b", 700=>"#bf003f", 800=>"#9d0137", 900=>"#811531"],
        "slate"   => [50=>"#f5f5f6", 100=>"#e8eaed", 200=>"#d5d9de", 300=>"#bac1ca", 400=>"#96a1af", 500=>"#808c9e", 600=>"#6e7a8d", 700=>"#5b6575", 800=>"#4c5461", 900=>"#414751"],
        "gray"    => [50=>"#f5f5f6", 100=>"#e9eaeb", 200=>"#d8d9dc", 300=>"#bec0c5", 400=>"#9ca0a7", 500=>"#878b94", 600=>"#757982", 700=>"#61656c", 800=>"#51545a", 900=>"#45474b"],
        "zinc"    => [50=>"#f5f5f6", 100=>"#eaeaea", 200=>"#d9d9da", 300=>"#c0c0c2", 400=>"#9f9fa3", 500=>"#8b8b8f", 600=>"#79797e", 700=>"#646468", 800=>"#535356", 900=>"#464649"],
        "neutral" => [50=>"#f5f5f5", 100=>"#eaeaea", 200=>"#d9d9d9", 300=>"#c0c0c0", 400=>"#9f9f9f", 500=>"#8b8b8b", 600=>"#797979", 700=>"#656565", 800=>"#535353", 900=>"#464646"],
        "stone"   => [50=>"#f6f5f5", 100=>"#eae9e9", 200=>"#dad9d7", 300=>"#c3bfbd", 400=>"#a49e9b", 500=>"#908a86", 600=>"#7e7874", 700=>"#696360", 800=>"#575350", 900=>"#494644"],
        "taupe"   => [50=>"#f6f5f4", 100=>"#ede9e7", 200=>"#ded7d4", 300=>"#cabeb6", 400=>"#af9b8f", 500=>"#9d8677", 600=>"#8b7465", 700=>"#736053", 800=>"#5f5045", 900=>"#4f443b"],
        "mauve"   => [50=>"#f8f4f6", 100=>"#f1e6eb", 200=>"#e7d3dc", 300=>"#d9b4c5", 400=>"#c68ba8", 500=>"#b87296", 600=>"#a55f85", 700=>"#894f6f", 800=>"#71425c", 900=>"#5e394e"],
        "mist"    => [50=>"#f4f6f7", 100=>"#e6eaed", 200=>"#d3dae0", 300=>"#b5c2cd", 400=>"#8ea3b4", 500=>"#758fa3", 600=>"#637d92", 700=>"#526879", 800=>"#455664", 900=>"#3b4854"],
        "olive"   => [50=>"#f5f6f2", 100=>"#e9ebe2", 200=>"#d8dbcb", 300=>"#c0c4a6", 400=>"#9fa574", 500=>"#8c9154", 600=>"#7b7f40", 700=>"#666934", 800=>"#55572d", 900=>"#484929"],
    ];

    /**
     * Get the CSS color value (hex, rgb, etc.).
     */
    public function getValue(): string
    {
        if ($this->valueCache !== null) {
            return $this->valueCache;
        }

        // Constructed directly from rgb channel values: emit as-is.
        if ($this->r !== null) {
            return $this->valueCache = $this->alphaValue !== null
                ? "rgba({$this->r}, {$this->g}, {$this->b}, {$this->alphaValue})"
                : "rgb({$this->r}, {$this->g}, {$this->b})";
        }

        if (isset(self::SPECIAL[$this->name])) {
            $resolved = self::SPECIAL[$this->name];
        } elseif ($this->shade !== null && isset(self::SHADES[$this->name][$this->shade])) {
            $resolved = self::SHADES[$this->name][$this->shade];
        } else {
            // Free-form (e.g. Color::hex('#abc')), unknown family, or
            // an out-of-range shade — emit as-is so typos surface as
            // broken CSS.
            $resolved = $this->shade !== null
                ? $this->name . '-' . $this->shade
                : $this->name;
        }

        // Apply alpha to a resolved hex.
        if ($this->alphaValue !== null && str_starts_with($resolved, '#')) {
            [$r, $g, $b] = self::hexChannels(substr($resolved, 1));
            if ($r !== null) {
                return $this->valueCache = "rgba($r, $g, $b, {$this->alphaValue})";
            }
        }

        return $this->valueCache = $resolved;
    }

    /**
     * Parse a hex body (no leading `#`) into [r, g, b] integer channels.
     * Returns [null, null, null] for malformed input.
     * @return array{0: ?int, 1: ?int, 2: ?int}
     */
    private static function hexChannels(string $hex): array
    {
        $len = strlen($hex);
        if ($len === 3 || $len === 4) {
            return [
                hexdec($hex[0] . $hex[0]),
                hexdec($hex[1] . $hex[1]),
                hexdec($hex[2] . $hex[2]),
            ];
        }
        if ($len === 6 || $len === 8) {
            return [
                hexdec(substr($hex, 0, 2)),
                hexdec(substr($hex, 2, 2)),
                hexdec(substr($hex, 4, 2)),
            ];
        }
        return [null, null, null];
    }

    // ============================================================
    // Basic colors
    // ============================================================

    public static function transparent(): static
    {
        static $instance = null;
        return $instance ??= new static('transparent');
    }

    public static function current(): static
    {
        static $instance = null;
        return $instance ??= new static('current');
    }

    public static function inherit(): static
    {
        static $instance = null;
        return $instance ??= new static('inherit');
    }

    public static function white(): static
    {
        static $instance = null;
        return $instance ??= new static('white');
    }

    public static function black(): static
    {
        static $instance = null;
        return $instance ??= new static('black');
    }

    // ============================================================
    // Color palette (50-950 shades)
    // ============================================================

    public static function slate(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('slate', $shade);
    }

    public static function gray(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('gray', $shade);
    }

    public static function zinc(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('zinc', $shade);
    }

    public static function neutral(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('neutral', $shade);
    }

    public static function stone(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('stone', $shade);
    }

    public static function red(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('red', $shade);
    }

    public static function orange(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('orange', $shade);
    }

    public static function amber(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('amber', $shade);
    }

    public static function yellow(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('yellow', $shade);
    }

    public static function lime(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('lime', $shade);
    }

    public static function green(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('green', $shade);
    }

    public static function emerald(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('emerald', $shade);
    }

    public static function teal(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('teal', $shade);
    }

    public static function cyan(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('cyan', $shade);
    }

    public static function sky(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('sky', $shade);
    }

    public static function blue(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('blue', $shade);
    }

    public static function indigo(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('indigo', $shade);
    }

    public static function violet(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('violet', $shade);
    }

    public static function purple(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('purple', $shade);
    }

    public static function fuchsia(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('fuchsia', $shade);
    }

    public static function pink(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('pink', $shade);
    }

    public static function rose(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('rose', $shade);
    }

    // ============================================================
    // Custom colors (non-Tailwind)
    // ============================================================

    public static function taupe(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('taupe', $shade);
    }

    public static function mauve(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('mauve', $shade);
    }

    public static function mist(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('mist', $shade);
    }

    public static function olive(int $shade = 500): static
    {
        static $cache = [];
        return $cache[$shade] ??= new static('olive', $shade);
    }

    /**
     * Gradient background (placeholder for demo).
     */
    public static function gradient(): static
    {
        static $instance = null;
        return $instance ??= new static('gradient');
    }

    /**
     * Custom hex color value (e.g. "#abcdef", "#abc").
     */
    public static function hex(string $hex): static
    {
        return new static($hex);
    }

    /**
     * Construct from RGB channel values (0–255 each). No alpha channel.
     */
    public static function rgb(int $r, int $g, int $b): static
    {
        return new static('rgb', null, null, null, $r, $g, $b);
    }

    /**
     * Construct from RGBA channel values (0–255 RGB, 0.0–1.0 alpha).
     */
    public static function rgba(int $r, int $g, int $b, float $a): static
    {
        return new static('rgba', null, null, $a, $r, $g, $b);
    }
}
