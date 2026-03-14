<?php

/**
 * SPWA Demo Site — Entry Point
 *
 * Demonstrates a complete server-powered web application.
 * No Node.js. No external libraries. Pure PHP.
 */

namespace Samples\Site;

use Spwa\Js\JsRuntime;
use Spwa\State\StateManagers;
use Spwa\UI\StyleGenerator;
use Spwa\VNode\Patcher;
use Spwa\VNode\RenderPhase;

require __DIR__ . '/../../www/vendor/autoload.php';

StateManagers::init();

$state = StateManagers::$session;
$app = new Pages\SiteApp();

// ─── Handle PATCH requests (client interactions) ───
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = json_decode(file_get_contents('php://input'), true);
    $event = $payload['event'] ?? '';
    $pathStr = $payload['path'] ?? '';
    $path = array_map('intval', explode(',', $pathStr));
    $value = $payload['value'] ?? null;

    $oldApp = new Pages\SiteApp();
    $oldUi = $oldApp->render($state, null, RenderPhase::Initial);

    $node = $oldUi->findByPath($path);
    if ($node !== null) {
        $node->executeEvent($event, $state, $value);
    }

    $oldApp->finalize($state);

    $newApp = new Pages\SiteApp();
    $newUi = $newApp->render($state, null, RenderPhase::Patch);

    $patcher = new Patcher();
    $newUi->compare($oldUi, $patcher);

    $oldStyles = $oldUi->collectStyles();
    $newStyles = $newUi->collectStyles();
    $deltaStyles = StyleGenerator::delta($oldStyles, $newStyles);
    $deltaGenerator = StyleGenerator::from($deltaStyles);

    $response = [
        "success" => true,
        "js" => JsRuntime::dump(),
        "patches" => $patcher->getOperations(),
        "styles" => $deltaGenerator->toRaw(),
    ];

    $clientState = StateManagers::getClientState();
    if ($clientState !== null) {
        $response["state"] = $clientState;
    }

    echo json_encode($response);
    die();
}

// ─── Initial full render ───
$ui = $app->render($state, null, RenderPhase::Initial);
$app->finalize($state);
$html = $ui->toHtml();

$generator = StyleGenerator::from($ui->collectStyles());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPWA — Server-Powered Web Applications</title>
    <style id="spwa-styles"><?= $generator->toStyle() ?></style>
    <script src="../../www/spwa.js"></script>
<?php if ($stateJs = StateManagers::getClientJs()): ?>
    <script><?= $stateJs ?></script>
<?php endif; ?>
</head>
<body style="margin: 0; font-family: system-ui, -apple-system, sans-serif;">
<?= $html ?>
</body>
</html>
