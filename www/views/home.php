<div>
    <div>
        <span>

            The current count is $model->counter
        </span>
        <button @click="$model->inc(1)">Increase</button>
        <button @click="$model->inc(-1)">Decrease</button>
        <button @click="$model->reset()">Reset</button>
        <span class="ref" @if="$model->counter >= 10">
            Value is greater or equal than 10!
        </span>
    </div>
    <input type="text" @bound="$model->text" @keydown.enter="$model->addItem()"/>
    <ul>
        <li class="bg-orange-600" @for="$model->items as $index => $item" @click="$model->delete($item, $index)">
            $item
        </li>
    </ul>
    <button @click="$model->addItem()">Add</button>
</div>


