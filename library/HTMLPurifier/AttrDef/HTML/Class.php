<?php

declare (strict_types=1);
/**
 * Implements special behavior for class attribute (normally NMTOKENS)
 */
class Html_Purifier_attr_Def_html_class extends Html_Purifier_attr_Def_html_nmtokens
{
    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    protected function split($string, $config, $context)
    {
        // really, this twiddle should be lazy loaded
        $name = $config->get_definition('HTML')->doctype->name;
        if ($name == 'XHTML 1.1' || $name == 'XHTML 2.0') {
            return parent::split($string, $config, $context);
        }
        return preg_split('/\s+/', $string);
    }
    /**
     * @param array $tokens
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    protected function filter($tokens, $config, $context)
    {
        $allowed = $config->get('Attr.AllowedClasses');
        $forbidden = $config->get('Attr.ForbiddenClasses');
        $ret = [];
        foreach ($tokens as $token) {
            if (($allowed === null || isset($allowed[$token])) && !isset($forbidden[$token]) && !in_array($token, $ret, true)) {
                $ret[] = $token;
            }
        }
        return $ret;
    }
}