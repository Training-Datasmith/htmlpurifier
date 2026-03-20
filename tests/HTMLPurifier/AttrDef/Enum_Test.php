<?php

declare(strict_types=1);

class HTMLPurifier_AttrDef_EnumTest extends HTMLPurifier_AttrDefHarness
{
    public function testCaseInsensitive()
    {
        $this->def = new HTMLPurifier_AttrDef_Enum(['one', 'two']);
        $this->assertDef('one');
        $this->assertDef('ONE', 'one');
    }

    public function testCaseSensitive()
    {
        $this->def = new HTMLPurifier_AttrDef_Enum(['one', 'two'], true);
        $this->assertDef('one');
        $this->assertDef('ONE', false);
    }

    public function testFixing()
    {
        $this->def = new HTMLPurifier_AttrDef_Enum(['one']);
        $this->assertDef(' one ', 'one');
    }

    public function test_make()
    {
        $factory = new HTMLPurifier_AttrDef_Enum();

        $def = $factory->make('foo,bar');
        $def2 = new HTMLPurifier_AttrDef_Enum(['foo', 'bar']);
        $this->assertIdentical($def, $def2);

        $def = $factory->make('s:foo,BAR');
        $def2 = new HTMLPurifier_AttrDef_Enum(['foo', 'BAR'], true);
        $this->assertIdentical($def, $def2);
    }

}

// vim: et sw=4 sts=4
