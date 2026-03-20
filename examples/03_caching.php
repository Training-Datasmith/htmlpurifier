<?php

declare(strict_types=1);

/**
 * Example: Enabling filesystem caching for faster repeated purification.
 *
 * HTMLPurifier parses its HTML definition on every instantiation unless a
 * cache is configured.  In production, caching the serialised definition to
 * disk can reduce per-request overhead significantly.
 *
 * Security notes:
 *  - The cache directory must be writable by the web server but should NOT
 *    be publicly accessible via HTTP — protect it with .htaccess or place it
 *    outside the document root.
 *  - Set a Cache.DefinitionImpl of 'Null' during development so stale cached
 *    definitions do not mask configuration changes.
 *  - Never store user input in the cache directory path; use a fixed path.
 *
 * @security Do not use a world-writable or publicly served directory as the
 *           cache path — cached definitions contain serialised PHP objects.
 */

require_once __DIR__ . '/../library/HTMLPurifier.auto.php';

$cache_dir = sys_get_temp_dir() . '/htmlpurifier_cache';
if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0750, true);
}

$config = HTMLPurifier_Config::createDefault();
$config->set('HTML.Allowed', 'p,strong,em,a[href],ul,li');
$config->set('URI.AllowedSchemes', ['https' => true]);

// Enable the serialiser cache (Serializer is the default; shown explicitly).
$config->set('Cache.DefinitionImpl', 'Serializer');
$config->set('Cache.SerializerPath', $cache_dir);

// Pin a definition revision so stale cache is invalidated when config changes.
$config->set('Cache.SerializerPermissions', 0640);

$purifier = new HTMLPurifier($config);

$html  = '<p>Hello <strong>world</strong>! <a href="https://example.com">Link</a></p>';
$clean = $purifier->purify($html);

echo $clean . PHP_EOL;
