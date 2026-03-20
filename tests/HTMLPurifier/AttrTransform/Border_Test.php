<?php

declare(strict_types=1);

class HTMLPurifier_AttrTransform_BorderTest extends HTMLPurifier_AttrTransformHarness
{
    public function setUp()
    {
        parent::setUp();
        $this->obj = new HTMLPurifier_AttrTransform_Border();
    }

    public function testEmptyInput()
    {
        $this->assertResult([]);
    }

    public function testBasicTransform()
    {
        $this->assertResult(
            ['border' => '1'],
            ['style' => 'border:1px solid;']
        );
    }

    public function testLenientTreatmentOfInvalidInput()
    {
        $this->assertResult(
            ['border' => '10%'],
            ['style' => 'border:10%px solid;']
        );
    }

    public function testPrependNewCSS()
    {
        $this->assertResult(
            ['border' => '23', 'style' => 'font-weight:bold;'],
            ['style' => 'border:23px solid;font-weight:bold;']
        );
    }

}

// vim: et sw=4 sts=4
