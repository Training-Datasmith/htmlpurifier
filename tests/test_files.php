<?php

declare(strict_types=1);

if (!defined('HTMLPurifierTest')) {
    exit;
}

// These arrays are defined by this file and can be relied upon.
$test_files = [];
$test_dirs = [];
$test_dirs_exclude = [];
$vtest_dirs = [];
$htmlt_dirs = [];
$phpt_dirs  = [];

$break = true;
switch ($AC['type']) {
    case '':
        $break = false;
        // no break
    case 'htmlpurifier':
        $test_dirs[] = 'HTMLPurifier';
        $test_files[] = 'HTMLPurifierTest.php';
        $test_dirs_exclude['HTMLPurifier/Filter/ExtractStyleBlocksTest.php'] = true;
        $test_files[] = 'HTMLPurifier/Filter/ExtractStyleBlocksTest.php';
        if ($break) {
            break;
        }
        // no break
    case 'configdoc':
        if (version_compare(PHP_VERSION, '5.2', '>=')) {
            // $test_dirs[] = 'ConfigDoc'; // no test files currently!
        }
        if ($break) {
            break;
        }
        // no break
    case 'fstools':
        $test_dirs[] = 'FSTools';
        if ($break) {
            break;
        }
        // no break
    case 'htmlt':
        $htmlt_dirs[] = 'HTMLPurifier/HTMLT';
        if ($break) {
            break;
        }
        // no break
    case 'vtest':
        $vtest_dirs[] = 'HTMLPurifier/ConfigSchema/Validator';
        if ($break) {
            break;
        }

        // no break
    case 'phpt':
        if (!$AC['disable-phpt'] && version_compare(PHP_VERSION, '5.2', '>=')) {
            $phpt_dirs[] = 'HTMLPurifier/PHPT';
        }
}

// vim: et sw=4 sts=4
