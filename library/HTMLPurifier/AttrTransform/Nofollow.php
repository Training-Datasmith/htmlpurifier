<?php

declare (strict_types=1);
// must be called POST validation
/**
 * Adds rel="nofollow" to all outbound links.  This transform is
 * only attached if Attr.Nofollow is TRUE.
 */
class Html_Purifier_attr_Transform_nofollow extends Html_Purifier_attr_Transform
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
        $scheme = $url->get_scheme_obj($config, $context);
        if ($scheme->browsable && !$url->is_local($config, $context)) {
            if (isset($attr['rel'])) {
                $rels = explode(' ', $attr['rel']);
                if (!in_array('nofollow', $rels)) {
                    $rels[] = 'nofollow';
                }
                $attr['rel'] = implode(' ', $rels);
            } else {
                $attr['rel'] = 'nofollow';
            }
        }
        return $attr;
    }
}
// vim: et sw=4 sts=4