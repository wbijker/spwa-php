<?php

require_once 'framework.php';
require_once 'view.php';
require_once 'home.php';

session_start();

renderPage(new HomePage());
