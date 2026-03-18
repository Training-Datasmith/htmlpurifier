<?php

declare(strict_types=1);

Mock::generatePartial(
    'HTMLPurifier_AttrTransform',
    'HTMLPurifier_AttrTransformTestable',
    ['transform']
);

class HTMLPurifier_AttrTransformTest extends HTMLPurifier_Harness
{
    public function test_prependCSS()
    {
        $t = new HTMLPurifier_AttrTransformTestable();

        $attr = [];
        $t->prependCSS($attr, 'style:new;');
        $this->assertIdentical(['style' => 'style:new;'], $attr);

        $attr = ['style' => 'style:original;'];
        $t->prependCSS($attr, 'style:new;');
        $this->assertIdentical(['style' => 'style:new;style:original;'], $attr);

        $attr = ['style' => 'style:original;', 'misc' => 'un-related'];
        $t->prependCSS($attr, 'style:new;');
        $this->assertIdentical(['style' => 'style:new;style:original;', 'misc' => 'un-related'], $attr);

    }

    public function test_confiscateAttr()
    {
        $t = new HTMLPurifier_AttrTransformTestable();

        $attr = ['flavor' => 'sweet'];
        $this->assertIdentical('sweet', $t->confiscateAttr($attr, 'flavor'));
        $this->assertIdentical([], $attr);

        $attr = ['flavor' => 'sweet'];
        $this->assertIdentical(null, $t->confiscateAttr($attr, 'color'));
        $this->assertIdentical(['flavor' => 'sweet'], $attr);

    }

}

// vim: et sw=4 sts=4
