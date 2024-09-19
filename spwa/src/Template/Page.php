<?php

namespace Spwa\Template;

abstract class Page extends Component
{

    abstract function body(): ElementNode;
    abstract function stateHandler(): StateHandler;


    /**
     * @return ElementNode[]
     */
    function head(): array
    {
        return [];
    }

    function view(): ElementNode
    {
        $content = file_get_contents(dirname(__FILE__) . "/spwa-runtime.js");
        $head = head();
        $head->addChild(
            script(content($content))
        );
        foreach ($this->head() as $node) {
            $head->addChild($node);
        }
        return html(
            $head,
            body($this->body())
        );
    }
}