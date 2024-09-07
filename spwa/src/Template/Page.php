<?php

namespace Spwa\Template;

abstract class Page extends Component
{
    public function __construct()
    {
        parent::__construct();
    }


    abstract function body(): ElementNode;

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
        $head->addChild(body($this->body()));
        return html($head);
    }
}