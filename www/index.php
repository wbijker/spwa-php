<?php

use Spwa\Spwa;
use Spwa\UI\CssExtractor;
use Samples\News\NewsApp;

require 'vendor/autoload.php';

// /style.css — preflight reset stitched together with the utility rules
// extracted by lex-scanning the News sample's PHP source. Preflight goes
// first so app rules can override its defaults. Versioned via ?h=<sourceHash>
// in the <link> tag; the browser keys its cache off the full URL so a fresh
// hash forces a refetch.
if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/style.css')) {
    header('Content-Type: text/css; charset=utf-8');
    header('Cache-Control: public, max-age=31536000, immutable');
    echo "/* ==========================================================================\n";
    echo "   PREFLIGHT — browser reset (from preflight.css)\n";
    echo "   ========================================================================== */\n\n";
    echo file_get_contents(__DIR__ . '/preflight.css');
    echo "\n/* ==========================================================================\n";
    echo "   APPLICATION — utility rules extracted from samples/News\n";
    echo "   ========================================================================== */\n\n";
    echo (new CssExtractor())->scan(__DIR__ . '/../samples/News');
    exit;
}

Spwa::run(NewsApp::class);
