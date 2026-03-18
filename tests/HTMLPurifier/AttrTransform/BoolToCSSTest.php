<?php

declare(strict_types=1);

class HTMLPurifier_AttrTransform_BoolToCSSTest extends HTMLPurifier_AttrTransformHarness
{
    public function setUp()
    {
        parent::setUp();
        $this->obj = new HTMLPurifier_AttrTransform_BoolToCSS('foo', 'bar:3in;');
    }

    public function testEmptyInput()
    {
        $this->assertResult([]);
    }

    public function testBasicTransform()
    {
        $this->assertResult(
            ['foo' => 'foo'],
            ['style' => 'bar:3in;']
        );
    }

    public function testIgnoreValueOfBooleanAttribute()
    {
        $this->assertResult(
            ['foo' => 'no'],
            ['style' => 'bar:3in;']
        );
    }

    public function testPrependCSS()
    {
        $this->assertResult(
            ['foo' => 'foo', 'style' => 'background-color:#F00;'],
            ['style' => 'bar:3in;background-color:#F00;']
        );
    }

}

// vim: et sw=4 sts=4
