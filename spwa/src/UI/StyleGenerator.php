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
     * Parse class name to extract breakpoint, color scheme, pseudos, and relational selectors.
     *
     * @return array{mediaQuery: ?string, pseudoSelector: string, baseClass: string}
     */
    private function parseClassName(string $className): array
    {
        $parts = $this->splitClassParts($className);
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
            } elseif (str_starts_with($part, 'has-[') && str_ends_with($part, ']')) {
                $inner = substr($part, 5, -1);
                $pseudoSelector .= ':has(' . $inner . ')';
            } elseif (str_starts_with($part, 'nth-[') && str_ends_with($part, ']')) {
                $inner = substr($part, 5, -1);
                $pseudoSelector .= ':nth-child(' . $inner . ')';
            } elseif (str_starts_with($part, 'not-[') && str_ends_with($part, ']')) {
                $inner = substr($part, 5, -1);
                $pseudoSelector .= ':not(' . $inner . ')';
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
     * Split class name on : while respecting [...] brackets.
     *
     * @return string[]
     */
    private function splitClassParts(string $className): array
    {
        $parts = [];
        $current = '';
        $depth = 0;
        $len = strlen($className);

        for ($i = 0; $i < $len; $i++) {
            $char = $className[$i];
            if ($char === '[') $depth++;
            elseif ($char === ']') $depth--;

            if ($char === ':' && $depth === 0) {
                if ($current !== '') $parts[] = $current;
                $current = '';
            } else {
                $current .= $char;
            }
        }
        if ($current !== '') $parts[] = $current;

        return $parts;
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
     * Generate CSS string for use inside a style tag.
     * Returns minified CSS suitable for embedding.
     */
    public function toStyle(): string
    {
        return $this->toMinifiedCSS();
    }

    /**
     * Escape class name for CSS selector.
     */
    private function escapeClassName(string $class): string
    {
        // Escape special CSS characters: . : [ ] / ( ) > , + ~
        return preg_replace('/([.:\[\]\/()>,+~])/', '\\\\$1', $class);
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
