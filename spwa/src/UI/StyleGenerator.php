<?php

namespace Spwa\UI;

/**
 * Generates CSS output from collected styles.
 * Abstracts the inner workings of CSS semantics.
 */
class StyleGenerator
{
    /** @var array<string, array<string, string>> */
    private array $styles = [];

    /**
     * Common CSS property names mapped to indices.
     * These are shipped once in the JS runtime dictionary.
     */
    private const COMMON_PROPERTIES = [
        'display' => 0,
        'flex-direction' => 1,
        'justify-content' => 2,
        'align-items' => 3,
        'gap' => 4,
        'padding' => 5,
        'margin' => 6,
        'width' => 7,
        'height' => 8,
        'background-color' => 9,
        'color' => 10,
        'font-size' => 11,
        'font-weight' => 12,
        'border-radius' => 13,
        'box-shadow' => 14,
        'flex-wrap' => 15,
        'flex-grow' => 16,
        'flex-shrink' => 17,
        'position' => 18,
        'top' => 19,
        'right' => 20,
        'bottom' => 21,
        'left' => 22,
        'text-align' => 23,
        'text-decoration' => 24,
        'line-height' => 25,
        'overflow' => 26,
        'opacity' => 27,
        'z-index' => 28,
        'cursor' => 29,
        'grid-template-columns' => 30,
        'column-gap' => 31,
        'row-gap' => 32,
        'max-width' => 33,
        'min-width' => 34,
        'max-height' => 35,
        'min-height' => 36,
        'padding-top' => 37,
        'padding-right' => 38,
        'padding-bottom' => 39,
        'padding-left' => 40,
        'margin-top' => 41,
        'margin-right' => 42,
        'margin-bottom' => 43,
        'margin-left' => 44,
        'object-fit' => 45,
        'object-position' => 46,
    ];

    /**
     * Common CSS values mapped to indices.
     * These are shipped once in the JS runtime dictionary.
     */
    private const COMMON_VALUES = [
        'flex' => 0,
        'block' => 1,
        'inline' => 2,
        'inline-block' => 3,
        'grid' => 4,
        'none' => 5,
        'row' => 6,
        'column' => 7,
        'row-reverse' => 8,
        'column-reverse' => 9,
        'flex-start' => 10,
        'flex-end' => 11,
        'center' => 12,
        'space-between' => 13,
        'space-around' => 14,
        'space-evenly' => 15,
        'stretch' => 16,
        'baseline' => 17,
        'wrap' => 18,
        'nowrap' => 19,
        'absolute' => 20,
        'relative' => 21,
        'fixed' => 22,
        'sticky' => 23,
        'auto' => 24,
        '0' => 25,
        '0px' => 26,
        '100%' => 27,
        '1' => 28,
        'pointer' => 29,
        'inherit' => 30,
        'transparent' => 31,
        '#ffffff' => 32,
        '#000000' => 33,
        'cover' => 34,
        'contain' => 35,
        'fill' => 36,
        'underline' => 37,
        'bold' => 38,
        'normal' => 39,
    ];

    /**
     * Breakpoint media queries.
     */
    private const BREAKPOINTS = [
        'sm' => '(min-width: 640px)',
        'md' => '(min-width: 768px)',
        'lg' => '(min-width: 1024px)',
        'xl' => '(min-width: 1280px)',
        '2xl' => '(min-width: 1536px)',
    ];

    /**
     * Color scheme media queries.
     */
    private const COLOR_SCHEMES = [
        'dark' => '(prefers-color-scheme: dark)',
        'light' => '(prefers-color-scheme: light)',
    ];

    /**
     * Pseudo-class/element selectors.
     */
    private const PSEUDOS = [
        'hover' => ':hover',
        'active' => ':active',
        'focus' => ':focus',
        'focus-visible' => ':focus-visible',
        'focus-within' => ':focus-within',
        'visited' => ':visited',
        'disabled' => ':disabled',
        'enabled' => ':enabled',
        'checked' => ':checked',
        'required' => ':required',
        'valid' => ':valid',
        'invalid' => ':invalid',
        'placeholder' => '::placeholder',
        'first' => ':first-child',
        'last' => ':last-child',
        'only' => ':only-child',
        'odd' => ':nth-child(odd)',
        'even' => ':nth-child(even)',
        'empty' => ':empty',
    ];

    /**
     * Add a style rule.
     *
     * @param string $class The CSS class name
     * @param array<string, string> $properties CSS properties as key-value pairs
     */
    public function add(string $class, array $properties): static
    {
        $this->styles[$class] = $properties;
        return $this;
    }

    /**
     * Add multiple style rules.
     *
     * @param array<string, array<string, string>> $styles
     */
    public function addAll(array $styles): static
    {
        foreach ($styles as $class => $properties) {
            $this->add($class, $properties);
        }
        return $this;
    }

    /**
     * Generate CSS string output with proper media queries and pseudo selectors.
     */
    public function toCSS(): string
    {
        $baseStyles = [];
        $mediaStyles = [];

        foreach ($this->styles as $className => $properties) {
            $parsed = $this->parseClassName($className);
            $selector = '.' . $this->escapeClassName($className) . $parsed['pseudoSelector'];
            $rule = $this->buildRule($selector, $properties);

            if ($parsed['mediaQuery']) {
                $mediaStyles[$parsed['mediaQuery']][] = $rule;
            } else {
                $baseStyles[] = $rule;
            }
        }

        $css = implode("\n", $baseStyles);

        foreach ($mediaStyles as $mediaQuery => $rules) {
            $css .= "\n{$mediaQuery} {\n";
            foreach ($rules as $rule) {
                $css .= '  ' . str_replace("\n", "\n  ", trim($rule)) . "\n";
            }
            $css .= "}\n";
        }

        return $css;
    }

    /**
     * Generate minified CSS string output.
     */
    public function toMinifiedCSS(): string
    {
        $baseStyles = [];
        $mediaStyles = [];

        foreach ($this->styles as $className => $properties) {
            $parsed = $this->parseClassName($className);
            $selector = '.' . $this->escapeClassName($className) . $parsed['pseudoSelector'];
            $rule = $this->buildMinifiedRule($selector, $properties);

            if ($parsed['mediaQuery']) {
                $mediaStyles[$parsed['mediaQuery']][] = $rule;
            } else {
                $baseStyles[] = $rule;
            }
        }

        $css = implode('', $baseStyles);

        foreach ($mediaStyles as $mediaQuery => $rules) {
            $css .= $mediaQuery . '{' . implode('', $rules) . '}';
        }

        return $css;
    }

    /**
     * Parse class name to extract breakpoint, color scheme, and pseudos.
     *
     * @return array{mediaQuery: ?string, pseudoSelector: string, baseClass: string}
     */
    private function parseClassName(string $className): array
    {
        $parts = explode(':', $className);
        $baseClass = array_pop($parts);

        $mediaConditions = [];
        $pseudoSelector = '';

        foreach ($parts as $part) {
            if (isset(self::BREAKPOINTS[$part])) {
                $mediaConditions[] = self::BREAKPOINTS[$part];
            } elseif (isset(self::COLOR_SCHEMES[$part])) {
                $mediaConditions[] = self::COLOR_SCHEMES[$part];
            } elseif (isset(self::PSEUDOS[$part])) {
                $pseudoSelector .= self::PSEUDOS[$part];
            }
        }

        $mediaQuery = null;
        if (!empty($mediaConditions)) {
            $mediaQuery = '@media ' . implode(' and ', $mediaConditions);
        }

        return [
            'mediaQuery' => $mediaQuery,
            'pseudoSelector' => $pseudoSelector,
            'baseClass' => $baseClass,
        ];
    }

    /**
     * Build a CSS rule block.
     */
    private function buildRule(string $selector, array $properties): string
    {
        $css = $selector . " {\n";
        foreach ($properties as $prop => $value) {
            $css .= '  ' . $prop . ': ' . $value . ";\n";
        }
        $css .= "}\n";
        return $css;
    }

    /**
     * Build a minified CSS rule.
     */
    private function buildMinifiedRule(string $selector, array $properties): string
    {
        $props = [];
        foreach ($properties as $prop => $value) {
            $props[] = $prop . ':' . $value;
        }
        return $selector . '{' . implode(';', $props) . '}';
    }

    /**
     * Generate a style tag with CSS.
     */
    public function toStyleTag(string $id = 'spwa-styles'): string
    {
        $idAttr = $id ? " id=\"{$id}\"" : '';
        return "<style{$idAttr}>\n" . $this->toCSS() . "</style>";
    }

    /**
     * Get styles as array.
     *
     * @return array<string, array<string, string>>
     */
    public function toArray(): array
    {
        return $this->styles;
    }

    /**
     * Generate compressed styles for frontend transmission.
     * Uses indices for common properties and values.
     *
     * Format: { className: { prop|index: value|index, ... }, ... }
     */
    public function toCompressed(): array
    {
        $compressed = [];
        foreach ($this->styles as $className => $properties) {
            $compressedProps = [];
            foreach ($properties as $prop => $value) {
                // Use index for common properties, string for uncommon
                $propKey = self::COMMON_PROPERTIES[$prop] ?? $prop;
                // Use index for common values, string for uncommon
                $valueKey = self::COMMON_VALUES[$value] ?? $value;
                $compressedProps[$propKey] = $valueKey;
            }
            $compressed[$className] = $compressedProps;
        }
        return $compressed;
    }

    /**
     * Generate compressed JSON for frontend.
     */
    public function toCompressedJSON(): string
    {
        return json_encode($this->toCompressed());
    }

    /**
     * Generate the JS runtime that includes the dictionary and decompression logic.
     */
    public function toJSRuntime(): string
    {
        $props = json_encode(array_flip(self::COMMON_PROPERTIES));
        $values = json_encode(array_flip(self::COMMON_VALUES));
        $breakpoints = json_encode(self::BREAKPOINTS);
        $colorSchemes = json_encode(self::COLOR_SCHEMES);
        $pseudos = json_encode(self::PSEUDOS);
        $compressed = $this->toCompressedJSON();

        return <<<JS
(function() {
  var P = {$props};
  var V = {$values};
  var B = {$breakpoints};
  var C = {$colorSchemes};
  var X = {$pseudos};
  var S = {$compressed};

  function parseClass(cls) {
    var parts = cls.split(':');
    var base = parts.pop();
    var media = [];
    var pseudo = '';
    for (var i = 0; i < parts.length; i++) {
      var p = parts[i];
      if (B[p]) media.push(B[p]);
      else if (C[p]) media.push(C[p]);
      else if (X[p]) pseudo += X[p];
    }
    return {
      media: media.length ? '@media ' + media.join(' and ') : null,
      pseudo: pseudo,
      base: base
    };
  }

  function decode(styles) {
    var base = [];
    var mediaMap = {};
    for (var cls in styles) {
      var parsed = parseClass(cls);
      var selector = '.' + cls.replace(/([.:\[\]\/])/g, '\\\\$1') + parsed.pseudo;
      var props = styles[cls];
      var rules = [];
      for (var p in props) {
        var prop = typeof p === 'number' || /^\d+$/.test(p) ? P[p] : p;
        var val = props[p];
        var isIndex = typeof val === 'number' || /^\d+$/.test(val);
        var value = isIndex && V[val] !== undefined ? V[val] : val;
        rules.push(prop + ':' + value);
      }
      var rule = selector + '{' + rules.join(';') + '}';
      if (parsed.media) {
        if (!mediaMap[parsed.media]) mediaMap[parsed.media] = [];
        mediaMap[parsed.media].push(rule);
      } else {
        base.push(rule);
      }
    }
    var css = base.join('');
    for (var mq in mediaMap) {
      css += mq + '{' + mediaMap[mq].join('') + '}';
    }
    return css;
  }

  var style = document.createElement('style');
  style.id = 'spwa-styles';
  style.textContent = decode(S);
  document.head.appendChild(style);
})();
JS;
    }

    /**
     * Generate script tag with JS runtime.
     */
    public function toScriptTag(): string
    {
        return "<script>\n" . $this->toJSRuntime() . "\n</script>";
    }

    /**
     * Get the property dictionary for external use.
     */
    public static function getPropertyDictionary(): array
    {
        return self::COMMON_PROPERTIES;
    }

    /**
     * Get the value dictionary for external use.
     */
    public static function getValueDictionary(): array
    {
        return self::COMMON_VALUES;
    }

    /**
     * Escape class name for CSS selector.
     */
    private function escapeClassName(string $class): string
    {
        // Escape special CSS characters: . : [ ] /
        return preg_replace('/([.:\[\]\/])/', '\\\\$1', $class);
    }

    /**
     * Create from collected styles array.
     *
     * @param array<string, array<string, string>> $styles
     */
    public static function from(array $styles): static
    {
        $generator = new static();
        $generator->addAll($styles);
        return $generator;
    }
}
