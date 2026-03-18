<?php

declare(strict_types=1);

// we currently rely on the CSS validator to fix any problems.
// This means that this transform, strictly speaking, supports
// a superset of the functionality.

class HTMLPurifier_AttrTransform_BgColorTest extends HTMLPurifier_AttrTransformHarness
{
    public function setUp()
    {
        parent::setUp();
        $this->obj = new HTMLPurifier_AttrTransform_BgColor();
    }

    public function testEmptyInput()
    {
        $this->assertResult([]);
    }

    public function testBasicTransform()
    {
        $this->assertResult(
            ['bgcolor' => '#000000'],
            ['style' => 'background-color:#000000;']
        );
    }

    public function testPrependNewCSS()
    {
        $this->assertResult(
            ['bgcolor' => '#000000', 'style' => 'font-weight:bold'],
            ['style' => 'background-color:#000000;font-weight:bold']
        );
    }

    public function testLenientTreatmentOfInvalidInput()
    {
        // this may change when we natively support the datatype and
        // validate its contents before forwarding it on
        $this->assertResult(
            ['bgcolor' => '#F00'],
            ['style' => 'background-color:#F00;']
        );
    }

}

// vim: et sw=4 sts=4
