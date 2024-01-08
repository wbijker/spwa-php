<?php

class HomePage extends Page
{
    public string $text = "initial...";
    public int $counter = 12;
    public array $items = ['Coffee', 'Tea', 'Milk'];

    public function inc(int $amount = 1)
    {
        $this->counter += $amount;
        JS::log("Counter is now $this->counter");
    }

    public function reset()
    {
        $this->counter = 0;
        $this->items = ['Coffee', 'Tea', 'Milk', 'Water', 'Hot Chocolate', 'Iced Coffee'];
    }

    public function delete(string $item, int $index)
    {
        unset($this->items[$index]);
        JS::log("Deleting $item at $index");
    }

    public function render(): HtmlTemplateNode
    {
        return $this->view('home');
    }

}
