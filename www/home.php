<?php

class HomePage extends Page
{
    public int $index = 0;
    public string $text = "initial...";
    public int $counter = 12;
    public array $items = ['Coffee', 'Tea', 'Milk'];

    public function inc(int $amount = 1)
    {
        $this->counter += $amount;
    }

    const COLORS = [
        'bg-blue',
        'bg-red',
        'bg-green',
        'bg-yellow',
        'bg-orange',
        'bg-pink',
        'bg-purple',
        'bg-indigo',
        'bg-gray',
        'bg-black',
    ];

    function getColor(): string
    {
        $abs = abs($this->counter);
        $index = floor($abs / 10) % 10;
        $segment = $abs % 10;

        $color = self::COLORS[$index] . '-' . (($segment+1) * 100);
        return $color;
    }

    public function addItem()
    {
        $this->items[] = $this->text;
        $this->text = "";
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

    public function render(): Template
    {
        return $this->view('home');
    }

}
