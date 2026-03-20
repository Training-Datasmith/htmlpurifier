<?php

declare(strict_types=1);

/**
 * Example: Restricting to a custom subset of HTML elements and attributes.
 *
 * HTML.Allowed accepts a comma-separated list of elements, optionally followed
 * by a bracketed attribute list.  This is the simplest way to tighten the
 * whitelist beyond the default.
 *
 * Security notes:
 *  - Prefer explicit allowlists (HTML.Allowed) over implicit denylists.
 *  - Setting URI.AllowedSchemes limits which URL schemes are accepted in href
 *    and src attributes; this prevents javascript:, data:, and vbscript: even
 *    if they somehow bypass element filtering.
 *  - Enabling the AutoParagraph injector can change the structure of input;
 *    test thoroughly if you are round-tripping content through the purifier.
 *
 * @security URI scheme validation is an independent layer from element/attribute
 *           filtering.  Both must pass for an attribute to survive purification.
 */

require_once __DIR__ . '/../library/HTMLPurifier.auto.php';

$config = HTMLPurifier_Config::createDefault();

// Only allow a handful of safe inline/block elements.
$config->set('HTML.Allowed', 'p,br,strong,em,a[href],ul,ol,li,blockquote');

// Only allow safe URL schemes in href attributes.
$config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);

// Automatically wrap bare text nodes in <p> tags.
$config->set('AutoFormat.AutoParagraph', true);

$purifier = new HTMLPurifier($config);

$dirty = '<h1>Title</h1><p>Visit <a href="https://example.com">example</a> or '
       . '<a href="javascript:void(0)">this link</a>.</p>'
       . '<table><tr><td>Table content (stripped)</td></tr></table>'
       . '<script>alert(1)</script>';

$clean = $purifier->purify($dirty);

echo "Clean output:\n" . $clean . "\n";
