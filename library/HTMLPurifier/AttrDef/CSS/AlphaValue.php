<?php

declare (strict_types=1);
class Html_Purifier_attr_Def_css_alpha_Value extends Html_Purifier_attr_Def_css_number
{
    public function __construct()
    {
        parent::__construct();
        // opacity is non-negative, but we will clamp it
    }
    /**
     * @param string $number
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return string
     */
    public function validate($number, $config, $context)
    {
        $result = parent::validate($number, $config, $context);
        if ($result === false) {
            return $result;
        }
        $float = (float) $result;
        if ($float < 0.0) {
            $result = '0';
        }
        if ($float > 1.0) {
            return '1';
        }
        return $result;
    }
}
// vim: et sw=4 sts=4