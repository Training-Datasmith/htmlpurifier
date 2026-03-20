<?php

declare (strict_types=1);
/**
 * Performs validations on HTMLPurifier_ConfigSchema_Interchange
 *
 * @note If you see '// handled by InterchangeBuilder', that means a
 *       design decision in that class would prevent this validation from
 *       ever being necessary. We have them anyway, however, for
 *       redundancy.
 */
class Html_Purifier_config_Schema_validator
{
    /**
     * @type HTMLPurifier_ConfigSchema_Interchange
     */
    protected $interchange;
    /**
     * @type array
     */
    protected $aliases;
    /**
     * Context-stack to provide easy to read error messages.
     * @type array
     */
    protected $context = [];
    /**
     * to test default's type.
     * @type HTMLPurifier_VarParser
     */
    protected $parser;
    public function __construct()
    {
        $this->parser = new Html_Purifier_var_Parser();
    }
    /**
     * Validates a fully-formed interchange object.
     * @param HTMLPurifier_ConfigSchema_Interchange $interchange
     * @return bool
     */
    public function validate($interchange)
    {
        $this->interchange = $interchange;
        $this->aliases = [];
        // PHP is a bit lax with integer <=> string conversions in
        // arrays, so we don't use the identical !== comparison
        foreach ($interchange->directives as $i => $directive) {
            $id = $directive->id->to_string();
            if ($i != $id) {
                $this->error(false, "Integrity violation: key '{$i}' does not match internal id '{$id}'");
            }
            $this->validate_directive($directive);
        }
        return true;
    }
    /**
     * Validates a HTMLPurifier_ConfigSchema_Interchange_Id object.
     * @param HTMLPurifier_ConfigSchema_Interchange_Id $id
     */
    public function validate_id($id)
    {
        $id_string = $id->to_string();
        $this->context[] = "id '{$id_string}'";
        if (!$id instanceof Html_Purifier_config_Schema_interchange_id) {
            // handled by InterchangeBuilder
            $this->error(false, 'is not an instance of HTMLPurifier_ConfigSchema_Interchange_Id');
        }
        // keys are now unconstrained (we might want to narrow down to A-Za-z0-9.)
        // we probably should check that it has at least one namespace
        $this->with($id, 'key')->assert_not_empty()->assert_is_string();
        // implicit assertIsString handled by InterchangeBuilder
        array_pop($this->context);
    }
    /**
     * Validates a HTMLPurifier_ConfigSchema_Interchange_Directive object.
     * @param HTMLPurifier_ConfigSchema_Interchange_Directive $d
     */
    public function validate_directive($d)
    {
        $id = $d->id->to_string();
        $this->context[] = "directive '{$id}'";
        $this->validate_id($d->id);
        $this->with($d, 'description')->assert_not_empty();
        // BEGIN - handled by InterchangeBuilder
        $this->with($d, 'type')->assert_not_empty();
        $this->with($d, 'typeAllowsNull')->assert_is_bool();
        try {
            // This also tests validity of $d->type
            $this->parser->parse($d->default, $d->type, $d->type_allows_null);
        } catch (Html_Purifier_var_Parser_Exception $e) {
            $this->error('default', 'had error: ' . $e->get_message());
        }
        // END - handled by InterchangeBuilder
        if (!is_null($d->allowed) || !empty($d->value_aliases)) {
            // allowed and valueAliases require that we be dealing with
            // strings, so check for that early.
            $d_int = Html_Purifier_var_Parser::$types[$d->type];
            if (!isset(Html_Purifier_var_Parser::$string_types[$d_int])) {
                $this->error('type', 'must be a string type when used with allowed or value aliases');
            }
        }
        $this->validate_directive_allowed($d);
        $this->validate_directive_value_aliases($d);
        $this->validate_directive_aliases($d);
        array_pop($this->context);
    }
    /**
     * Extra validation if $allowed member variable of
     * HTMLPurifier_ConfigSchema_Interchange_Directive is defined.
     * @param HTMLPurifier_ConfigSchema_Interchange_Directive $d
     */
    public function validate_directive_allowed($d)
    {
        if (is_null($d->allowed)) {
            return;
        }
        $this->with($d, 'allowed')->assert_not_empty()->assert_is_lookup();
        // handled by InterchangeBuilder
        if (is_string($d->default) && !isset($d->allowed[$d->default])) {
            $this->error('default', 'must be an allowed value');
        }
        $this->context[] = 'allowed';
        foreach ($d->allowed as $val => $x) {
            if (!is_string($val)) {
                $this->error("value {$val}", 'must be a string');
            }
        }
        array_pop($this->context);
    }
    /**
     * Extra validation if $valueAliases member variable of
     * HTMLPurifier_ConfigSchema_Interchange_Directive is defined.
     * @param HTMLPurifier_ConfigSchema_Interchange_Directive $d
     */
    public function validate_directive_value_aliases($d)
    {
        if (is_null($d->value_aliases)) {
            return;
        }
        $this->with($d, 'valueAliases')->assert_is_array();
        // handled by InterchangeBuilder
        $this->context[] = 'valueAliases';
        foreach ($d->value_aliases as $alias => $real) {
            if (!is_string($alias)) {
                $this->error("alias {$alias}", 'must be a string');
            }
            if (!is_string($real)) {
                $this->error("alias target {$real} from alias '{$alias}'", 'must be a string');
            }
            if ($alias === $real) {
                $this->error("alias '{$alias}'", 'must not be an alias to itself');
            }
        }
        if (!is_null($d->allowed)) {
            foreach ($d->value_aliases as $alias => $real) {
                if (isset($d->allowed[$alias])) {
                    $this->error("alias '{$alias}'", 'must not be an allowed value');
                } elseif (!isset($d->allowed[$real])) {
                    $this->error("alias '{$alias}'", 'must be an alias to an allowed value');
                }
            }
        }
        array_pop($this->context);
    }
    /**
     * Extra validation if $aliases member variable of
     * HTMLPurifier_ConfigSchema_Interchange_Directive is defined.
     * @param HTMLPurifier_ConfigSchema_Interchange_Directive $d
     */
    public function validate_directive_aliases($d)
    {
        $this->with($d, 'aliases')->assert_is_array();
        // handled by InterchangeBuilder
        $this->context[] = 'aliases';
        foreach ($d->aliases as $alias) {
            $this->validate_id($alias);
            $s = $alias->to_string();
            if (isset($this->interchange->directives[$s])) {
                $this->error("alias '{$s}'", 'collides with another directive');
            }
            if (isset($this->aliases[$s])) {
                $other_directive = $this->aliases[$s];
                $this->error("alias '{$s}'", "collides with alias for directive '{$other_directive}'");
            }
            $this->aliases[$s] = $d->id->to_string();
        }
        array_pop($this->context);
    }
    // protected helper functions
    /**
     * Convenience function for generating HTMLPurifier_ConfigSchema_ValidatorAtom
     * for validating simple member variables of objects.
     * @param $obj
     * @param $member
     * @return HTMLPurifier_ConfigSchema_ValidatorAtom
     */
    protected function with($obj, $member)
    {
        return new Html_Purifier_config_Schema_validator_Atom($this->get_formatted_context(), $obj, $member);
    }
    /**
     * Emits an error, providing helpful context.
     * @throws HTMLPurifier_ConfigSchema_Exception
     */
    protected function error($target, $msg)
    {
        if ($target !== false) {
            $prefix = ucfirst($target) . ' in ' . $this->get_formatted_context();
        } else {
            $prefix = ucfirst($this->get_formatted_context());
        }
        throw new Html_Purifier_config_Schema_exception(trim($prefix . ' ' . $msg));
    }
    /**
     * Returns a formatted context string.
     * @return string
     */
    protected function get_formatted_context()
    {
        return implode(' in ', array_reverse($this->context));
    }
}
// vim: et sw=4 sts=4