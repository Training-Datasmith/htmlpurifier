<?php

declare (strict_types=1);
/**
 * Pre-transform that changes deprecated bgcolor attribute to CSS.
 */
class Html_Purifier_attr_Transform_bg_Color extends Html_Purifier_attr_Transform
{
    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr['bgcolor'])) {
            return $attr;
        }
        $bgcolor = $this->confiscate_attr($attr, 'bgcolor');
        // some validation should happen here
        $this->prepend_css($attr, "background-color:{$bgcolor};");
        return $attr;
    }
}
// vim: et sw=4 sts=4