<?php
/**
 * GeChiUI Version
 *
 * Contains version information for the current GeChiUI release.
 *
 * @package GeChiUI
 *
 */

/**
 * The GeChiUI version string.
 *
 * Holds the current version number for GeChiUI core. Used to bust caches
 * and to enable development mode for scripts when running from the /src directory.
 * 开发版本使用 alpha|beta|RC， 如 6.0-alpha-52448-src
 *
 * @global string $gc_version
 */
$gc_version = '6.0.6';

/**
 * Holds the GeChiUI DB revision, increments when changes are made to the GeChiUI DB schema.
 *
 * @global int $gc_db_version
 */
$gc_db_version = 51917;

/**
 * Holds the TinyMCE version.
 *
 * @global string $tinymce_version
 */
$tinymce_version = '49110-20201110';

/**
 * Holds the required PHP version.
 *
 * @global string $required_php_version
 */
$required_php_version = '5.6.20';

/**
 * Holds the required MySQL version.
 *
 * @global string $required_mysql_version
 */
$required_mysql_version = '5.0';