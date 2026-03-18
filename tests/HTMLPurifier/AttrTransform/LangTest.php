<?php

declare(strict_types=1);

class HTMLPurifier_AttrTransform_LangTest extends HTMLPurifier_AttrTransformHarness
{
    public function setUp()
    {
        parent::setUp();
        $this->obj = new HTMLPurifier_AttrTransform_Lang();
    }

    public function testEmptyInput()
    {
        $this->assertResult([]);
    }

    public function testCopyLangToXMLLang()
    {
        $this->assertResult(
            ['lang' => 'en'],
            ['lang' => 'en', 'xml:lang' => 'en']
        );
    }

    public function testPreserveAttributes()
    {
        $this->assertResult(
            ['src' => 'vert.png', 'lang' => 'fr'],
            ['src' => 'vert.png', 'lang' => 'fr', 'xml:lang' => 'fr']
        );
    }

    public function testCopyXMLLangToLang()
    {
        $this->assertResult(
            ['xml:lang' => 'en'],
            ['xml:lang' => 'en', 'lang' => 'en']
        );
    }

    public function testXMLLangOverridesLang()
    {
        $this->assertResult(
            ['lang' => 'fr', 'xml:lang' => 'de'],
            ['lang' => 'de', 'xml:lang' => 'de']
        );
    }

}

// vim: et sw=4 sts=4
