<?php

function className($name)
{
    return ucfirst($name) . "View";
}


abstract class Page
{
    abstract function render();

    public function save()
    {
        $_SESSION['page'] = json_encode($this);
    }

    public function restore()
    {
        if (isset($_SESSION['page'])) {
            $savedPage = json_decode($_SESSION['page'], true);
            if (is_array($savedPage)) {
                foreach ($savedPage as $property => $value) {
                    if (property_exists($this, $property)) {
                        $this->$property = $value;
                    }
                }
            }
        }
    }

    function view($name): HtmlNode
    {
        // execute view
        ob_start();
        require 'views/'.$name.'.php';
        $html = ob_get_clean();

        $dom = new DOMDocument();
        @$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $template = buildTree($dom->documentElement, 2);
        $className = className($name);
        $content = <<<EOD
    <?php
    class $className
    {
        static function template(\$model): HtmlNode
        {
            return $template;
        }
    }
    EOD;

        $compiled = 'views/'.$name.'.compiled.php';
        file_put_contents($compiled, $content);

        require_once $compiled;
        return $className::template($this);
    }


}


function renderPage($page)
{
    $page->restore();
    $prev = $page->render();
    $prev->fillPath(null, 0);

    // GET or POST
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'POST') {
        // read JSON body
        $json = json_decode(file_get_contents('php://input'), true);

        if (is_array($json['params'])) {
            // Extract the method name and parameters from the $json['params'] array.
            $methodName = $json['params'][0];
            $methodArgs = array_slice($json['params'], 1);

            // Check if the method exists in the $page object.
            if (method_exists($page, $methodName)) {
                // Dynamically call the method with the provided arguments.
                call_user_func_array([$page, $methodName], $methodArgs);
            } else {
                // Handle the case where the method doesn't exist.
                // This might involve logging an error, throwing an exception, etc.
                // For example:
                throw new Exception("Method $methodName does not exist on the page object.");
            }
        }

        $next = $page->render();
        $next->fillPath(null, 0);
        $patches = [];
        compare($prev, $next, $patches);
        // persist state
        $page->save();
        echo json_encode($patches);
        return;
    }

    // add JS runtime
    // later CSS runtime?
    $prev->render();
    ?>

    <script>
        function createNode(value) {
            if (typeof value == 'string')
                return document.createTextNode(value);

            const node = document.createElement(value.tag);
            for (const child of value.children) {
                node.appendChild(createNode(child));
            }
            return node;
        }

        function transversePaths(arr) {
            let it = document.body;
            for (const path of arr) {
                it = it.childNodes[path];
            }
            return it;
        }

        function applyPatch(patch) {
            // update text
            if (patch.type === 0) {
                const textNode = transversePaths(patch.path);
                textNode.nodeValue = patch.value;
            }

            // delete node
            if (patch.type === 2) {
                const node = transversePaths(patch.path);
                node.parentNode.removeChild(node);
            }

            // insert node
            if (patch.type === 3) {
                const node = transversePaths(patch.path);
                const newNode = createNode(patch.value);
                node.insertBefore(newNode, node.childNodes[patch.index]);
            }

        }

        function serializeEvent(e) {
            if (e instanceof PointerEvent) {
                return {
                    type: e.type,
                    timeStamp: e.timeStamp,
                    clientX: e.clientX,
                    clientY: e.clientY,
                    button: e.button,
                    buttons: e.buttons,
                    pointerId: e.pointerId,
                    pointerType: e.pointerType,
                    isPrimary: e.isPrimary,
                    // ... other PointerEvent properties
                };
            }

            if (e instanceof KeyboardEvent) {
                return {
                    type: e.type,
                    timeStamp: e.timeStamp,
                    key: e.key,
                    code: e.code,
                    location: e.location,
                    ctrlKey: e.ctrlKey,
                    shiftKey: e.shiftKey,
                    altKey: e.altKey,
                    metaKey: e.metaKey,
                    repeat: e.repeat,
                    // ... other KeyboardEvent properties
                };
            }
            if (e instanceof MouseEvent) {
                return {
                    type: e.type,
                    timeStamp: e.timeStamp,
                    clientX: e.clientX,
                    clientY: e.clientY,
                    button: e.button,
                    buttons: e.buttons,
                    // ... other MouseEvent properties
                };
            }
        }

        function eventHandler(e, params) {
            postData('/', {params, event: serializeEvent(e)}, (err, data) => {
                for (const patch of data) {
                    applyPatch(patch);
                }
            });


        }

        function postData(url, data, callback) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", url, true);
            xhr.setRequestHeader("Content-Type", "application/json");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) { // 4 means request is done
                    if (xhr.status === 200) { // 200 means "OK"
                        callback(null, JSON.parse(xhr.responseText));
                    } else {
                        callback(new Error("Request failed: " + xhr.status));
                    }
                }
            };

            xhr.send(JSON.stringify(data));
        }


    </script>


    <?php

}
