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


    public function __construct(private string|RoutePath $href, private string $text)
    {
    }


    private function navigate(): void
    {
        $url = $this->href instanceof RoutePath
            ? $this->href->toUrl()
            : $this->href;

        Router::navigate($url);
        // change the URL instruction
        History::pushState(null, "", $url);
    }

    function render(): Node
    {
        return new A(
            class: "underline mx-2 cursor-pointer",
            mouse: MouseEvents::click(fn() => $this->navigate()),
            href: $this->href,
            children: [
                new HtmlText($this->text)]
        );
    }
}