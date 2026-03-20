<?php

declare (strict_types=1);
// must be called POST validation
/**
 * Adds target="blank" to all outbound links.  This transform is
 * only attached if Attr.TargetBlank is TRUE.  This works regardless
 * of whether or not Attr.AllowedFrameTargets
 */
class Html_Purifier_attr_Transform_target_Blank extends Html_Purifier_attr_Transform
{
    /**
     * @type HTMLPurifier_URIParser
     */
    private $parser;
    public function __construct()
    {
        $this->parser = new Html_Purifier_uri_Parser();
    }
    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr['href'])) {
            return $attr;
        }
        // XXX Kind of inefficient
        $url = $this->parser->parse($attr['href']);
        // Ignore invalid schemes (e.g. `javascript:`)
        if (!$scheme = $url->get_scheme_obj($config, $context)) {
            return $attr;
        }
        if ($scheme->browsable && !$url->is_benign($config, $context)) {
            $attr['target'] = '_blank';
        }
        return $attr;
    }
}
// vim: et sw=4 sts=4