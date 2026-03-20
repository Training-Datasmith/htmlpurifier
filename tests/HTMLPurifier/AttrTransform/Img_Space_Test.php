<?php

declare(strict_types=1);

class HTMLPurifier_AttrTransform_ImgSpaceTest extends HTMLPurifier_AttrTransformHarness
{
    public function setUp()
    {
        parent::setUp();
        $this->obj = new HTMLPurifier_AttrTransform_ImgSpace('vspace');
    }

    public function testEmptyInput()
    {
        $this->assertResult([]);
    }

    public function testVerticalBasicUsage()
    {
        $this->assertResult(
            ['vspace' => '1'],
            ['style' => 'margin-top:1px;margin-bottom:1px;']
        );
    }

    public function testLenientHandlingOfInvalidInput()
    {
        $this->assertResult(
            ['vspace' => '10%'],
            ['style' => 'margin-top:10%px;margin-bottom:10%px;']
        );
    }

    public function testPrependNewCSS()
    {
        $this->assertResult(
            ['vspace' => '23', 'style' => 'font-weight:bold;'],
            ['style' => 'margin-top:23px;margin-bottom:23px;font-weight:bold;']
        );
    }

    public function testHorizontalBasicUsage()
    {
        $this->obj = new HTMLPurifier_AttrTransform_ImgSpace('hspace');
        $this->assertResult(
            ['hspace' => '1'],
            ['style' => 'margin-left:1px;margin-right:1px;']
        );
    }

    public function testInvalidConstructionParameter()
    {
        $this->expectError('ispace is not valid space attribute');
        $this->obj = new HTMLPurifier_AttrTransform_ImgSpace('ispace');
        $this->assertResult(
            ['ispace' => '1'],
            []
        );
    }

}

// vim: et sw=4 sts=4
