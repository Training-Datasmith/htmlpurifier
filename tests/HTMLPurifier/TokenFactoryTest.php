<?php

declare(strict_types=1);

class HTMLPurifier_TokenFactoryTest extends HTMLPurifier_Harness
{
    public function test()
    {
        $factory = new HTMLPurifier_TokenFactory();

        $regular = new HTMLPurifier_Token_Start('a', ['href' => 'about:blank']);
        $generated = $factory->createStart('a', ['href' => 'about:blank']);

        $this->assertIdentical($regular, $generated);

    }
}

// vim: et sw=4 sts=4
