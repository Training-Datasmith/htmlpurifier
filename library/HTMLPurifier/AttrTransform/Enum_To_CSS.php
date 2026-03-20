<?php

declare (strict_types=1);
/**
 * Generic pre-transform that converts an attribute with a fixed number of
 * values (enumerated) to CSS.
 */
class Html_Purifier_attr_Transform_enum_To_Css extends Html_Purifier_attr_Transform
{
    /**
     * Name of attribute to transform from.
     * @type string
     */
    protected $attr;
    /**
     * Lookup array of attribute values to CSS.
     * @type array
     */
    protected $enum_to_css = [];
    /**
     * Case sensitivity of the matching.
     * @type bool
     * @warning Currently can only be guaranteed to work with ASCII
     *          values.
     */
    protected $case_sensitive = false;
    /**
     * @param string $attr Attribute name to transform from
     * @param array $enum_to_css Lookup array of attribute values to CSS
     * @param bool $case_sensitive Case sensitivity indicator, default false
     */
    public function __construct($attr, $enum_to_css, $case_sensitive = false)
    {
        $this->attr = $attr;
        $this->enum_to_css = $enum_to_css;
        $this->case_sensitive = (bool) $case_sensitive;
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
        $value = trim($attr[$this->attr]);
        unset($attr[$this->attr]);
        if (!$this->case_sensitive) {
            $value = strtolower($value);
        }
        if (!isset($this->enum_to_css[$value])) {
            return $attr;
        }
        $this->prepend_css($attr, $this->enum_to_css[$value]);
        return $attr;
    }
}
// vim: et sw=4 sts=4