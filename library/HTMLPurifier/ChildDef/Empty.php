<?php

declare (strict_types=1);
/**
 * Definition that disallows all elements.
 * @warning validateChildren() in this class is actually never called, because
 *          empty elements are corrected in HTMLPurifier_Strategy_MakeWellFormed
 *          before child definitions are parsed in earnest by
 *          HTMLPurifier_Strategy_FixNesting.
 */
class Html_Purifier_child_Def_empty extends Html_Purifier_child_Def
{
    /**
     * @type bool
     */
    public $allow_empty = true;
    /**
     * @type string
     */
    public $type = 'empty';
    /**
     * @param HTMLPurifier_Node[] $children
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function validate_children($children, $config, $context)
    {
        return [];
    }
}
// vim: et sw=4 sts=4