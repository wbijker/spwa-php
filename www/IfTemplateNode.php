<?php

class IfTemplateNode extends TemplateNode
{
    public bool $condition;
    public ?TemplateNode $then;
    public ?TemplateNode $else;

    /**
     * @param bool $condition
     * @param TemplateNode |null $then
     * @param TemplateNode |null $else
     */
    public function __construct(bool $condition, ?TemplateNode $then, ?TemplateNode $else)
    {
        $this->condition = $condition;
        $this->then = $then;
        $this->else = $else;
    }

    public function compare(IfTemplateNode $other, &$list): void
    {
//        if ($prev->condition != $next->condition) {
//
//            if ($next->condition) {
//                // remove $prev->else
//                // insert $next->then
////                echo "Need to replace next\n";
////                print_r($next->then);
//                return;
//            }
//            // remove $prev->then
//            // insert $next->else
////            echo "Need to replace else\n";
//            return;
//        }
//        return;
    }

    function resolve(ResolvedNode $parent): void
    {
    }
}