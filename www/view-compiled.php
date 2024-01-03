<?php
/**
 * @var Model $model
 */

$template = node("html", ["lang" => "en"], [
    node("body", null, [
        node("div", null, [
            node("div", null, [
                node("span", null, [
                    "The current count is ",
                    bind($model->counter)
                ]),
                conditional($model->counter > 10, node("span", ["class" => "ref"], null), null),
                node("ul", null, [
                    multiple($model->items, fn($item) => node("li", null, [
                        bind($item)
                    ]))
                ])
            ])
        ])
    ])
]);

?>
