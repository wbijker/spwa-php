<?php

class View
{
    public static function render(HomePage $model)
    {
        return node("body", null, [
            node("div", null, [
                node("div", null, [
                    node("span", [
                        'onClick' => 'buttonClick',
                    ], [
                        text("The current count is " . $model->counter)]),
                    conditional($model->counter > 10, node("span", ["class" => "ref"], [
                        text("Value is greater than 10")]), null),
                    node("ul", null, [
                        multiple($model->items, fn($item) => node("li", null, [text($item)]), fn($item) => $item),

                    ])])])]);
    }
}


?>
