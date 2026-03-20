<?php

declare (strict_types=1);
class Html_Purifier_config_Schema_interchange_Builder
{
    /**
     * Used for processing DEFAULT, nothing else.
     * @type HTMLPurifier_VarParser
     */
    protected $var_parser;
    /**
     * @param HTMLPurifier_VarParser $varParser
     */
    public function __construct($var_parser = null)
    {
        $this->var_parser = $var_parser ?: new Html_Purifier_var_Parser_native();
    }
    /**
     * @param string $dir
     * @return HTMLPurifier_ConfigSchema_Interchange
     */
    public static function build_from_directory($dir = null)
    {
        $builder = new Html_Purifier_config_Schema_interchange_Builder();
        $interchange = new Html_Purifier_config_Schema_interchange();
        return $builder->build_dir($interchange, $dir);
    }
    /**
     * @param HTMLPurifier_ConfigSchema_Interchange $interchange
     * @param string $dir
     * @return HTMLPurifier_ConfigSchema_Interchange
     */
    public function build_dir($interchange, $dir = null)
    {
        if (!$dir) {
            $dir = HTMLPURIFIER_PREFIX . '/HTMLPurifier/ConfigSchema/schema';
        }
        if (file_exists($dir . '/info.ini')) {
            $info = parse_ini_file($dir . '/info.ini');
            $interchange->name = $info['name'];
        }
        $files = [];
        $dh = opendir($dir);
        while (false !== $file = readdir($dh)) {
            if (!$file) {
                continue;
            }
            if ($file[0] == '.') {
                continue;
            }
            if (strrchr($file, '.') !== '.txt') {
                continue;
            }
            $files[] = $file;
        }
        closedir($dh);
        sort($files);
        foreach ($files as $file) {
            $this->build_file($interchange, $dir . '/' . $file);
        }
        return $interchange;
    }
    /**
     * @param HTMLPurifier_ConfigSchema_Interchange $interchange
     * @param string $file
     */
    public function build_file($interchange, $file)
    {
        $parser = new Html_Purifier_string_Hash_Parser();
        $this->build($interchange, new Html_Purifier_string_Hash($parser->parse_file($file)));
    }
    /**
     * Builds an interchange object based on a hash.
     * @param HTMLPurifier_ConfigSchema_Interchange $interchange HTMLPurifier_ConfigSchema_Interchange object to build
     * @param HTMLPurifier_StringHash $hash source data
     * @throws HTMLPurifier_ConfigSchema_Exception
     */
    public function build($interchange, $hash)
    {
        if (!$hash instanceof Html_Purifier_string_Hash) {
            $hash = new Html_Purifier_string_Hash($hash);
        }
        if (!isset($hash['ID'])) {
            throw new Html_Purifier_config_Schema_exception('Hash does not have any ID');
        }
        if (strpos($hash['ID'], '.') === false) {
            if (count($hash) == 2 && isset($hash['DESCRIPTION'])) {
                $hash->offsetGet('DESCRIPTION');
                // prevent complaining
            } else {
                throw new Html_Purifier_config_Schema_exception('All directives must have a namespace');
            }
        } else {
            $this->build_directive($interchange, $hash);
        }
        $this->_find_unused($hash);
    }
    /**
     * @param HTMLPurifier_ConfigSchema_Interchange $interchange
     * @param HTMLPurifier_StringHash $hash
     * @throws HTMLPurifier_ConfigSchema_Exception
     */
    public function build_directive($interchange, $hash)
    {
        $directive = new Html_Purifier_config_Schema_interchange_directive();
        // These are required elements:
        $directive->id = $this->id($hash->offsetGet('ID'));
        $id = $directive->id->to_string();
        // convenience
        if (isset($hash['TYPE'])) {
            $type = explode('/', $hash->offsetGet('TYPE'));
            if (isset($type[1])) {
                $directive->type_allows_null = true;
            }
            $directive->type = $type[0];
        } else {
            throw new Html_Purifier_config_Schema_exception("TYPE in directive hash '{$id}' not defined");
        }
        if (isset($hash['DEFAULT'])) {
            try {
                $directive->default = $this->var_parser->parse($hash->offsetGet('DEFAULT'), $directive->type, $directive->type_allows_null);
            } catch (Html_Purifier_var_Parser_Exception $e) {
                throw new Html_Purifier_config_Schema_exception($e->get_message() . " in DEFAULT in directive hash '{$id}'");
            }
        }
        if (isset($hash['DESCRIPTION'])) {
            $directive->description = $hash->offsetGet('DESCRIPTION');
        }
        if (isset($hash['ALLOWED'])) {
            $directive->allowed = $this->lookup($this->eval_array($hash->offsetGet('ALLOWED')));
        }
        if (isset($hash['VALUE-ALIASES'])) {
            $directive->value_aliases = $this->eval_array($hash->offsetGet('VALUE-ALIASES'));
        }
        if (isset($hash['ALIASES'])) {
            $raw_aliases = trim($hash->offsetGet('ALIASES'));
            $aliases = preg_split('/\s*,\s*/', $raw_aliases);
            foreach ($aliases as $alias) {
                $directive->aliases[] = $this->id($alias);
            }
        }
        if (isset($hash['VERSION'])) {
            $directive->version = $hash->offsetGet('VERSION');
        }
        if (isset($hash['DEPRECATED-USE'])) {
            $directive->deprecated_use = $this->id($hash->offsetGet('DEPRECATED-USE'));
        }
        if (isset($hash['DEPRECATED-VERSION'])) {
            $directive->deprecated_version = $hash->offsetGet('DEPRECATED-VERSION');
        }
        if (isset($hash['EXTERNAL'])) {
            $directive->external = preg_split('/\s*,\s*/', trim($hash->offsetGet('EXTERNAL')));
        }
        $interchange->add_directive($directive);
    }
    /**
     * Evaluates an array PHP code string without array() wrapper
     * @param string $contents
     */
    protected function eval_array($contents)
    {
        return eval('return array(' . $contents . ');');
    }
    /**
     * Converts an array list into a lookup array.
     * @param array $array
     * @return array
     */
    protected function lookup($array)
    {
        $ret = [];
        foreach ($array as $val) {
            $ret[$val] = true;
        }
        return $ret;
    }
    /**
     * Convenience function that creates an HTMLPurifier_ConfigSchema_Interchange_Id
     * object based on a string Id.
     * @param string $id
     * @return HTMLPurifier_ConfigSchema_Interchange_Id
     */
    protected function id($id)
    {
        return Html_Purifier_config_Schema_interchange_id::make($id);
    }
    /**
     * Triggers errors for any unused keys passed in the hash; such keys
     * may indicate typos, missing values, etc.
     * @param HTMLPurifier_StringHash $hash Hash to check.
     */
    protected function _find_unused($hash)
    {
        $accessed = $hash->get_accessed();
        foreach ($hash as $k => $v) {
            if (!isset($accessed[$k])) {
                trigger_error("String hash key '{$k}' not used by builder", E_USER_NOTICE);
            }
        }
    }
}
// vim: et sw=4 sts=4