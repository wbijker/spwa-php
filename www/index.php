<?php

use Spwa\Spwa;
use Spwa\UI\CssExtractor;
use Samples\News\NewsApp;

require 'vendor/autoload.php';

// /styles.css — generate the stylesheet by lex-scanning the News sample's
// PHP source. Every `->method(args)` triple is evaluated on a probe UIElement;
// the union of emitted utility classes is rendered through StyleGenerator.
if (($_SERVER['REQUEST_URI'] ?? '') === '/styles.css') {
    header('Content-Type: text/css; charset=utf-8');
    echo (new CssExtractor())->scan(__DIR__ . '/../samples/News');
    exit;
}

Spwa::run(NewsApp::class);
