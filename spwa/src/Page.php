<?php

namespace Spwa;

use Spwa\Template\Component;
use Spwa\Template\ElementNode;
use Spwa\Template\Node;
use Spwa\Template\StateHandler;
use function Spwa\Template\body;
use function Spwa\Template\content;
use function Spwa\Template\head;
use function Spwa\Template\html;
use function Spwa\Template\script;

abstract class Page extends Component
{
    abstract function body(): Node;

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
