<?php

declare(strict_types=1);

class HTMLPurifier_DefinitionCache_DecoratorTest extends HTMLPurifier_DefinitionCacheHarness
{
    public function test()
    {
        generate_mock_once('HTMLPurifier_DefinitionCache');
        $mock = new HTMLPurifier_DefinitionCacheMock();
        $mock->type = 'Test';

        $cache = new HTMLPurifier_DefinitionCache_Decorator();
        $cache = $cache->decorate($mock);

        $this->assertIdentical($cache->type, $mock->type);

        $def = $this->generateDefinition();
        $config = $this->generateConfigMock();

        $mock->expectOnce('add', [$def, $config]);
        $cache->add($def, $config);

        $mock->expectOnce('set', [$def, $config]);
        $cache->set($def, $config);

        $mock->expectOnce('replace', [$def, $config]);
        $cache->replace($def, $config);

        $mock->expectOnce('get', [$config]);
        $cache->get($config);

        $mock->expectOnce('flush', [$config]);
        $cache->flush($config);

        $mock->expectOnce('cleanup', [$config]);
        $cache->cleanup($config);

    }

}

// vim: et sw=4 sts=4
