<?php

declare(strict_types=1);

class HTMLPurifier_AttrTransform_LengthTest extends HTMLPurifier_AttrTransformHarness
{
    public function setUp()
    {
        parent::setUp();
        $this->obj = new HTMLPurifier_AttrTransform_Length('width');
    }

    public function testEmptyInput()
    {
        $this->assertResult([]);
    }

    public function testTransformPixel()
    {
        $this->assertResult(
            ['width' => '10'],
            ['style' => 'width:10px;']
        );
    }

    public function testTransformPercentage()
    {
        $this->assertResult(
            ['width' => '10%'],
            ['style' => 'width:10%;']
        );
    }

    public function testPrependNewCSS()
    {
        $this->assertResult(
            ['width' => '10%', 'style' => 'font-weight:bold'],
            ['style' => 'width:10%;font-weight:bold']
        );
    }

    public function testLenientTreatmentOfInvalidInput()
    {
        $this->assertResult(
            ['width' => 'asdf'],
            ['style' => 'width:asdf;']
        );
    }

}

// vim: et sw=4 sts=4
