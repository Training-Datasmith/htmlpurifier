<?php

declare(strict_types=1);

class HTMLPurifier_AttrDef_URI_Email_SimpleCheckTest extends HTMLPurifier_AttrDef_URI_EmailHarness
{
    public function setUp()
    {
        $this->def = new HTMLPurifier_AttrDef_URI_Email_SimpleCheck();
    }

}

// vim: et sw=4 sts=4
