<?php
/** @var HomePage $model */
?>
<div>
    <div>
        <span onClick="buttonClick">
            The current count is <?= $model->counter ?>
        </span>
        <?php if ($model->counter > 10):?>
            <span class="ref">
                Value is greater than 10
            </span>
        <?php endif;?>
    </div>
    <ul>
        <?php foreach ($model->items as $item):?>
            <li>
                <?= $item ?>
            </li>
        <?php endforeach;?>
    </ul>
</div>
