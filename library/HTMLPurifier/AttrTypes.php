<?php

declare (strict_types=1);
/**
 * Provides lookup array of attribute types to HTMLPurifier_AttrDef objects
 */
class Html_Purifier_attr_Types
{
    /**
     * Lookup array of attribute string identifiers to concrete implementations.
     * @type HTMLPurifier_AttrDef[]
     */
    protected $info = [];
    /**
     * Constructs the info array, supplying default implementations for attribute
     * types.
     */
    public function __construct()
    {
        // XXX This is kind of poor, since we don't actually /clone/
        // instances; instead, we use the supplied make() attribute. So,
        // the underlying class must know how to deal with arguments.
        // With the old implementation of Enum, that ignored its
        // arguments when handling a make dispatch, the IAlign
        // definition wouldn't work.
        // pseudo-types, must be instantiated via shorthand
        $this->info['Enum'] = new Html_Purifier_attr_Def_enum();
        $this->info['Bool'] = new Html_Purifier_attr_Def_html_bool();
        $this->info['CDATA'] = new Html_Purifier_attr_Def_text();
        $this->info['ID'] = new Html_Purifier_attr_Def_html_id();
        $this->info['Length'] = new Html_Purifier_attr_Def_html_length();
        $this->info['MultiLength'] = new Html_Purifier_attr_Def_html_multi_Length();
        $this->info['NMTOKENS'] = new Html_Purifier_attr_Def_html_nmtokens();
        $this->info['Pixels'] = new Html_Purifier_attr_Def_html_pixels();
        $this->info['Text'] = new Html_Purifier_attr_Def_text();
        $this->info['URI'] = new Html_Purifier_attr_Def_uri();
        $this->info['LanguageCode'] = new Html_Purifier_attr_Def_lang();
        $this->info['Color'] = new Html_Purifier_attr_Def_html_color();
        $this->info['IAlign'] = self::make_enum('top,middle,bottom,left,right');
        $this->info['LAlign'] = self::make_enum('top,bottom,left,right');
        $this->info['FrameTarget'] = new Html_Purifier_attr_Def_html_frame_Target();
        $this->info['ContentEditable'] = new Html_Purifier_attr_Def_html_content_Editable();
        // unimplemented aliases
        $this->info['ContentType'] = new Html_Purifier_attr_Def_text();
        $this->info['ContentTypes'] = new Html_Purifier_attr_Def_text();
        $this->info['Charsets'] = new Html_Purifier_attr_Def_text();
        $this->info['Character'] = new Html_Purifier_attr_Def_text();
        // "proprietary" types
        $this->info['Class'] = new Html_Purifier_attr_Def_html_class();
        // number is really a positive integer (one or more digits)
        // FIXME: ^^ not always, see start and value of list items
        $this->info['Number'] = new Html_Purifier_attr_Def_integer(false, false, true);
    }
    private static function make_enum($in)
    {
        return new Html_Purifier_attr_Def_clone(new Html_Purifier_attr_Def_enum(explode(',', $in)));
    }
    /**
     * Retrieves a type
     * @param string $type String type name
     * @return HTMLPurifier_AttrDef Object AttrDef for type
     */
    public function get($type)
    {
        // determine if there is any extra info tacked on
        if (strpos($type, '#') !== false) {
            list($type, $string) = explode('#', $type, 2);
        } else {
            $string = '';
        }
        if (!isset($this->info[$type])) {
            throw new Exception('Cannot retrieve undefined attribute type ' . $type);
        }
        return $this->info[$type]->make($string);
    }
    /**
     * Sets a new implementation for a type
     * @param string $type String type name
     * @param HTMLPurifier_AttrDef $impl Object AttrDef for type
     */
    public function set($type, $impl)
    {
        $this->info[$type] = $impl;
    }
}
// vim: et sw=4 sts=4