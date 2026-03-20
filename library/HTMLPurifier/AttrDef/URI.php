<?php

declare (strict_types=1);
/**
 * Validates a URI as defined by RFC 3986.
 * @note Scheme-specific mechanics deferred to HTMLPurifier_URIScheme
 */
class Html_Purifier_attr_Def_uri extends Html_Purifier_attr_Def
{
    /**
     * @type HTMLPurifier_URIParser
     */
    protected $parser;
    /**
     * @type bool
     */
    protected $embeds_resource;
    /**
     * @param bool $embeds_resource Does the URI here result in an extra HTTP request?
     */
    public function __construct($embeds_resource = false)
    {
        $this->parser = new Html_Purifier_uri_Parser();
        $this->embeds_resource = (bool) $embeds_resource;
    }
    /**
     * @param string $string
     * @return HTMLPurifier_AttrDef_URI
     */
    public function make($string)
    {
        $embeds = $string === 'embedded';
        return new Html_Purifier_attr_Def_uri($embeds);
    }
    /**
     * @param string $uri
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($uri, $config, $context)
    {
        if ($config->get('URI.Disable')) {
            return false;
        }
        $uri = $this->parse_cdata($uri);
        // parse the URI
        $uri = $this->parser->parse($uri);
        if ($uri === false) {
            return false;
        }
        // add embedded flag to context for validators
        $context->register('EmbeddedURI', $this->embeds_resource);
        $ok = false;
        do {
            // generic validation
            $result = $uri->validate($config, $context);
            if (!$result) {
                break;
            }
            // chained filtering
            $uri_def = $config->get_definition('URI');
            $result = $uri_def->filter($uri, $config, $context);
            if (!$result) {
                break;
            }
            // scheme-specific validation
            $scheme_obj = $uri->get_scheme_obj($config, $context);
            if (!$scheme_obj) {
                break;
            }
            if ($this->embeds_resource && !$scheme_obj->browsable) {
                break;
            }
            $result = $scheme_obj->validate($uri, $config, $context);
            if (!$result) {
                break;
            }
            // Post chained filtering
            $result = $uri_def->post_filter($uri, $config, $context);
            if (!$result) {
                break;
            }
            // survived gauntlet
            $ok = true;
        } while (false);
        $context->destroy('EmbeddedURI');
        if (!$ok) {
            return false;
        }
        // back to string
        return $uri->to_string();
    }
}
// vim: et sw=4 sts=4