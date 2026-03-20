<?php

declare (strict_types=1);
/**
 * Converts HTMLPurifier_ConfigSchema_Interchange to our runtime
 * representation used to perform checks on user configuration.
 */
class Html_Purifier_config_Schema_builder_config_Schema
{
    /**
     * @param HTMLPurifier_ConfigSchema_Interchange $interchange
     * @return HTMLPurifier_ConfigSchema
     */
    public function build($interchange)
    {
        $schema = new Html_Purifier_config_Schema();
        foreach ($interchange->directives as $d) {
            $schema->add($d->id->key, $d->default, $d->type, $d->type_allows_null);
            if ($d->allowed !== null) {
                $schema->add_allowed_values($d->id->key, $d->allowed);
            }
            foreach ($d->aliases as $alias) {
                $schema->add_alias($alias->key, $d->id->key);
            }
            if ($d->value_aliases !== null) {
                $schema->add_value_aliases($d->id->key, $d->value_aliases);
            }
        }
        $schema->post_process();
        return $schema;
    }
}
// vim: et sw=4 sts=4