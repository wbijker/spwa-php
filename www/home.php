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
        $this->items = ['1. Coffee', '2. Tea', '3. Milk', '4. Water', '5. Hot Chocolate', '6. Iced Coffee'];
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
