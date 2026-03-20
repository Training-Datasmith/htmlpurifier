<?php

declare (strict_types=1);
/**
 * Pre-transform that changes proprietary background attribute to CSS.
 */
class Html_Purifier_attr_Transform_background extends Html_Purifier_attr_Transform
{
    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr['background'])) {
            return $attr;
        }
        $background = $this->confiscate_attr($attr, 'background');
        // some validation should happen here
        $this->prepend_css($attr, "background-image:url({$background});");
        return $attr;
    }
}
// vim: et sw=4 sts=4