<?php

declare(strict_types=1);

/**
 * Example: Basic HTML purification with the default configuration.
 *
 * HTMLPurifier strips all elements and attributes not in the HTML 4.01
 * Transitional whitelist by default.  XSS vectors — inline event handlers,
 * javascript: URIs, <script> tags, <object> tags, style-based attacks — are
 * all removed automatically.
 *
 * Security notes:
 *  - Always purify user-supplied HTML before storing OR before rendering,
 *    depending on your trust model.  Purifying at display time is safer.
 *  - Do NOT use HTML.Trusted = true for user content; it weakens the filter.
 *  - The default configuration disallows <script>, <object>, <embed>, and
 *    all inline event handlers (onclick, onerror, …).
 *  - javascript: and vbscript: URI schemes are blocked even inside href/src.
 *  - CSS is stripped by default; enable it only via CSS.Allowed if required.
 */

require_once __DIR__ . '/../library/HTMLPurifier.auto.php';

$config   = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

$dirty_html = <<<'HTML'
<p>Hello <strong>world</strong>!</p>
<script>alert('XSS')</script>
<a href="javascript:alert('XSS')">Click me</a>
<img src="x" onerror="alert('XSS')">
<p style="background:url(javascript:alert('XSS'))">styled</p>
<iframe src="https://evil.example.com"></iframe>
HTML;

$clean_html = $purifier->purify($dirty_html);

echo "Input:\n" . $dirty_html . "\n\n";
echo "Output:\n" . $clean_html . "\n";
