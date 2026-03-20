<?php

declare (strict_types=1);
/**
 * Generic schema interchange format that can be converted to a runtime
 * representation (HTMLPurifier_ConfigSchema) or HTML documentation. Members
 * are completely validated.
 */
class Html_Purifier_config_Schema_interchange
{
    /**
     * Name of the application this schema is describing.
     * @type string
     */
    public $name;
    /**
     * Array of Directive ID => array(directive info)
     * @type HTMLPurifier_ConfigSchema_Interchange_Directive[]
     */
    public $directives = [];
    /**
     * Adds a directive array to $directives
     * @param HTMLPurifier_ConfigSchema_Interchange_Directive $directive
     * @throws HTMLPurifier_ConfigSchema_Exception
     */
    public function add_directive($directive)
    {
        if (isset($this->directives[$i = $directive->id->to_string()])) {
            throw new Html_Purifier_config_Schema_exception("Cannot redefine directive '{$i}'");
        }
        $this->directives[$i] = $directive;
    }
    /**
     * Convenience function to perform standard validation. Throws exception
     * on failed validation.
     */
    public function validate()
    {
        $validator = new Html_Purifier_config_Schema_validator();
        return $validator->validate($this);
    }
}
// vim: et sw=4 sts=4