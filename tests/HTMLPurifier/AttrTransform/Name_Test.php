<?php

declare(strict_types=1);

class HTMLPurifier_AttrTransform_NameTest extends HTMLPurifier_AttrTransformHarness
{
    public function setUp()
    {
        parent::setUp();
        $this->obj = new HTMLPurifier_AttrTransform_Name();
    }

    public function testEmpty()
    {
        $this->assertResult([]);
    }

    public function testTransformNameToID()
    {
        $this->assertResult(
            ['name' => 'free'],
            ['id' => 'free']
        );
    }

    public function testExistingIDOverridesName()
    {
        $this->assertResult(
            ['name' => 'tryit', 'id' => 'tobad'],
            ['id' => 'tobad']
        );
    }

}

// vim: et sw=4 sts=4
