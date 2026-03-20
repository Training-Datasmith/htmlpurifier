<?php

declare(strict_types=1);

class HTMLPurifier_Strategy_MakeWellFormed_SkipInjector extends HTMLPurifier_Injector
{
    public $name = 'EndRewindInjector';
    public $needed = ['span'];
    public function handleElement(&$token)
    {
        $token = [clone $token, clone $token];
    }
}

// vim: et sw=4 sts=4
