<?php

declare (strict_types=1);
/**
 * Sets height/width defaults for <textarea>
 */
class Html_Purifier_attr_Transform_textarea extends Html_Purifier_attr_Transform
{
    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        // Calculated from Firefox
        if (!isset($attr['cols'])) {
            $attr['cols'] = '22';
        }
        if (!isset($attr['rows'])) {
            $attr['rows'] = '3';
        }
        return $attr;
    }
}
// vim: et sw=4 sts=4