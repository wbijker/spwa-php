<?php

namespace Spwa\Route;

use Spwa\Html\A;
use Spwa\Html\MouseEvents;
use Spwa\Js\History;
use Spwa\Nodes\Component;
use Spwa\Nodes\HtmlText;
use Spwa\Nodes\Node;

class RouteLink extends Component
{


    public function __construct(private string $url, private string $text)
    {
    }


    private function navigate(): void
    {
        Router::navigate($this->url);
        // change the URL instruction
        History::pushState(null, "", $this->url);
    }

    function render(): Node
    {
        return new A(
            class: "underline mx-2 cursor-pointer",
            mouse: MouseEvents::click(fn() => $this->navigate()),
            href: $this->url,
            children: [
                new HtmlText($this->text)]
        );
    }
}