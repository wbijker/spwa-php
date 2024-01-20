<?php
/* @var $model HomePage */
$pre = $model->precalc(fn($model) => $model->counter = 99);
?>

<div class="bg-red-200">
    <script src="https://cdn.tailwindcss.com"></script>

    <div>
        <span @ignore
              class="m-4 p-2 border-2"
              @class="$model->counter > 5 ? 'text-red-600' : 'text-blue-600'">
            The current count is $model->counter
        </span>

        <div>
            <button @click="$pre">Reset to 99</button>
        </div>
        <button @click="$model->inc(1)">Increase</button>
        <button @click="$model->inc(-1)">Decrease</button>
        <button @click="$model->reset()">Reset</button>

        <span class="ref" @if="$model->counter >= 10">
            Value is greater or equal than 10!
        </span>
    </div>

    <input type="text" @bound="$model->text"/>
    <ul @class="$model->getColor()">
        <li @for="$model->items as $index => $item" @click="$model->delete($item, $index)">
            $item
        </li>
    </ul>
    <button @click="$model->addItem()">Add</button>

    <?php foreach (range(0, 10) as $index): ?>
        <div @click.pre="$model->index = <?= $index ?>">Tab <?= $index ?></div>
    <?php endforeach; ?>

    <div>Current selected tab: $model->index</div>
</div>

