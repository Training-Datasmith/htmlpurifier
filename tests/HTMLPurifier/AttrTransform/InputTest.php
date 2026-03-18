<?php

declare(strict_types=1);

class HTMLPurifier_AttrTransform_InputTest extends HTMLPurifier_AttrTransformHarness
{
    public function setUp()
    {
        parent::setUp();
        $this->obj = new HTMLPurifier_AttrTransform_Input();
    }

    public function testEmptyInput()
    {
        $this->assertResult([]);
    }

    public function testInvalidCheckedWithEmpty()
    {
        $this->assertResult(['checked' => 'checked'], []);
    }

    public function testInvalidCheckedWithPassword()
    {
        $this->assertResult([
            'checked' => 'checked',
            'type' => 'password',
        ], [
            'type' => 'password',
        ]);
    }

    public function testValidCheckedWithUcCheckbox()
    {
        $this->assertResult([
            'checked' => 'checked',
            'type' => 'CHECKBOX',
            'value' => 'bar',
        ]);
    }

    public function testInvalidMaxlength()
    {
        $this->assertResult([
            'maxlength' => '10',
            'type' => 'checkbox',
            'value' => 'foo',
        ], [
            'type' => 'checkbox',
            'value' => 'foo',
        ]);
    }

    public function testValidMaxLength()
    {
        $this->assertResult([
            'maxlength' => '10',
        ]);
    }

    // these two are really bad test-cases

    public function testSizeWithCheckbox()
    {
        $this->assertResult([
            'type' => 'checkbox',
            'value' => 'foo',
            'size' => '100px',
        ], [
            'type' => 'checkbox',
            'value' => 'foo',
            'size' => '100',
        ]);
    }

    public function testSizeWithText()
    {
        $this->assertResult([
            'type' => 'password',
            'size' => '100px', // spurious value, to indicate no validation takes place
        ], [
            'type' => 'password',
            'size' => '100px',
        ]);
    }

    public function testInvalidSrc()
    {
        $this->assertResult([
            'src' => 'img.png',
        ], []);
    }

    public function testMissingValue()
    {
        $this->assertResult([
            'type' => 'checkbox',
        ], [
            'type' => 'checkbox',
            'value' => '',
        ]);
    }

}

// vim: et sw=4 sts=4
