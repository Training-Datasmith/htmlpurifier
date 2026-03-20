<?php

declare(strict_types=1);

class HTMLPurifier_StringHashTest extends UnitTestCase
{
    public function testUsed()
    {
        $hash = new HTMLPurifier_StringHash([
            'key' => 'value',
            'key2' => 'value2',
        ]);
        $this->assertIdentical($hash->getAccessed(), []);
        $t = $hash->offsetGet('key');
        $this->assertIdentical($hash->getAccessed(), ['key' => true]);
        $hash->resetAccessed();
        $this->assertIdentical($hash->getAccessed(), []);
    }

}

// vim: et sw=4 sts=4
