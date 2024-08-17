<?php

namespace Spwa\Html;

abstract class BaseAttribute
{
    abstract function render(): string;
}