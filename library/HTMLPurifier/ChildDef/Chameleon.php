<?php

declare (strict_types=1);
/**
 * Definition that uses different definitions depending on context.
 *
 * The del and ins tags are notable because they allow different types of
 * elements depending on whether or not they're in a block or inline context.
 * Chameleon allows this behavior to happen by using two different
 * definitions depending on context.  While this somewhat generalized,
 * it is specifically intended for those two tags.
 */
class Html_Purifier_child_Def_chameleon extends Html_Purifier_child_Def
{
    /**
     * Instance of the definition object to use when inline. Usually stricter.
     * @type HTMLPurifier_ChildDef_Optional
     */
    public $inline;
    /**
     * Instance of the definition object to use when block.
     * @type HTMLPurifier_ChildDef_Optional
     */
    public $block;
    /**
     * @type string
     */
    public $type = 'chameleon';
    /**
     * @param array $inline List of elements to allow when inline.
     * @param array $block List of elements to allow when block.
     */
    public function __construct($inline, $block)
    {
        $this->inline = new Html_Purifier_child_Def_optional($inline);
        $this->block = new Html_Purifier_child_Def_optional($block);
        $this->elements = $this->block->elements;
    }
    /**
     * @param HTMLPurifier_Node[] $children
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool
     */
    public function validate_children($children, $config, $context)
    {
        if ($context->get('IsInline') === false) {
            return $this->block->validate_children($children, $config, $context);
        }
        return $this->inline->validate_children($children, $config, $context);
    }
}
// vim: et sw=4 sts=4