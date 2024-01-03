<?php

require_once 'view.php';

class Model
{
    public int $counter = 12;
    public array $items = ['Coffee'];
}


$model = new Model();
require_once 'view-compiled.php';
$template->render();