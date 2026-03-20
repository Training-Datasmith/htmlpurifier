<?php

declare (strict_types=1);
/**
 * Pre-transform that changes deprecated border attribute to CSS.
 */
class Html_Purifier_attr_Transform_border extends Html_Purifier_attr_Transform
{
    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr['border'])) {
            return $attr;
        }
        $border_width = $this->confiscate_attr($attr, 'border');
        // some validation should happen here
        $this->prepend_css($attr, "border:{$border_width}px solid;");
        return $attr;
    }
}
// vim: et sw=4 sts=4