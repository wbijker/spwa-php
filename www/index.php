<?php

namespace App;

use Spwa\Js\Console;
use Spwa\Js\JsRuntime;
use Spwa\State\SessionStateManager;
use Spwa\UI\Examples\Showcase;
use Spwa\UI\StyleGenerator;
use Spwa\VNode\Patcher;

require 'vendor/autoload.php';
// Build and render the UI Showcase
$state = new SessionStateManager();
$showcase = new Showcase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = json_decode(file_get_contents('php://input'), true);
    $event = $payload['event'] ?? '';
    $pathStr = $payload['path'] ?? '';
    $path = array_map('intval', explode(',', $pathStr));

    // 1. Render the old component tree (before event)
    $oldShowcase = new Showcase();
    $oldUi = $oldShowcase->render($state);

    // 2. Find the node by path and execute the event
    // executeEvent will finalize the owning component automatically
    $node = $oldUi->findByPath($path);
    if ($node !== null) {
        $node->executeEvent($event, $state);
    }

    // 3. Render the new component tree (after event, with updated state)
    $newShowcase = new Showcase();
    $newUi = $newShowcase->render($state);

    // 4. Compare new DOM vs old DOM to generate patches
    $patcher = new Patcher();
    $newUi->compare($oldUi, $patcher);

    // 5. Return patches to frontend
    echo json_encode([
        "success" => true,
        "js" => JsRuntime::dump(),
        "patches" => $patcher->getOperations(),
        "state" => $state->getAll()
    ]);
    die();
}

$ui = $showcase->render($state);
$showcase->finalize($state);
$html = $ui->toHtml();

// Generate compressed styles with JS runtime
$generator = StyleGenerator::from($ui->collectStyles());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPWA UI Showcase</title>
    <?= $generator->toScriptTag() ?>
    <script>

        function resolveObject(path) {
            const last = path.pop();
            const resolved = path.reduce((acc, cur) => acc[cur], window);
            return [resolved, last];
        }


        function executeJsDump(dump) {
            for (const [mode, path, args] of dump) {
                const [obj, bind] = resolveObject(path);
                if (mode === 'invoke')
                    obj[bind](...args);
                else if (mode === 'assign')
                    obj[bind] = args;
            }
        }


        function findNodeByPath(path) {
            // Start from body's first child (the root element)
            let node = document.body.firstElementChild;
            for (const index of path) {
                if (!node) return null;
                node = node.children[index];
            }
            return node;
        }

        function applyPatches(patches) {
            for (const patch of patches) {
                const path = patch.path;

                switch (patch.type) {
                    case 'replace_node': {
                        const node = findNodeByPath(path);
                        if (node) {
                            const temp = document.createElement('div');
                            temp.innerHTML = patch.html;
                            node.replaceWith(temp.firstElementChild);
                        }
                        break;
                    }
                    case 'insert_node': {
                        const parentPath = path.slice(0, -1);
                        const index = path[path.length - 1];
                        const parent = parentPath.length === 0
                            ? document.body.firstElementChild
                            : findNodeByPath(parentPath);
                        if (parent) {
                            const temp = document.createElement('div');
                            temp.innerHTML = patch.html;
                            const newNode = temp.firstElementChild;
                            if (index >= parent.children.length) {
                                parent.appendChild(newNode);
                            } else {
                                parent.insertBefore(newNode, parent.children[index]);
                            }
                        }
                        break;
                    }
                    case 'delete_node': {
                        const node = findNodeByPath(path);
                        if (node) {
                            node.remove();
                        }
                        break;
                    }
                    case 'set_attribute': {
                        const node = findNodeByPath(path);
                        if (node) {
                            node.setAttribute(patch.name, patch.value);
                        }
                        break;
                    }
                    case 'remove_attribute': {
                        const node = findNodeByPath(path);
                        if (node) {
                            node.removeAttribute(patch.name);
                        }
                        break;
                    }
                }
            }
        }

        function callback(error, data) {
            if (error) {
                console.error("Error:", error);
                return;
            }

            // Execute JS commands from server
            if (data.js) {
                executeJsDump(data.js);
            }

            // Apply DOM patches
            if (data.patches && data.patches.length > 0) {
                console.log("Applying patches:", data.patches);
                applyPatches(data.patches);
            }

            console.log("Response:", data);
        }

        function post(data, headers) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", window.location.href, true);
            xhr.setRequestHeader("Content-Type", "application/json");

            // Set custom headers
            for (var key in headers ?? {}) {
                if (headers.hasOwnProperty(key)) {
                    xhr.setRequestHeader(key, headers[key]);
                }
            }

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


        function handleEvent(event, path) {
            console.log('Event:', event, 'Path:', path);
            post({ event, path });
        }


    </script>
</head>
<body style="margin: 0; font-family: system-ui, -apple-system, sans-serif;">
<?= $html ?>
</body>
</html>
    <!--<script src="https://cdn.tailwindcss.com"></script>-->
<?php
//
//$el->render();

//class ProductAgg
//{
//    public function __construct(
//        public IntColumn    $categoryId,
//        public IntColumn    $count,
//        public IntColumn    $row,
//        public StringColumn $name
//    )
//    {
//    }
//
//    public function toFlat(): ProductAggFlat
//    {
//        return new ProductAggFlat(
//            categoryId: $this->categoryId->value,
//            count: $this->count->value,
//            row: $this->row->value,
//            name: $this->name->value
//        );
//    }
//}
//
//class ProductAggFlat
//{
//    public function __construct(
//        public int    $categoryId,
//        public int    $count,
//        public int    $row,
//        public string $name
//    )
//    {
//    }
//
//}
//
///*SELECT
//    t0.categoryId AS categoryId,
//    t0.count * 3 AS count,
//    t1.name AS name
//FROM
//(
//    SELECT
//            t0.category_id + 100 AS categoryId,
//            count(t0.id) * 2 AS count,
//            t0.name AS name
//        FROM
//            `product` t0
//        WHERE
//            t0.price > 0
//        GROUP BY
//            t0.category_id
//    ) t0
//    INNER JOIN `category` t1 ON t0.categoryId = t1.id*/
//
//// create database File containing all associated tables
//// migrations dumping an diffing the database schema
//// generate file containing the above;
//
//// SqlDriver + sql generator;
//
//// scoped create all table sources
//
//// Select category_id, max(id) from product groupby category_id order by id desc
//// join back to products to get name
//
//
///*
//The from and join clauses is the only place where a new source can be introduced.
//
//$source = table | query | constant | function
//
//Query::from($source);
//->join($source);
//
//
//Scoped is just the grouping of sources for a query.
//*/
//
//$q = Database::scoped(fn(Product $p, Category $c, Query $q) => $q
//    ->from($p)
//    ->innerJoin($c, $c->id->equals($p->category_id))
//    ->select(new ProductAgg(
//        categoryId: $p->category_id->add(100),
//        count: $p->id->count()->multiply(2),
//        row: WindowFunction::rowNumber($p->id),
//        name: StringColumn::case()
//            ->when($p->category_id->equals(0), $c->name)
//            ->when($p->category_id->equals(1), "Other")
//            ->end()
//    ))
//    ->select(fn(ProductAgg $a) => new ProductAgg(
//        categoryId: $a->categoryId,
//        count: $a->count->multiply(3),
//        row: $a->row,
//        name: $a->name
//    ))
//);
//
//echo $q->toSql();


//Query::scoped(fn(Product $p) => Query::from($p)
//    ->groupBy($p->category_id)
//    ->orderByDesc($p->id)
//    ->select(new AA(
//            id: $p->id->max(),
//            categoryId: $p->category_id,
//        )
//    ))
//    ->scoped(fn(AA $a, Product $p) => Query::from($p)
//        ->innerJoin($a, $a->id->equals($p->id))
//        // laterJoin / crossLateralJoin
//        ->select(new ProductSelector(
//            id: $p->id,
//            categoryId: $p->category_id,
//            name: $p->name
//        ))
//    );
//
//// -> innerJoin(Newtable::class, fn(Newtable $n, Product $p) => $n->id->equals($p->id))
//// -> lateralJoin(fn(Sources $s) => ...);
//
//$q = Query::scoped(fn(Product $p, Category $c) => Query::from($p)
//    ->innerJoin($c, $c->id->equals($p->category_id))
//    ->where($p->price->greaterThan(10))
//    ->orderByDesc($p->price)
//    ->orderBy($p->category_id)
//    ->select(new ProductAgg(
//        categoryId: $p->category_id->add(100),
//        count: $p->id->count()->multiply(2),
//        row: WindowFunction::rowNumber($p->id),
//        name: StringColumn::case()
//            ->when($p->category_id->equals(0), $c->name)
//            ->when($p->category_id->equals(1), "Other")
//            ->end()
//    ))
//    ->scoped(fn(ProductAgg $p, Product $p) =>
//        )
//);
//

//App::run([
//    new SpwMiddleware(fn() => new WelcomePage()),
//]);


