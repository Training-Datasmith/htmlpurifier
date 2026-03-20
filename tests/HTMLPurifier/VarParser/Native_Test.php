<?php

declare(strict_types=1);

class HTMLPurifier_VarParser_NativeTest extends HTMLPurifier_VarParserHarness
{
    public function testValidateSimple()
    {
        $this->assertValid('"foo\\\\"', 'string', 'foo\\');
    }

}

// vim: et sw=4 sts=4
