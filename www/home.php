<?php

class HomePage extends Page
{
    public string $newItem;
    public int $counter = 12;
    public array $items = ['Coffee'];

    public function buttonClick()
    {
        $this->counter++;
    }

    public function render(): HtmlNode
    {
        return $this->view('home');
    }

}
