<?php

declare (strict_types=1);
/**
 * Represents a Length as defined by CSS.
 */
class Html_Purifier_attr_Def_css_length extends Html_Purifier_attr_Def
{
    /**
     * @type HTMLPurifier_Length|string
     */
    protected $min;
    /**
     * @type HTMLPurifier_Length|string
     */
    protected $max;
    /**
     * @param HTMLPurifier_Length|string $min Minimum length, or null for no bound. String is also acceptable.
     * @param HTMLPurifier_Length|string $max Maximum length, or null for no bound. String is also acceptable.
     */
    public function __construct($min = null, $max = null)
    {
        $this->min = $min !== null ? Html_Purifier_length::make($min) : null;
        $this->max = $max !== null ? Html_Purifier_length::make($max) : null;
    }
    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $string = $this->parse_cdata($string);
        // Optimizations
        if ($string === '') {
            return false;
        }
        if ($string === '0') {
            return '0';
        }
        if (strlen($string) === 1) {
            return false;
        }
        $length = Html_Purifier_length::make($string);
        if (!$length->is_valid()) {
            return false;
        }
        if ($this->min) {
            $c = $length->compare_to($this->min);
            if ($c === false) {
                return false;
            }
            if ($c < 0) {
                return false;
            }
        }
        if ($this->max) {
            $c = $length->compare_to($this->max);
            if ($c === false) {
                return false;
            }
            if ($c > 0) {
                return false;
            }
        }
        return $length->to_string();
    }
}
// vim: et sw=4 sts=4