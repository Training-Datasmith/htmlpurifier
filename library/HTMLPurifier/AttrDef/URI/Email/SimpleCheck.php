<?php

declare (strict_types=1);
/**
 * Primitive email validation class based on the regexp found at
 * http://www.regular-expressions.info/email.html
 */
class Html_Purifier_attr_Def_uri_email_simple_Check extends Html_Purifier_attr_Def_uri_email
{
    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        // no support for named mailboxes i.e. "Bob <bob@example.com>"
        // that needs more percent encoding to be done
        if ($string == '') {
            return false;
        }
        $string = trim($string);
        $result = preg_match('/^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $string);
        return $result ? $string : false;
    }
}
// vim: et sw=4 sts=4