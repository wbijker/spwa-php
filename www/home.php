<?php

require_once 'view-compiled.php';

class HomePage extends Page
{

    public int $counter = 12;
    public array $items = ['Coffee'];

    public function buttonClick()
    {
        $this->counter++;
    }

    public function render(): HtmlNode
    {
        return View::render($this);
    }

}
