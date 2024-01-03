<?php

require_once 'view.php';

class Model
{
    public int $counter = 12;
    public array $items = ['Coffee'];

    /**
     * @param int $counter
     * @param array|string[] $items
     */
    public function __construct(int $counter, array $items)
    {
        $this->counter = $counter;
        $this->items = $items;
    }


}


require_once 'view-compiled.php';

$prev = View::render(new Model(8, ['Tea', 'Water', 'Coffee', 'Milk']));
$next = View::render(new Model(12, ['Hot chocolate', 'Coffee', 'Milk', 'Tea']));

$prev->fillPath(null, 0);
$next->fillPath(null, 0);//
//compare($prev, $next);

$prev->render();
