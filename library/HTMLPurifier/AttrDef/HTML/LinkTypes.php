<?php

declare (strict_types=1);
/**
 * Validates a rel/rev link attribute against a directive of allowed values
 * @note We cannot use Enum because link types allow multiple
 *       values.
 * @note Assumes link types are ASCII text
 */
class Html_Purifier_attr_Def_html_link_Types extends Html_Purifier_attr_Def
{
    /**
     * Name config attribute to pull.
     * @type string
     */
    protected $name;
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $config_lookup = ['rel' => 'AllowedRel', 'rev' => 'AllowedRev'];
        if (!isset($config_lookup[$name])) {
            throw new Exception('Unrecognized attribute name for link relationship.');
        }
        $this->name = $config_lookup[$name];
    }
    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $allowed = $config->get('Attr.' . $this->name);
        if (empty($allowed)) {
            return false;
        }
        $string = $this->parse_cdata($string);
        $parts = explode(' ', $string);
        // lookup to prevent duplicates
        $ret_lookup = [];
        foreach ($parts as $part) {
            $part = strtolower(trim($part));
            if (!isset($allowed[$part])) {
                continue;
            }
            $ret_lookup[$part] = true;
        }
        if (empty($ret_lookup)) {
            return false;
        }
        return implode(' ', array_keys($ret_lookup));
    }
}
// vim: et sw=4 sts=4