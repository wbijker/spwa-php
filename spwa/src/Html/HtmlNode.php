<?php

namespace Spwa\Html;

abstract class HtmlNode
{
    abstract function render(): string;
}