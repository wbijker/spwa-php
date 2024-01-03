<?php

require_once 'view.php';

class Model
{
    public int $counter = 12;
    public array $items = ['Coffee'];
}


require_once 'view-compiled.php';

$view = View::render(new Model());
$view->render();
