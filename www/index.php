<?php

use Spwa\Spwa;
use Spwa\UI\CssExtractor;
use Samples\News\NewsApp;

require 'vendor/autoload.php';

// /style.css — generate the stylesheet by lex-scanning the News sample's
// PHP source. Every `->method(args)` triple is evaluated on a probe UIElement;
// the union of emitted utility classes is rendered through StyleGenerator.
// Versioned via ?h=<sourceHash> in the <link> tag; the browser keys its cache
// off the full URL so a fresh hash forces a refetch.
if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/style.css')) {
    header('Content-Type: text/css; charset=utf-8');
    header('Cache-Control: public, max-age=31536000, immutable');
    echo (new CssExtractor())->scan(__DIR__ . '/../samples/News');
    exit;
}

Spwa::run(NewsApp::class);
