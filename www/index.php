<?php

require_once 'view.php';

class Model
{
    public int $counter = 12;
    public array $items = ['Coffee'];

    /**
     * @param int $counter
     * @param array|string[] $items
     */
    public function __construct(int $counter, array $items)
    {
        $this->counter = $counter;
        $this->items = $items;
    }


}


require_once 'view-compiled.php';

$prev = View::render(new Model(8, ['Tea', 'Water', 'Coffee', 'Milk']));
$next = View::render(new Model(12, ['Hot chocolate', 'Coffee', 'Milk', 'Tea', 'Lemonade', 'Milk']));

$prev->fillPath(null, 0);
$next->fillPath(null, 0);
$list = [];
compare($prev, $next, $list);

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

    const diff = <?php echo json_encode($list) ?>;

    const button = document.createElement('button');
    button.innerText = 'Update';
    button.addEventListener('click', function () {
        for (const patch of diff) {
            applyPatch(patch);
        }
    });
    document.body.appendChild(button);
</script>
