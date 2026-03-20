<?php

declare (strict_types=1);
/**
 * Decorator that, depending on a token, switches between two definitions.
 */
class Html_Purifier_attr_Def_switch
{
    /**
     * @type string
     */
    protected $tag;
    /**
     * @type HTMLPurifier_AttrDef
     */
    protected $with_tag;
    /**
     * @type HTMLPurifier_AttrDef
     */
    protected $without_tag;
    /**
     * @param string $tag Tag name to switch upon
     * @param HTMLPurifier_AttrDef $with_tag Call if token matches tag
     * @param HTMLPurifier_AttrDef $without_tag Call if token doesn't match, or there is no token
     */
    public function __construct($tag, $with_tag, $without_tag)
    {
        $this->tag = $tag;
        $this->with_tag = $with_tag;
        $this->without_tag = $without_tag;
    }
    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        $token = $context->get('CurrentToken', true);
        if (!$token || $token->name !== $this->tag) {
            return $this->without_tag->validate($string, $config, $context);
        }
        return $this->with_tag->validate($string, $config, $context);
    }
}
// vim: et sw=4 sts=4