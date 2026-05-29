<?php

use Samples\News\NewsApp;
use Spwa\Spwa;

require 'vendor/autoload.php';

// Dev-only HMR endpoint. Long-polls for a source change and publishes the
// current fingerprint into Spwa\Hash; the client reloads when it reports
// changed. Config (incl. the dev gate) comes from the app.
Spwa::watch(NewsApp::class);
