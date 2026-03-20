<?php

declare (strict_types=1);
/**
 * Post-transform that performs validation to the name attribute; if
 * it is present with an equivalent id attribute, it is passed through;
 * otherwise validation is performed.
 */
class Html_Purifier_attr_Transform_name_Sync extends Html_Purifier_attr_Transform
{
    /**
     * @type HTMLPurifier_AttrDef_HTML_ID
     */
    public $id_def;
    public function __construct()
    {
        $this->id_def = new Html_Purifier_attr_Def_html_id();
    }
    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr['name'])) {
            return $attr;
        }
        $name = $attr['name'];
        if (isset($attr['id']) && $attr['id'] === $name) {
            return $attr;
        }
        $result = $this->id_def->validate($name, $config, $context);
        if ($result === false) {
            unset($attr['name']);
        } else {
            $attr['name'] = $result;
        }
        return $attr;
    }
}
// vim: et sw=4 sts=4