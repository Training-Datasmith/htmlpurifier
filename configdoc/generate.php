<?php

error_reporting(E_ALL);

// load dual-libraries
require_once __DIR__ . '/../extras/HTMLPurifierExtras.auto.php';
require_once __DIR__ . '/../library/HTMLPurifier.auto.php';

// setup HTML Purifier singleton
HTMLPurifier::getInstance([
    'AutoFormat.PurifierLinkify' => true
]);

$builder = new HTMLPurifier_ConfigSchema_InterchangeBuilder();
$interchange = new HTMLPurifier_ConfigSchema_Interchange();
$builder->buildDir($interchange);
$loader = __DIR__ . '/../config-schema.php';
if (file_exists($loader)) include $loader;
$interchange->validate();

$style = 'plain'; // use $_GET in the future, careful to validate!
$configdoc_xml = __DIR__ . '/configdoc.xml';

$xml_builder = new HTMLPurifier_ConfigSchema_Builder_Xml();
$xml_builder->openURI($configdoc_xml);
$xml_builder->build($interchange);
unset($xml_builder); // free handle

$xslt = new ConfigDoc_HTMLXSLTProcessor();
$xslt->importStylesheet(__DIR__ . "/styles/$style.xsl");
$output = $xslt->transformToHTML($configdoc_xml);

if (!$output) {
    echo "Error in generating files\n";
    exit(1);
}

// write out
file_put_contents(__DIR__ . "/$style.html", $output);

if (php_sapi_name() != 'cli') {
    // output (instant feedback if it's a browser)
    echo $output;
} else {
    echo "Files generated successfully.\n";
}

// vim: et sw=4 sts=4
