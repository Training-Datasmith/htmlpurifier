<?php

declare(strict_types=1);

class HTMLPurifier_VarParser_FlexibleTest extends HTMLPurifier_VarParserHarness
{
    public function testValidate()
    {
        $this->assertValid('foobar', 'string');
        $this->assertValid('foobar', 'text');
        $this->assertValid('FOOBAR', 'istring', 'foobar');
        $this->assertValid('FOOBAR', 'itext', 'foobar');

        $this->assertValid(34, 'int');

        $this->assertValid(3.34, 'float');

        $this->assertValid(false, 'bool');
        $this->assertValid(0, 'bool', false);
        $this->assertValid(1, 'bool', true);
        $this->assertValid('true', 'bool', true);
        $this->assertValid('false', 'bool', false);
        $this->assertValid('1', 'bool', true);
        $this->assertInvalid(34, 'bool');
        $this->assertInvalid(null, 'bool');

        $this->assertValid(['1', '2', '3'], 'list');
        $this->assertValid('foo,bar, cow', 'list', ['foo', 'bar', 'cow']);
        $this->assertValid('', 'list', []);
        $this->assertValid("foo\nbar", 'list', ['foo', 'bar']);
        $this->assertValid("foo\nbar,baz", 'list', ['foo', 'bar', 'baz']);

        $this->assertValid(['1' => true, '2' => true], 'lookup');
        $this->assertValid(['1', '2'], 'lookup', ['1' => true, '2' => true]);
        $this->assertValid('foo,bar', 'lookup', ['foo' => true, 'bar' => true]);
        $this->assertValid("foo\nbar", 'lookup', ['foo' => true, 'bar' => true]);
        $this->assertValid("foo\nbar,baz", 'lookup', ['foo' => true, 'bar' => true, 'baz' => true]);
        $this->assertValid('', 'lookup', []);
        $this->assertValid([], 'lookup');

        $this->assertValid(['foo' => 'bar'], 'hash');
        $this->assertValid([1 => 'moo'], 'hash');
        $this->assertInvalid([0 => 'moo'], 'hash');
        $this->assertValid('', 'hash', []);
        $this->assertValid('foo:bar,too:two', 'hash', ['foo' => 'bar', 'too' => 'two']);
        $this->assertValid("foo:bar\ntoo:two,three:free", 'hash', ['foo' => 'bar', 'too' => 'two', 'three' => 'free']);
        $this->assertValid('foo:bar,too', 'hash', ['foo' => 'bar']);
        $this->assertValid('foo:bar,', 'hash', ['foo' => 'bar']);
        $this->assertValid('foo:bar:baz', 'hash', ['foo' => 'bar:baz']);

        $this->assertValid(23, 'mixed');

    }

    public function testValidate_withMagicNumbers()
    {
        $this->assertValid('foobar', HTMLPurifier_VarParser::C_STRING);
    }

    public function testValidate_null()
    {
        $this->assertIdentical($this->parser->parse(null, 'string', true), null);
        $this->expectException('HTMLPurifier_VarParserException');
        $this->parser->parse(null, 'string', false);
    }

}

// vim: et sw=4 sts=4
