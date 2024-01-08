<div>
    <div>
        <span>

            The current count is $model->counter
        </span>
        <button click="$model->inc(1)">Increase</button>
        <button click="$model->inc(-1)">Decrease</button>
        <span class="ref" if="$model->counter >= 10">
            Value is greater or equal than 10!
        </span>
    </div>
    <input type="text" bound="text"/>
    <ul>
        <li class="bg-orange-600" for="$model->items as $index => $item" click="$model->delete($item, $index)">
            $item
        </li>
    </ul>
    <button click="$model->items[] = $model->text">Add</button>
</div>
