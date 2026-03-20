<?php

declare (strict_types=1);
class Html_Purifier_attr_Def_html_content_Editable extends Html_Purifier_attr_Def
{
    public function validate($string, $config, $context)
    {
        $allowed = ['false'];
        if ($config->get('HTML.Trusted')) {
            $allowed = ['', 'true', 'false'];
        }
        $enum = new Html_Purifier_attr_Def_enum($allowed);
        return $enum->validate($string, $config, $context);
    }
}