<?php

namespace App;

use Spwa\Js\JsRuntime;
use Spwa\Samples\TodoApp;
use Spwa\State\StateManagers;
use Spwa\UI\StyleGenerator;
use Spwa\VNode\Patcher;
use Spwa\VNode\RenderPhase;

require 'vendor/autoload.php';

StateManagers::init();

$state = StateManagers::$session;
$app = new TodoApp();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_start();

    $payload = json_decode(file_get_contents('php://input'), true);
    $event = $payload['event'] ?? '';
    $pathStr = $payload['path'] ?? '';
    $path = array_map('intval', explode(',', $pathStr));
    $value = $payload['value'] ?? null;

    // 1. Render the old component tree (before event) in Initial phase
    $oldApp = new TodoApp();
    $oldUi = $oldApp->render($state, null, RenderPhase::Initial);

    // 2. Find the node by path and execute the event
    $node = $oldUi->findByPath($path);
    if ($node !== null) {
        $node->executeEvent($event, $state, $value);
    }

    // 3. Finalize the root component to save any state changes from closures
    $oldApp->finalize($state);

    // 4. Render the new component tree (after event, with updated state) in Patch phase
    $newApp = new TodoApp();
    $newUi = $newApp->render($state, null, RenderPhase::Patch);

    // 5. Compare new DOM vs old DOM to generate patches
    $patcher = new Patcher();
    $newUi->compare($oldUi, $patcher);

    // 6. Collect styles from old and new, compute delta
    $oldStyles = $oldUi->collectStyles();
    $newStyles = $newUi->collectStyles();
    $deltaStyles = StyleGenerator::delta($oldStyles, $newStyles);
    $deltaGenerator = StyleGenerator::from($deltaStyles);

    // Capture any output (warnings, echoes, etc.) and send via console.log
    $output = ob_get_clean();
    if ($output !== '' && $output !== false) {
        JsRuntime::invoke(['console', 'log'], [$output]);
    }

    // 7. Build response
    $response = [
        "success" => true,
        "js" => JsRuntime::dump(),
        "patches" => $patcher->getOperations(),
        "styles" => $deltaGenerator->toRaw(),
    ];

    // Include client state from all managers that need it
    $clientState = StateManagers::getClientState();
    if ($clientState !== null) {
        $response["state"] = $clientState;
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$ui = $app->render($state, null, RenderPhase::Initial);
$app->finalize($state);
$html = $ui->toHtml();

// Generate compressed styles with JS runtime
$generator = StyleGenerator::from($ui->collectStyles());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TodoMVC - SPWA</title>
    <style id="spwa-styles"><?= $generator->toStyle() ?></style>
    <script src="spwa.js"></script>
<?php if ($stateJs = StateManagers::getClientJs()): ?>
    <script><?= $stateJs ?></script>
<?php endif; ?>
</head>
<body style="margin: 0; font-family: system-ui, -apple-system, sans-serif;">
<?= $html ?>
</body>
</html>

