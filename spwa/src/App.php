<?php

namespace Spwa;

use Spwa\Js\JsRuntime;
use Spwa\Nodes\Component;
use Spwa\Nodes\PatchBuilder;
use Spwa\Nodes\PathInfo;
use Spwa\Nodes\StateManager;

class App
{
    static function render(Component $component): void
    {
        ob_start();
        session_start();
        $data = $_SESSION['state'] ?? null;

        $manager = new StateManager();
        $manager->unserialize($data);

        $component->initialize(null, new PathInfo(0, get_class($component)), $manager);
        $node = $component->getNode();

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            echo $node->renderHtml();
        } else {
            // http post, read JSON body
            $json = json_decode(file_get_contents('php://input'), true);

            $event = $json['event'];
            $inputs = $json["inputs"];

            // handle bindings
//            foreach ($inputs as $path => $value) {
//                $pathData = $state->get(new NodePath(json_decode($path)));
//                if ($pathData->binding)
//                    $pathData->binding->set($value);
//            }

            [$path, $event] = $event;
            // find event from frontend.
            // execute event that will likely change the dom
            $manager->triggerEvent(implode("|", $path), $event);

            // force a re-render
            $new = $component->render();
            $patch = new PatchBuilder();
            $node->compare($new, $patch);

            // and finally return the patches to the JS runtime
            echo json_encode([
                'p' => $patch->patches,
                'j' => JsRuntime::dump()
            ]);
        }

        $component->finalize($manager);
        $_SESSION['state'] = $manager->serialize();

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {


            ?>

            <script lang="js">
                <?php
                include 'runtime.js';
                ?>
            </script>
            <?php
        }
    }
}


