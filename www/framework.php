<?php

function className($name)
{
    return ucfirst($name) . "View";
}


abstract class Page
{
    abstract function render(): HtmlNode;

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

    function compileView(string $viewPath, string $name, string $className)
    {
        $start = microtime(true);
        // execute view
        ob_start();
        require $viewPath;
        $html = ob_get_clean();

        $dom = new DOMDocument();
        @$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $template = buildTree($dom->documentElement, 2, true);
        $endTime = microtime(true);

        $date = date('Y-m-d H:i:s');
        $duration = ($endTime - $start) * 1000;
        $typeClass = get_class($this);

        $content = <<<EOD
    <?php
    /**
     * This file was automatically generated by SPWA template compiler
     * Generated: $date
     * Input: $viewPath
     * Duration: $duration ms
    */
    
    class $className
    {
        static function template($typeClass \$model): HtmlNode
        {
            return $template;
        }
    }
    EOD;

        $compiledPath = 'views/' . $name . '.compiled.php';
        file_put_contents($compiledPath, $content);
    }

    function view($name): HtmlNode
    {
        $viewPath = 'views/' . $name . '.php';
        $compiledPath = 'views/' . $name . '.compiled.php';
        $className = className($name);

        if (file_exists($viewPath) === false) {
            throw new Exception("View $viewPath does not exist");
        }

        // only generate template again if input file is newer than compiled file
        if (!file_exists($compiledPath) || filemtime($viewPath) > filemtime($compiledPath)) {
            $this->compileView($viewPath, $name, $className);
        }

        require_once $compiledPath;
        return $className::template($this);
    }
}

function transverse(HtmlNode $node, array $path): HtmlNode
{
    $it = $node;
    foreach ($path as $index) {
        $it = $it->children[$index];
    }
    return $it;
}

function renderPage(Page $page)
{
    $page->restore();
    $prev = $page->render();
    $prev->fillPath(null, 0);

    // GET or POST
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'POST') {
        // read JSON body
        $json = json_decode(file_get_contents('php://input'), true);

        // transverse old structure to find path
        $node = transverse($prev, $json['path']);

        // fill inputs
        foreach ($json['inputs'] as $name => $value) {
            $page->$name = $value;
        }

        $event = $node->attributes['click'];

        if (is_callable($event)) {
            call_user_func($event);
        } else {
            // Handle the case where the method doesn't exist.
            // This might involve logging an error, throwing an exception, etc.
            // throw new Exception("Method $methodName does not exist on the page object.");
        }

        $next = $page->render();
        $next->fillPath(null, 0);
        $patches = [];
        compare($prev, $next, $patches);
        // persist state
        $page->save();
        echo json_encode(['patches' => $patches, 'js' => JsRuntime::$pendingCalls]);
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
            // root node is []
            let it = document.body.childNodes[0];

            for (const path of arr) {
                it = it.childNodes[path];
            }
            return it;
        }

        function getJsFunction(path) {
            let it = window;
            for (const name of path) {
                it = it[name];
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

        const inputs = {};

        function handleInput(e, name) {
            inputs[name] = {
                change: true,
                value: e.target.value
            }
        }

        function eventHandler(e, path) {
            const changes = {};
            for (const name in inputs) {
                if (inputs[name].change) {
                    changes[name] = inputs[name].value
                }
            }
            postData('/', {path, event: serializeEvent(e), inputs: changes}, (err, data) => {
                for (const patch of data.patches) {
                    applyPatch(patch);
                }

                // execute JS calls
                for (const call of data.js) {
                    const path = call[0];
                    const args = call[1];
                    const fn = getJsFunction(path);
                    fn.apply(null, args);
                }

                // mark all inputs as not changed
                for (const name in inputs) {
                    inputs[name].change = false;
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
