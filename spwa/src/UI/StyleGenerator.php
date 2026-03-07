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
     * Generate CSS string output.
     */
    public function toCSS(): string
    {
        $css = '';
        foreach ($this->styles as $className => $properties) {
            $css .= '.' . $this->escapeClassName($className) . " {\n";
            foreach ($properties as $prop => $value) {
                $css .= '  ' . $prop . ': ' . $value . ";\n";
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
        $css = '';
        foreach ($this->styles as $className => $properties) {
            $css .= '.' . $this->escapeClassName($className) . '{';
            $props = [];
            foreach ($properties as $prop => $value) {
                $props[] = $prop . ':' . $value;
            }
            $css .= implode(';', $props) . '}';
        }
        return $css;
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
