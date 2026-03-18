<?php

declare(strict_types=1);

class HTMLPurifier_HTMLModuleTest extends HTMLPurifier_Harness
{
    public function test_addElementToContentSet()
    {
        $module = new HTMLPurifier_HTMLModule();

        $module->addElementToContentSet('b', 'Inline');
        $this->assertIdentical($module->content_sets, ['Inline' => 'b']);

        $module->addElementToContentSet('i', 'Inline');
        $this->assertIdentical($module->content_sets, ['Inline' => 'b | i']);

    }

    public function test_addElement()
    {
        $module = new HTMLPurifier_HTMLModule();
        $def = $module->addElement(
            'a',
            'Inline',
            'Optional: #PCDATA',
            ['Common'],
            [
                'href' => 'URI',
            ]
        );

        $module2 = new HTMLPurifier_HTMLModule();
        $def2 = new HTMLPurifier_ElementDef();
        $def2->content_model = '#PCDATA';
        $def2->content_model_type = 'optional';
        $def2->attr = [
            'href' => 'URI',
            0 => ['Common'],
        ];
        $module2->info['a'] = $def2;
        $module2->elements = ['a'];
        $module2->content_sets['Inline'] = 'a';

        $this->assertIdentical($module, $module2);
        $this->assertIdentical($def, $def2);
        $this->assertReference($def, $module->info['a']);

    }

    public function test_parseContents()
    {
        $module = new HTMLPurifier_HTMLModule();

        // pre-defined templates
        $this->assertIdentical(
            $module->parseContents('Inline'),
            ['optional', 'Inline | #PCDATA']
        );
        $this->assertIdentical(
            $module->parseContents('Flow'),
            ['optional', 'Flow | #PCDATA']
        );
        $this->assertIdentical(
            $module->parseContents('Empty'),
            ['empty', '']
        );

        // normalization procedures
        $this->assertIdentical(
            $module->parseContents('optional: a'),
            ['optional', 'a']
        );
        $this->assertIdentical(
            $module->parseContents('OPTIONAL :a'),
            ['optional', 'a']
        );
        $this->assertIdentical(
            $module->parseContents('Optional: a'),
            ['optional', 'a']
        );

        // others
        $this->assertIdentical(
            $module->parseContents('Optional: a | b | c'),
            ['optional', 'a | b | c']
        );

        // object pass-through
        generate_mock_once('HTMLPurifier_AttrDef');
        $this->assertIdentical(
            $module->parseContents(new HTMLPurifier_AttrDefMock()),
            [null, null]
        );

    }

    public function test_mergeInAttrIncludes()
    {
        $module = new HTMLPurifier_HTMLModule();

        $attr = [];
        $module->mergeInAttrIncludes($attr, 'Common');
        $this->assertIdentical($attr, [0 => ['Common']]);

        $attr = ['a' => 'b'];
        $module->mergeInAttrIncludes($attr, ['Common', 'Good']);
        $this->assertIdentical($attr, ['a' => 'b', 0 => ['Common', 'Good']]);

    }

    public function test_addBlankElement()
    {
        $module = new HTMLPurifier_HTMLModule();
        $def = $module->addBlankElement('a');

        $def2 = new HTMLPurifier_ElementDef();
        $def2->standalone = false;

        $this->assertReference($module->info['a'], $def);
        $this->assertIdentical($def, $def2);

    }

    public function test_makeLookup()
    {
        $module = new HTMLPurifier_HTMLModule();

        $this->assertIdentical(
            $module->makeLookup('foo'),
            ['foo' => true]
        );
        $this->assertIdentical(
            $module->makeLookup(['foo']),
            ['foo' => true]
        );

        $this->assertIdentical(
            $module->makeLookup('foo', 'two'),
            ['foo' => true, 'two' => true]
        );
        $this->assertIdentical(
            $module->makeLookup(['foo', 'two']),
            ['foo' => true, 'two' => true]
        );

    }

}

// vim: et sw=4 sts=4
