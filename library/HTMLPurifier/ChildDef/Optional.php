<?php

declare (strict_types=1);
/**
 * Definition that allows a set of elements, and allows no children.
 * @note This is a hack to reuse code from HTMLPurifier_ChildDef_Required,
 *       really, one shouldn't inherit from the other.  Only altered behavior
 *       is to overload a returned false with an array.  Thus, it will never
 *       return false.
 */
class Html_Purifier_child_Def_optional extends Html_Purifier_child_Def_required
{
    /**
     * @type bool
     */
    public $allow_empty = true;
    /**
     * @type string
     */
    public $type = 'optional';
    /**
     * @param array $children
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function validate_children($children, $config, $context)
    {
        $result = parent::validate_children($children, $config, $context);
        // we assume that $children is not modified
        if ($result !== false) {
            return $result;
        }
        if (empty($children)) {
            return true;
        }
        if ($this->whitespace) {
            return $children;
        }
        return [];
    }
}
// vim: et sw=4 sts=4