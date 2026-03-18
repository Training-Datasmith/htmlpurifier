<?php

declare(strict_types=1);

class HTMLPurifier_AttrTransform_NameSyncTest extends HTMLPurifier_AttrTransformHarness
{
    /**
     * @type HTMLPurifier_IDAccumulator
     */
    public $accumulator;

    public function setUp()
    {
        parent::setUp();
        $this->obj = new HTMLPurifier_AttrTransform_NameSync();
        $this->accumulator = new HTMLPurifier_IDAccumulator();
        $this->context->register('IDAccumulator', $this->accumulator);
        $this->config->set('Attr.EnableID', true);
    }

    public function testEmpty()
    {
        $this->assertResult([]);
    }

    public function testAllowSame()
    {
        $this->assertResult(
            ['name' => 'free', 'id' => 'free']
        );
    }

    public function testAllowDifferent()
    {
        $this->assertResult(
            ['name' => 'tryit', 'id' => 'thisgood']
        );
    }

    public function testCheckName()
    {
        $this->accumulator->add('notok');
        $this->assertResult(
            ['name' => 'notok', 'id' => 'ok'],
            ['id' => 'ok']
        );
    }

}

// vim: et sw=4 sts=4
