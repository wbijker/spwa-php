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

    function transversePaths(arr) {
        let it = document.body;
        for (const path of arr) {
            it = it.childNodes[path];
        }
        return it;
    }

    function applyPatch(patch) {
        console.log('Patch ', patch);

        if (patch.type === 0) {
            const textNode = transversePaths(patch.path);
            textNode.nodeValue = patch.value;
        }

        if (patch.type === 2) {
            const node = transversePaths(patch.path);
            node.parentNode.removeChild(node);
        }

    }

    const diff = <?php echo json_encode($list) ?>;

    const button = document.createElement('button');
    button.innerText = 'Update';
    button.addEventListener('click', function () {

        applyPatch(diff[0]);
        applyPatch(diff[1]);

        // for (const patch of diff) {
        //
        // }

    });
    document.body.appendChild(button);
</script>
