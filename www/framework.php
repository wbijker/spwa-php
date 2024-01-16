<?php

function className($name)
{
    return ucfirst($name) . "View";
}


abstract class Page
{
    abstract function render(): HtmlTemplateNode;

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

        $root = HtmlTokenizer::parseHtml($html);
        if (count($root->children) != 1)
            throw new Exception("View must have exactly one root element");

        $template = buildTree($root->children[0], 2, true);
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
        static function template($typeClass \$model): TemplateNode
        {
            return $template;
        }
    }
    EOD;

        $compiledPath = 'views/' . $name . '.compiled.php';
        file_put_contents($compiledPath, $content);
    }

    function view($name): HtmlTemplateNode
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

function traverse(ResolvedNode $node, array $path): ResolvedNode
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

    $template = $page->render();
    $resolved = new ResolvedNode(null, new RootData());
    $template->resolve($resolved);

    // GET or POST
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'POST') {
        // read JSON body
        $json = json_decode(file_get_contents('php://input'), true);
        // fill inputs
        foreach ($json['inputs'] as $name => $value) {
            $page->$name = $value;
        }

        // transverse old structure to find path
        $node = traverse($resolved, $json['path']);

        if ($node->data instanceof TagData) {
            $event = $node->data->attributes['@click'];
            if (is_callable($event)) {
                call_user_func($event);
            }
        }
        // Handle the case where the method doesn't exist.
        // This might involve logging an error, throwing an exception, etc.
        $next = $page->render();
        $patches = [];
        compare($template, $next, $patches);
        // persist state
        $page->save();
        echo json_encode(['patches' => $patches, 'js' => JsRuntime::$pendingCalls]);
        return;
    }

    $resolved->render();

    ?>

    <script src="runtime.js?v=1"></script>
    <?php
}
