<?php

declare (strict_types=1);
/**
 * Validates a boolean attribute
 */
class Html_Purifier_attr_Def_html_bool extends Html_Purifier_attr_Def
{
    /**
     * @type string
     */
    protected $name;
    /**
     * @type bool
     */
    public $minimized = true;
    /**
     * @param bool|string $name
     */
    public function __construct($name = false)
    {
        $this->name = $name;
    }
    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        return $this->name;
    }
    /**
     * @param string $string Name of attribute
     * @return HTMLPurifier_AttrDef_HTML_Bool
     */
    public function make($string)
    {
        return new Html_Purifier_attr_Def_html_bool($string);
    }
}
// vim: et sw=4 sts=4