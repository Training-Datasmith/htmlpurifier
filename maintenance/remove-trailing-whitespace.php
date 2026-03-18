#!/usr/bin/php
<?php

chdir(__DIR__);
require_once 'common.php';
assertCli();

/**
 * @file
 * Removes trailing whitespace from files.
 */

chdir(__DIR__ . '/..');
$FS = new FSTools();

$files = $FS->globr('.', '{,.}*', GLOB_BRACE);
foreach ($files as $file) {
    if (!is_file($file)) {
        continue;
    }
    if (prefix_is('./.git', $file)) {
        continue;
    }
    if (prefix_is('./docs/doxygen', $file)) {
        continue;
    }
    if (postfix_is('.ser', $file)) {
        continue;
    }
    if (postfix_is('.tgz', $file)) {
        continue;
    }
    if (postfix_is('.patch', $file)) {
        continue;
    }
    if (postfix_is('.dtd', $file)) {
        continue;
    }
    if (postfix_is('.ent', $file)) {
        continue;
    }
    if ($file == './library/HTMLPurifier/Lexer/PH5P.php') {
        continue;
    }
    if ($file == './maintenance/PH5P.php') {
        continue;
    }
    $contents = file_get_contents($file);
    $result = preg_replace('/^(.*?)[ \t]+(\r?)$/m', '\1\2', $contents, -1, $count);
    if (!$count) continue;
    echo "$file\n";
    file_put_contents($file, $result);
}

// vim: et sw=4 sts=4
