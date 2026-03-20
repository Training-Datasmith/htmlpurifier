<?php

declare(strict_types=1);

class HTMLPurifier_ZipperTest extends HTMLPurifier_Harness
{
    public function testBasicNavigation()
    {
        list($z, $t) = HTMLPurifier_Zipper::fromArray([0,1,2,3]);
        $this->assertIdentical($t, 0);
        $t = $z->next($t);
        $this->assertIdentical($t, 1);
        $t = $z->prev($t);
        $this->assertIdentical($t, 0);
        $t = $z->advance($t, 2);
        $this->assertIdentical($t, 2);
        $t = $z->delete();
        $this->assertIdentical($t, 3);
        $z->insertBefore(4);
        $z->insertAfter(5);
        $this->assertIdentical($z->toArray($t), [0,1,4,3,5]);
        list($old, $t) = $z->splice($t, 2, [6,7]);
        $this->assertIdentical($old, [3,5]);
        $this->assertIdentical($t, 6);
        $this->assertIdentical($z->toArray($t), [0,1,4,6,7]);
    }

    // ToDo: QuickCheck style test comparing with array_splice
}
