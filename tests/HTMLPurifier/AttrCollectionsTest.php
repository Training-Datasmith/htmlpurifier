<?php

declare(strict_types=1);

Mock::generatePartial(
    'HTMLPurifier_AttrCollections',
    'HTMLPurifier_AttrCollections_TestForConstruct',
    ['performInclusions', 'expandIdentifiers']
);

class HTMLPurifier_AttrCollectionsTest extends HTMLPurifier_Harness
{
    public function testConstruction()
    {
        generate_mock_once('HTMLPurifier_AttrTypes');

        $collections = new HTMLPurifier_AttrCollections_TestForConstruct();

        $types = new HTMLPurifier_AttrTypesMock();

        $modules = [];

        $modules['Module1'] = new HTMLPurifier_HTMLModule();
        $modules['Module1']->attr_collections = [
            'Core' => [
                0 => ['Soup', 'Undefined'],
                'attribute' => 'Type',
                'attribute-2' => 'Type2',
            ],
            'Soup' => [
                'attribute-3' => 'Type3-old', // overwritten
            ],
        ];

        $modules['Module2'] = new HTMLPurifier_HTMLModule();
        $modules['Module2']->attr_collections = [
            'Core' => [
                0 => ['Broccoli'],
            ],
            'Soup' => [
                'attribute-3' => 'Type3',
            ],
            'Broccoli' => [],
        ];

        $collections->doConstruct($types, $modules);
        // this is without identifier expansion or inclusions
        $this->assertIdentical(
            $collections->info,
            [
                'Core' => [
                    0 => ['Soup', 'Undefined', 'Broccoli'],
                    'attribute' => 'Type',
                    'attribute-2' => 'Type2',
                ],
                'Soup' => [
                    'attribute-3' => 'Type3',
                ],
                'Broccoli' => [],
            ]
        );

    }

    public function test_performInclusions()
    {
        generate_mock_once('HTMLPurifier_AttrTypes');

        $types = new HTMLPurifier_AttrTypesMock();
        $collections = new HTMLPurifier_AttrCollections($types, []);
        $collections->info = [
            'Core' => [0 => ['Inclusion', 'Undefined'], 'attr-original' => 'Type'],
            'Inclusion' => [0 => ['SubInclusion'], 'attr' => 'Type'],
            'SubInclusion' => ['attr2' => 'Type'],
        ];

        $collections->performInclusions($collections->info['Core']);
        $this->assertIdentical(
            $collections->info['Core'],
            [
                'attr-original' => 'Type',
                'attr' => 'Type',
                'attr2' => 'Type',
            ]
        );

        // test recursive
        $collections->info = [
            'One' => [0 => ['Two'], 'one' => 'Type'],
            'Two' => [0 => ['One'], 'two' => 'Type'],
        ];
        $collections->performInclusions($collections->info['One']);
        $this->assertIdentical(
            $collections->info['One'],
            [
                'one' => 'Type',
                'two' => 'Type',
            ]
        );

    }

    public function test_expandIdentifiers()
    {
        generate_mock_once('HTMLPurifier_AttrTypes');

        $types = new HTMLPurifier_AttrTypesMock();
        $collections = new HTMLPurifier_AttrCollections($types, []);

        $attr = [
            'attr1' => 'Color',
            'attr2*' => 'URI',
        ];
        $c_object = new HTMLPurifier_AttrDef_HTML_Color();
        $u_object = new HTMLPurifier_AttrDef_URI();

        $types->returns('get', $c_object, ['Color']);
        $types->returns('get', $u_object, ['URI']);

        $collections->expandIdentifiers($attr, $types);

        $u_object->required = true;
        $this->assertIdentical(
            $attr,
            [
                'attr1' => $c_object,
                'attr2' => $u_object,
            ]
        );

    }

}

// vim: et sw=4 sts=4
