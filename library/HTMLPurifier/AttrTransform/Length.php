<?php

declare (strict_types=1);
/**
 * Class for handling width/height length attribute transformations to CSS
 */
class Html_Purifier_attr_Transform_length extends Html_Purifier_attr_Transform
{
    /**
     * @type string
     */
    protected $name;
    /**
     * @type string
     */
    protected $css_name;
    public function __construct($name, $css_name = null)
    {
        $this->name = $name;
        $this->css_name = $css_name ?: $name;
    }
    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr[$this->name])) {
            return $attr;
        }
        $length = $this->confiscate_attr($attr, $this->name);
        if (ctype_digit($length)) {
            $length .= 'px';
        }
        $this->prepend_css($attr, $this->css_name . ":{$length};");
        return $attr;
    }
}
// vim: et sw=4 sts=4