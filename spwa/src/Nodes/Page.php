<?php

namespace Spwa\Nodes;

use Spwa\Html\HtmlDocument;
use Spwa\Html\Meta;
use Spwa\Html\Title;

abstract class Page extends Component
{

    abstract function render(): HtmlDocument;


}