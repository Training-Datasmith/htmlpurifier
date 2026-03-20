<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Security boundary tests for HTMLPurifier's XSS filtering.
 *
 * These tests validate that common XSS vectors are reliably stripped by the
 * default configuration.  They serve as regression tests ensuring that future
 * changes to the purification pipeline do not inadvertently allow known vectors.
 *
 * Each test method name describes the attack vector being prevented.
 */
class HTMLPurifier_Security_XssVectorTest extends TestCase
{
    private HTMLPurifier $purifier;
    private HTMLPurifier_Config $config;

    protected function setUp(): void
    {
        $this->config   = HTMLPurifier_Config::createDefault();
        $this->purifier = new HTMLPurifier($this->config);
    }

    /**
     * Inline <script> tags must be completely removed from the output.
     * Validates that RemoveForeignElements drops the script element and its content.
     */
    public function test_script_tag_removed(): void
    {
        $input  = '<p>Hello</p><script>alert("XSS")</script>';
        $output = $this->purifier->purify($input);

        $this->assertStringNotContainsString('<script', $output);
        $this->assertStringNotContainsString('alert', $output);
    }

    /**
     * javascript: URI in an href attribute must be stripped.
     * Validates that the URI attribute validator rejects the javascript: scheme.
     */
    public function test_javascript_uri_in_href_removed(): void
    {
        $input  = '<a href="javascript:alert(1)">click</a>';
        $output = $this->purifier->purify($input);

        $this->assertStringNotContainsString('javascript:', $output);
    }

    /**
     * Inline event handlers (onclick, onerror, onload, etc.) must be stripped.
     * Validates that ValidateAttributes removes attributes not in the allowlist.
     */
    public function test_inline_event_handlers_removed(): void
    {
        $input  = '<img src="x" onerror="alert(1)" onload="evil()" onclick="bad()">';
        $output = $this->purifier->purify($input);

        $this->assertStringNotContainsString('onerror', $output);
        $this->assertStringNotContainsString('onload', $output);
        $this->assertStringNotContainsString('onclick', $output);
    }

    /**
     * CSS expression() injection via the style attribute must be blocked.
     * Validates that CSS attribute validation strips expression() calls.
     */
    public function test_css_expression_in_style_removed(): void
    {
        $input  = '<p style="background:expression(alert(1))">text</p>';
        $output = $this->purifier->purify($input);

        $this->assertStringNotContainsString('expression', $output);
        $this->assertStringNotContainsString('alert', $output);
    }

    /**
     * vbscript: URI must be rejected by the URI scheme validator.
     */
    public function test_vbscript_uri_blocked(): void
    {
        $input  = '<a href="vbscript:msgbox(1)">link</a>';
        $output = $this->purifier->purify($input);

        $this->assertStringNotContainsString('vbscript:', $output);
    }

    /**
     * An <iframe> tag is not in the default whitelist and must be removed.
     */
    public function test_iframe_removed_by_default(): void
    {
        $input  = '<iframe src="https://evil.example.com"></iframe>';
        $output = $this->purifier->purify($input);

        $this->assertStringNotContainsString('<iframe', $output);
    }

    /**
     * HTML comments are stripped in untrusted mode (HTML.Trusted = false, default).
     * This prevents conditional-comment attacks in IE and comment-injection vectors.
     */
    public function test_html_comments_stripped_in_untrusted_mode(): void
    {
        $input  = '<!-- <script>alert(1)</script> --><p>content</p>';
        $output = $this->purifier->purify($input);

        $this->assertStringNotContainsString('<!--', $output);
        $this->assertStringNotContainsString('-->', $output);
    }

    /**
     * data: URIs in img src must be blocked by the default URI scheme whitelist.
     * This prevents data-URI exfiltration and polyglot file injection.
     */
    public function test_data_uri_in_img_src_blocked(): void
    {
        $input  = '<img src="data:image/svg+xml,<svg onload=alert(1)>">';
        $output = $this->purifier->purify($input);

        $this->assertStringNotContainsString('data:', $output);
    }

    /**
     * Safe, well-formed HTML must pass through unchanged.
     * Validates that the purifier does not over-strip legitimate content.
     */
    public function test_safe_html_passes_through(): void
    {
        $input  = '<p>Hello <strong>world</strong>! <a href="https://example.com">Link</a></p>';
        $output = $this->purifier->purify($input);

        $this->assertStringContainsString('<p>', $output);
        $this->assertStringContainsString('<strong>', $output);
        $this->assertStringContainsString('https://example.com', $output);
    }
}
