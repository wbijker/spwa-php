<?php

namespace Spwa\Dom;

class HtmlComment extends HtmlText
{
    function render(): string
    {
        return "<!--" . parent::render() . "-->";
    }

}