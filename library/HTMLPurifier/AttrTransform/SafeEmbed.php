<?php

declare (strict_types=1);
class Html_Purifier_attr_Transform_safe_Embed extends Html_Purifier_attr_Transform
{
    /**
     * @type string
     */
    public $name = 'SafeEmbed';
    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        $attr['allowscriptaccess'] = 'never';
        $attr['allownetworking'] = 'internal';
        $attr['type'] = 'application/x-shockwave-flash';
        return $attr;
    }
}
// vim: et sw=4 sts=4