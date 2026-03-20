<?php

declare(strict_types=1);

class HTMLPurifier_AttrTransform_BdoDirTest extends HTMLPurifier_AttrTransformHarness
{
    public function setUp()
    {
        parent::setUp();
        $this->obj = new HTMLPurifier_AttrTransform_BdoDir();
    }

    public function testAddDefaultDir()
    {
        $this->assertResult([], ['dir' => 'ltr']);
    }

    public function testPreserveExistingDir()
    {
        $this->assertResult(['dir' => 'rtl']);
    }

    public function testAlternateDefault()
    {
        $this->config->set('Attr.DefaultTextDir', 'rtl');
        $this->assertResult(
            [],
            ['dir' => 'rtl']
        );

    }

}

// vim: et sw=4 sts=4
