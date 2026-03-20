<?php

declare (strict_types=1);
/**
 * Pre-transform that changes converts a boolean attribute to fixed CSS
 */
class Html_Purifier_attr_Transform_bool_To_Css extends Html_Purifier_attr_Transform
{
    /**
     * Name of boolean attribute that is trigger.
     * @type string
     */
    protected $attr;
    /**
     * CSS declarations to add to style, needs trailing semicolon.
     * @type string
     */
    protected $css;
    /**
     * @param string $attr attribute name to convert from
     * @param string $css CSS declarations to add to style (needs semicolon)
     */
    public function __construct($attr, $css)
    {
        $this->attr = $attr;
        $this->css = $css;
    }
    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr[$this->attr])) {
            return $attr;
        }
        unset($attr[$this->attr]);
        $this->prepend_css($attr, $this->css);
        return $attr;
    }
}
// vim: et sw=4 sts=4