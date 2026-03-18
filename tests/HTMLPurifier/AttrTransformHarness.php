<?php

declare(strict_types=1);

class HTMLPurifier_AttrTransformHarness extends HTMLPurifier_ComplexHarness
{
    public function setUp()
    {
        parent::setUp();
        $this->func = 'transform';
    }

}

// vim: et sw=4 sts=4
