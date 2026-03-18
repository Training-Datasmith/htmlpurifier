<?php

declare(strict_types=1);

Mock::generatePartial(
    'HTMLPurifier_HTMLModule_Tidy',
    'HTMLPurifier_HTMLModule_Tidy_TestForConstruct',
    ['makeFixes', 'makeFixesForLevel', 'populate']
);

class HTMLPurifier_HTMLModule_TidyTest extends HTMLPurifier_Harness
{
    public function test_getFixesForLevel()
    {
        $module = new HTMLPurifier_HTMLModule_Tidy();
        $module->fixesForLevel['light'][]  = 'light-fix';
        $module->fixesForLevel['medium'][] = 'medium-fix';
        $module->fixesForLevel['heavy'][]  = 'heavy-fix';

        $this->assertIdentical(
            [],
            $module->getFixesForLevel('none')
        );
        $this->assertIdentical(
            ['light-fix' => true],
            $module->getFixesForLevel('light')
        );
        $this->assertIdentical(
            ['light-fix' => true, 'medium-fix' => true],
            $module->getFixesForLevel('medium')
        );
        $this->assertIdentical(
            ['light-fix' => true, 'medium-fix' => true, 'heavy-fix' => true],
            $module->getFixesForLevel('heavy')
        );

        $this->expectError('Tidy level turbo not recognized');
        $module->getFixesForLevel('turbo');

    }

    public function test_setup()
    {
        $i = 0; // counter, helps us isolate expectations

        // initialize partial mock
        $module = new HTMLPurifier_HTMLModule_Tidy_TestForConstruct();
        $module->fixesForLevel['light']  = ['light-fix-1', 'light-fix-2'];
        $module->fixesForLevel['medium'] = ['medium-fix-1', 'medium-fix-2'];
        $module->fixesForLevel['heavy']  = ['heavy-fix-1', 'heavy-fix-2'];

        $j = 0;
        $fixes = [
            'light-fix-1'  => $lf1 = $j++,
            'light-fix-2'  => $lf2 = $j++,
            'medium-fix-1' => $mf1 = $j++,
            'medium-fix-2' => $mf2 = $j++,
            'heavy-fix-1'  => $hf1 = $j++,
            'heavy-fix-2'  => $hf2 = $j++,
        ];
        $module->returns('makeFixes', $fixes);

        $config = HTMLPurifier_Config::create([
            'HTML.TidyLevel' => 'none',
        ]);
        $module->expectAt($i++, 'populate', [[]]);
        $module->setup($config);

        // basic levels

        $config = HTMLPurifier_Config::create([
            'HTML.TidyLevel' => 'light',
        ]);
        $module->expectAt($i++, 'populate', [[
            'light-fix-1' => $lf1,
            'light-fix-2' => $lf2,
        ]]);
        $module->setup($config);

        $config = HTMLPurifier_Config::create([
            'HTML.TidyLevel' => 'heavy',
        ]);
        $module->expectAt($i++, 'populate', [[
            'light-fix-1'  => $lf1,
            'light-fix-2'  => $lf2,
            'medium-fix-1' => $mf1,
            'medium-fix-2' => $mf2,
            'heavy-fix-1'  => $hf1,
            'heavy-fix-2'  => $hf2,
        ]]);
        $module->setup($config);

        // fine grained tuning

        $config = HTMLPurifier_Config::create([
            'HTML.TidyLevel' => 'none',
            'HTML.TidyAdd'   => ['light-fix-1', 'medium-fix-1'],
        ]);
        $module->expectAt($i++, 'populate', [[
            'light-fix-1' => $lf1,
            'medium-fix-1' => $mf1,
        ]]);
        $module->setup($config);

        $config = HTMLPurifier_Config::create([
            'HTML.TidyLevel' => 'medium',
            'HTML.TidyRemove'   => ['light-fix-1', 'medium-fix-1'],
        ]);
        $module->expectAt($i++, 'populate', [[
            'light-fix-2' => $lf2,
            'medium-fix-2' => $mf2,
        ]]);
        $module->setup($config);

    }

    public function test_makeFixesForLevel()
    {
        $module = new HTMLPurifier_HTMLModule_Tidy();
        $module->defaultLevel = 'heavy';

        $module->makeFixesForLevel([
            'fix-1' => 0,
            'fix-2' => 1,
            'fix-3' => 2,
        ]);

        $this->assertIdentical($module->fixesForLevel['heavy'], ['fix-1', 'fix-2', 'fix-3']);
        $this->assertIdentical($module->fixesForLevel['medium'], []);
        $this->assertIdentical($module->fixesForLevel['light'], []);

    }
    public function test_makeFixesForLevel_undefinedLevel()
    {
        $module = new HTMLPurifier_HTMLModule_Tidy();
        $module->defaultLevel = 'bananas';

        $this->expectException(new Exception('Default level bananas does not exist'));

        $module->makeFixesForLevel([
            'fix-1' => 0,
        ]);

    }

    public function test_getFixType()
    {
        // syntax needs documenting

        $module = new HTMLPurifier_HTMLModule_Tidy();

        $this->assertIdentical(
            $module->getFixType('a'),
            ['tag_transform', ['element' => 'a']]
        );

        $this->assertIdentical(
            $module->getFixType('a@href'),
            $reuse = ['attr_transform_pre', ['element' => 'a', 'attr' => 'href']]
        );

        $this->assertIdentical(
            $module->getFixType('a@href#pre'),
            $reuse
        );

        $this->assertIdentical(
            $module->getFixType('a@href#post'),
            ['attr_transform_post', ['element' => 'a', 'attr' => 'href']]
        );

        $this->assertIdentical(
            $module->getFixType('xml:foo@xml:bar'),
            ['attr_transform_pre', ['element' => 'xml:foo', 'attr' => 'xml:bar']]
        );

        $this->assertIdentical(
            $module->getFixType('blockquote#child'),
            ['child', ['element' => 'blockquote']]
        );

        $this->assertIdentical(
            $module->getFixType('@lang'),
            ['attr_transform_pre', ['attr' => 'lang']]
        );

        $this->assertIdentical(
            $module->getFixType('@lang#post'),
            ['attr_transform_post', ['attr' => 'lang']]
        );

    }

    public function test_populate()
    {
        $i = 0;

        $module = new HTMLPurifier_HTMLModule_Tidy();
        $module->populate([
            'element' => $element = $i++,
            'element@attr' => $attr = $i++,
            'element@attr#post' => $attr_post = $i++,
            'element#child' => $child = $i++,
            'element#content_model_type' => $content_model_type = $i++,
            '@attr' => $global_attr = $i++,
            '@attr#post' => $global_attr_post = $i++,
        ]);

        $module2 = new HTMLPurifier_HTMLModule_Tidy();
        $e = $module2->addBlankElement('element');
        $e->attr_transform_pre['attr'] = $attr;
        $e->attr_transform_post['attr'] = $attr_post;
        $e->child = $child;
        $e->content_model_type = $content_model_type;
        $module2->info_tag_transform['element'] = $element;
        $module2->info_attr_transform_pre['attr'] = $global_attr;
        $module2->info_attr_transform_post['attr'] = $global_attr_post;

        $this->assertEqual($module, $module2);

    }

}

// vim: et sw=4 sts=4
