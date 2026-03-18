#!/usr/bin/php
<?php

declare(strict_types=1);

chdir(__DIR__);
require_once 'common.php';
assertCli();

/**
 * @file
 * Adds vimline to files
 */

chdir(__DIR__ . '/..');
$FS = new FSTools();

$vimline = 'vim: et sw=4 sts=4';

$files = $FS->globr('.', '*');
foreach ($files as $file) {
    if (!is_file($file)) {
        continue;
    }
    if (prefix_is('./docs/doxygen', $file)) {
        continue;
    }
    if (prefix_is('./library/standalone', $file)) {
        continue;
    }
    if (prefix_is('./docs/specimens', $file)) {
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
    if (postfix_is('.png', $file)) {
        continue;
    }
    if (postfix_is('.ico', $file)) {
        continue;
    }
    if (postfix_is('.vtest', $file)) {
        continue;
    }
    if (postfix_is('.svg', $file)) {
        continue;
    }
    if (postfix_is('.phpt', $file)) {
        continue;
    }
    if (postfix_is('VERSION', $file)) {
        continue;
    }
    if (postfix_is('configdoc/usage.xml', $file)) {
        continue;
    }
    if (postfix_is('library/HTMLPurifier.includes.php', $file)) {
        continue;
    }
    if (postfix_is('library/HTMLPurifier.safe-includes.php', $file)) {
        continue;
    }
    if (postfix_is('smoketests/xssAttacks.xml', $file)) {
        continue;
    }
    if (postfix_is('.diff', $file)) {
        continue;
    }
    if (postfix_is('.exp', $file)) {
        continue;
    }
    if (postfix_is('.log', $file)) {
        continue;
    }
    if (postfix_is('.out', $file)) {
        continue;
    }
    if ($file == './library/HTMLPurifier/Lexer/PH5P.php') {
        continue;
    }
    if ($file == './maintenance/PH5P.php') {
        continue;
    }
    $ext = strrchr($file, '.');
    if (
        postfix_is('README', $file) ||
        postfix_is('LICENSE', $file) ||
        postfix_is('CREDITS', $file) ||
        postfix_is('INSTALL', $file) ||
        postfix_is('NEWS', $file) ||
        postfix_is('TODO', $file) ||
        postfix_is('WYSIWYG', $file) ||
        postfix_is('Changelog', $file)
    ) {
        $ext = '.txt';
    }
    if (postfix_is('Doxyfile', $file)) {
        $ext = 'Doxyfile';
    }
    if (postfix_is('.php.in', $file)) {
        $ext = '.php';
    }
    $no_nl = false;
    switch ($ext) {
        case '.php':
        case '.inc':
        case '.js':
            $line = '// %s';
            break;
        case '.html':
        case '.xsl':
        case '.xml':
        case '.htc':
            $line = "<!-- %s\n-->";
            break;
        case '.htmlt':
            $no_nl = true;
            $line = '--# %s';
            break;
        case '.ini':
            $line = '; %s';
            break;
        case '.css':
            $line = '/* %s */';
            break;
        case '.bat':
            $line = 'rem %s';
            break;
        case '.txt':
        case '.utf8':
            if (
                prefix_is('./library/HTMLPurifier/ConfigSchema', $file) ||
                prefix_is('./smoketests/test-schema', $file) ||
                prefix_is('./tests/HTMLPurifier/StringHashParser', $file)
            ) {
                $no_nl = true;
                $line = '--# %s';
            } else {
                $line = '    %s';
            }
            break;
        case 'Doxyfile':
            $line = '# %s';
            break;
        default:
            throw new Exception('Unknown file: ' . $file);
    }

    echo "$file\n";
    $contents = file_get_contents($file);

    $regex = '~' . str_replace('%s', 'vim: .+', preg_quote($line, '~')) .  '~m';
    $contents = preg_replace($regex, '', $contents);

    $contents = rtrim($contents);

    if (strpos($contents, "\r\n") !== false) {
        $nl = "\r\n";
    } elseif (strpos($contents, "\n") !== false) {
        $nl = "\n";
    } elseif (strpos($contents, "\r") !== false) {
        $nl = "\r";
    } else {
        $nl = PHP_EOL;
    }

    if (!$no_nl) {
        $contents .= $nl;
    }
    $contents .= $nl . str_replace('%s', $vimline, $line) . $nl;

    file_put_contents($file, $contents);

}

// vim: et sw=4 sts=4
