<?php

namespace Spwa\Html;

use Spwa\Js\JsFunction;
use Spwa\Js\JsRuntime;
use Spwa\Nodes\HtmlNode;
use Spwa\Nodes\Node;
use Spwa\Nodes\PatchBuilder;
use Spwa\Nodes\PathInfo;
use Spwa\Nodes\StateManager;

class Body extends HtmlNode
{

    public function __construct(
        array $children
    )
    {
        $this->children = $children;
    }

    function tag(): string
    {
        return 'body';
    }


    function initialize(?Node $parent, PathInfo $current, StateManager $manager): void
    {
        parent::initialize($this, PathInfo::root(), $manager);
    }

    function initializeAndCompare(?Node $parent, PathInfo $current, StateManager $manager, Node $old, PatchBuilder $patch): void
    {
        parent::initializeAndCompare($this, PathInfo::root(), $manager, $old, $patch);
    }

    function renderHtml(): string
    {
        $script = new InlineScript(JsFunction::create("executeJsDump", JsRuntime::dump()), ignore: true);
        return parent::renderHtml(). PHP_EOL. $script->renderHtml();
    }


}