<?php

namespace Spwa\Web;

// The base interface for enabling smooth HTML building in PHP by
// removing the need for arrays and opening and closing parentheses
// The main idea is to conform as close as HTML syntax as possible.
// Signature for building HTML is simply a variadic parameter of type HtmlBuildable:
// function html(HtmlBuildable ...$buildable): void;
// Which mean text, attributes and tags can be combined in any order and hierarchy will be preserved with a simple syntax

/*
div(_class("flex w-screen h-screen",
     div(_class("m-auto p-10 border rounded-md"),
         text("Html Builder")
     )
);

which will render:

<div class="flex w-screen h-screen">
    <div class="m-auto p-10 border rounded-md">
        Html Builder
    </div>
</div>

*/

interface HtmlBuildable
{
    public function execute(HtmlTag $tag): void;
}