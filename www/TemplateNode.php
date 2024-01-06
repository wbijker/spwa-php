<?php

abstract class TemplateNode
{

    // factory methods
    static function html(string $tag, ?array $attributes, ?array $children): HtmlTemplateNode
    {
        return new HtmlTemplateNode($tag, $attributes, $children);
    }

    static function text($value): TextTemplateNode
    {
        return new TextTemplateNode($value);
    }

    static function if($exp, $then, $else): IfTemplateNode
    {
        return new IfTemplateNode($exp, $then, $else);
    }

    static function for($array, $callback, $keyCallback = null): ForTemplateNode
    {
        return new ForTemplateNode($array, $callback, $keyCallback);
    }

    abstract function resolve(ResolvedNode $parent): void;
}