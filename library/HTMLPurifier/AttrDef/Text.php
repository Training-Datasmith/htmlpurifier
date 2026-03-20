<?php

declare (strict_types=1);
/**
 * Validates arbitrary text according to the HTML spec.
 */
class Html_Purifier_attr_Def_text extends Html_Purifier_attr_Def
{
    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        return $this->parse_cdata($string);
    }
}
// vim: et sw=4 sts=4