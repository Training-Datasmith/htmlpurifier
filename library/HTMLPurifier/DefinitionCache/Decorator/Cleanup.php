<?php

declare (strict_types=1);
/**
 * Definition cache decorator class that cleans up the cache
 * whenever there is a cache miss.
 */
class Html_Purifier_definition_Cache_decorator_cleanup extends Html_Purifier_definition_Cache_decorator
{
    /**
     * @type string
     */
    public $name = 'Cleanup';
    /**
     * @return HTMLPurifier_DefinitionCache_Decorator_Cleanup
     */
    public function copy()
    {
        return new Html_Purifier_definition_Cache_decorator_cleanup();
    }
    /**
     * @param HTMLPurifier_Definition $def
     * @param HTMLPurifier_Config $config
     * @return mixed
     */
    public function add($def, $config)
    {
        $status = parent::add($def, $config);
        if (!$status) {
            parent::cleanup($config);
        }
        return $status;
    }
    /**
     * @param HTMLPurifier_Definition $def
     * @param HTMLPurifier_Config $config
     * @return mixed
     */
    public function set($def, $config)
    {
        $status = parent::set($def, $config);
        if (!$status) {
            parent::cleanup($config);
        }
        return $status;
    }
    /**
     * @param HTMLPurifier_Definition $def
     * @param HTMLPurifier_Config $config
     * @return mixed
     */
    public function replace($def, $config)
    {
        $status = parent::replace($def, $config);
        if (!$status) {
            parent::cleanup($config);
        }
        return $status;
    }
    /**
     * @param HTMLPurifier_Config $config
     * @return mixed
     */
    public function get($config)
    {
        $ret = parent::get($config);
        if (!$ret) {
            parent::cleanup($config);
        }
        return $ret;
    }
}
// vim: et sw=4 sts=4