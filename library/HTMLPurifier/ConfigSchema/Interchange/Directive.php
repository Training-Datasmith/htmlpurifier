<?php

declare (strict_types=1);
/**
 * Interchange component class describing configuration directives.
 */
class Html_Purifier_config_Schema_interchange_directive
{
    /**
     * ID of directive.
     * @type HTMLPurifier_ConfigSchema_Interchange_Id
     */
    public $id;
    /**
     * Type, e.g. 'integer' or 'istring'.
     * @type string
     */
    public $type;
    /**
     * Default value, e.g. 3 or 'DefaultVal'.
     * @type mixed
     */
    public $default;
    /**
     * HTML description.
     * @type string
     */
    public $description;
    /**
     * Whether or not null is allowed as a value.
     * @type bool
     */
    public $type_allows_null = false;
    /**
     * Lookup table of allowed scalar values.
     * e.g. array('allowed' => true).
     * Null if all values are allowed.
     * @type array
     */
    public $allowed;
    /**
     * List of aliases for the directive.
     * e.g. array(new HTMLPurifier_ConfigSchema_Interchange_Id('Ns', 'Dir'))).
     * @type HTMLPurifier_ConfigSchema_Interchange_Id[]
     */
    public $aliases = [];
    /**
     * Hash of value aliases, e.g. array('alt' => 'real'). Null if value
     * aliasing is disabled (necessary for non-scalar types).
     * @type array
     */
    public $value_aliases;
    /**
     * Version of HTML Purifier the directive was introduced, e.g. '1.3.1'.
     * Null if the directive has always existed.
     * @type string
     */
    public $version;
    /**
     * ID of directive that supersedes this old directive.
     * Null if not deprecated.
     * @type HTMLPurifier_ConfigSchema_Interchange_Id
     */
    public $deprecated_use;
    /**
     * Version of HTML Purifier this directive was deprecated. Null if not
     * deprecated.
     * @type string
     */
    public $deprecated_version;
    /**
     * List of external projects this directive depends on, e.g. array('CSSTidy').
     * @type array
     */
    public $external = [];
}
// vim: et sw=4 sts=4