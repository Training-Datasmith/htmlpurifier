<?php

declare (strict_types=1);
/**
 * Validates the border property as defined by CSS.
 */
class Html_Purifier_attr_Def_css_border extends Html_Purifier_attr_Def
{
    /**
     * Local copy of properties this property is shorthand for.
     * @type HTMLPurifier_AttrDef[]
     */
    protected $info = [];
    /**
     * @param HTMLPurifier_Config $config
     */
    public function __construct($config)
    {
        $def = $config->get_css_definition();
        $this->info['border-width'] = $def->info['border-width'];
        $this->info['border-style'] = $def->info['border-style'];
        $this->info['border-top-color'] = $def->info['border-top-color'];
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
        $string = $this->munge_rgb($string);
        $bits = explode(' ', $string);
        $done = [];
        // segments we've finished
        $ret = '';
        // return value
        foreach ($bits as $bit) {
            foreach ($this->info as $propname => $validator) {
                if (isset($done[$propname])) {
                    continue;
                }
                $r = $validator->validate($bit, $config, $context);
                if ($r !== false) {
                    $ret .= $r . ' ';
                    $done[$propname] = true;
                    break;
                }
            }
        }
        return rtrim($ret);
    }
}
// vim: et sw=4 sts=4