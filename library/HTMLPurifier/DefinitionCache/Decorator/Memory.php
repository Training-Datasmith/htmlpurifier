<?php

declare (strict_types=1);
/**
 * Definition cache decorator class that saves all cache retrievals
 * to PHP's memory; good for unit tests or circumstances where
 * there are lots of configuration objects floating around.
 */
class Html_Purifier_definition_Cache_decorator_memory extends Html_Purifier_definition_Cache_decorator
{
    /**
     * @type array
     */
    protected $definitions;
    /**
     * @type string
     */
    public $name = 'Memory';
    /**
     * @return HTMLPurifier_DefinitionCache_Decorator_Memory
     */
    public function copy()
    {
        return new Html_Purifier_definition_Cache_decorator_memory();
    }
    /**
     * @param HTMLPurifier_Definition $def
     * @param HTMLPurifier_Config $config
     * @return mixed
     */
    public function add($def, $config)
    {
        $status = parent::add($def, $config);
        if ($status) {
            $this->definitions[$this->generate_key($config)] = $def;
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
        if ($status) {
            $this->definitions[$this->generate_key($config)] = $def;
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
        if ($status) {
            $this->definitions[$this->generate_key($config)] = $def;
        }
        return $status;
    }
    /**
     * @param HTMLPurifier_Config $config
     * @return mixed
     */
    public function get($config)
    {
        $key = $this->generate_key($config);
        if (isset($this->definitions[$key])) {
            return $this->definitions[$key];
        }
        $this->definitions[$key] = parent::get($config);
        return $this->definitions[$key];
    }
}
// vim: et sw=4 sts=4