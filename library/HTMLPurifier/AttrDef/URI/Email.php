<?php

declare (strict_types=1);
abstract class Html_Purifier_attr_Def_uri_email extends Html_Purifier_attr_Def
{
    /**
     * Unpacks a mailbox into its display-name and address
     * @param string $string
     * @return mixed
     */
    public function unpack($string)
    {
        // needs to be implemented
    }
}
// sub-implementations
// vim: et sw=4 sts=4