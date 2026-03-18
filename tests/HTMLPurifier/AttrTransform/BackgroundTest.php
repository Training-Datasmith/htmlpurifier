<?php

declare(strict_types=1);

class HTMLPurifier_AttrTransform_BackgroundTest extends HTMLPurifier_AttrTransformHarness
{
    public function setUp()
    {
        parent::setUp();
        $this->obj = new HTMLPurifier_AttrTransform_Background();
    }

    public function testEmptyInput()
    {
        $this->assertResult([]);
    }

    public function testBasicTransform()
    {
        $this->assertResult(
            ['background' => 'logo.png'],
            ['style' => 'background-image:url(logo.png);']
        );
    }

    public function testPrependNewCSS()
    {
        $this->assertResult(
            ['background' => 'logo.png', 'style' => 'font-weight:bold'],
            ['style' => 'background-image:url(logo.png);font-weight:bold']
        );
    }

    public function testLenientTreatmentOfInvalidInput()
    {
        // notice that we rely on the CSS validator later to fix this invalid
        // stuff
        $this->assertResult(
            ['background' => 'logo.png);foo:('],
            ['style' => 'background-image:url(logo.png);foo:();']
        );
    }

}

// vim: et sw=4 sts=4
