<?php

declare(strict_types=1);

class HTMLPurifier_AttrTransform_EnumToCSSTest extends HTMLPurifier_AttrTransformHarness
{
    public function setUp()
    {
        parent::setUp();
        $this->obj = new HTMLPurifier_AttrTransform_EnumToCSS('align', [
            'left'  => 'text-align:left;',
            'right' => 'text-align:right;',
        ]);
    }

    public function testEmptyInput()
    {
        $this->assertResult([]);
    }

    public function testPreserveArraysWithoutInterestingAttributes()
    {
        $this->assertResult(['style' => 'font-weight:bold;']);
    }

    public function testConvertAlignLeft()
    {
        $this->assertResult(
            ['align' => 'left'],
            ['style' => 'text-align:left;']
        );
    }

    public function testConvertAlignRight()
    {
        $this->assertResult(
            ['align' => 'right'],
            ['style' => 'text-align:right;']
        );
    }

    public function testRemoveInvalidAlign()
    {
        $this->assertResult(
            ['align' => 'invalid'],
            []
        );
    }

    public function testPrependNewCSS()
    {
        $this->assertResult(
            ['align' => 'left', 'style' => 'font-weight:bold;'],
            ['style' => 'text-align:left;font-weight:bold;']
        );

    }

    public function testCaseInsensitive()
    {
        $this->obj = new HTMLPurifier_AttrTransform_EnumToCSS('align', [
            'right' => 'text-align:right;',
        ]);
        $this->assertResult(
            ['align' => 'RIGHT'],
            ['style' => 'text-align:right;']
        );
    }

    public function testCaseSensitive()
    {
        $this->obj = new HTMLPurifier_AttrTransform_EnumToCSS('align', [
            'right' => 'text-align:right;',
        ], true);
        $this->assertResult(
            ['align' => 'RIGHT'],
            []
        );
    }

}

// vim: et sw=4 sts=4
