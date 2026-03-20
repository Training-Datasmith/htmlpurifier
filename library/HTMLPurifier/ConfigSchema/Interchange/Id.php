<?php

declare (strict_types=1);
/**
 * Represents a directive ID in the interchange format.
 */
class Html_Purifier_config_Schema_interchange_id
{
    /**
     * @type string
     */
    public $key;
    /**
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }
    /**
     * @return string
     * @warning This is NOT magic, to ensure that people don't abuse SPL and
     *          cause problems for PHP 5.0 support.
     */
    public function to_string()
    {
        return $this->key;
    }
    /**
     * @return string
     */
    public function get_root_namespace()
    {
        return substr($this->key, 0, strpos($this->key, '.'));
    }
    /**
     * @return string
     */
    public function get_directive()
    {
        return substr($this->key, strpos($this->key, '.') + 1);
    }
    /**
     * @param string $id
     * @return HTMLPurifier_ConfigSchema_Interchange_Id
     */
    public static function make($id)
    {
        return new Html_Purifier_config_Schema_interchange_id($id);
    }
}
// vim: et sw=4 sts=4