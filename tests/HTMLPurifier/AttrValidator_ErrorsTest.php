<?php

declare(strict_types=1);

class HTMLPurifier_AttrValidator_ErrorsTest extends HTMLPurifier_ErrorsHarness
{
    /**
     * @type HTMLPurifier_Language
     */
    public $language;

    public function setup()
    {
        parent::setup();
        $config = HTMLPurifier_Config::createDefault();
        $this->language = HTMLPurifier_LanguageFactory::instance()->create($config, $this->context);
        $this->context->register('Locale', $this->language);
        $this->collector = new HTMLPurifier_ErrorCollector($this->context);
        $gen = new HTMLPurifier_Generator($config, $this->context);
        $this->context->register('Generator', $gen);
    }

    protected function invoke($input)
    {
        $validator = new HTMLPurifier_AttrValidator();
        $validator->validateToken($input, $this->config, $this->context);
    }

    public function testAttributesTransformedGlobalPre()
    {
        $def = $this->config->getHTMLDefinition(true);
        generate_mock_once('HTMLPurifier_AttrTransform');
        $transform = new HTMLPurifier_AttrTransformMock();
        $input = ['original' => 'value'];
        $output = ['class' => 'value']; // must be valid
        $transform->returns('transform', $output, [$input, new AnythingExpectation(), new AnythingExpectation()]);
        $def->info_attr_transform_pre[] = $transform;

        $token = new HTMLPurifier_Token_Start('span', $input, 1);
        $this->invoke($token);

        $result = $this->collector->getRaw();
        $expect = [
            [1, E_NOTICE, 'Attributes on <span> transformed from original to class', []],
        ];
        $this->assertIdentical($result, $expect);
    }

    public function testAttributesTransformedLocalPre()
    {
        $this->config->set('HTML.TidyLevel', 'heavy');
        $input = ['align' => 'right'];
        $output = ['style' => 'text-align:right;'];
        $token = new HTMLPurifier_Token_Start('p', $input, 1);
        $this->invoke($token);
        $result = $this->collector->getRaw();
        $expect = [
            [1, E_NOTICE, 'Attributes on <p> transformed from align to style', []],
        ];
        $this->assertIdentical($result, $expect);
    }

    // too lazy to check for global post and global pre

    public function testAttributeRemoved()
    {
        $token = new HTMLPurifier_Token_Start('p', ['foobar' => 'right'], 1);
        $this->invoke($token);
        $result = $this->collector->getRaw();
        $expect = [
            [1, E_ERROR, 'foobar attribute on <p> removed', []],
        ];
        $this->assertIdentical($result, $expect);
    }

}

// vim: et sw=4 sts=4
