<?php

declare (strict_types=1);
/**
 * Implements required attribute stipulation for <script>
 */
class Html_Purifier_attr_Transform_script_Required extends Html_Purifier_attr_Transform
{
    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr['type'])) {
            $attr['type'] = 'text/javascript';
        }
        return $attr;
    }
}
// vim: et sw=4 sts=4