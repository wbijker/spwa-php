<?php

class HomePage extends Page
{
    public string $newItem;
    public int $counter = 12;
    public array $items = ['Coffee'];

    public function inc(int $amount = 1)
    {
        $this->counter += $amount;
    }

    public function render(): HtmlNode
    {
        return $this->view('home');
    }

}
