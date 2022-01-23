<?php
/*
Plugin name: Entity viewer
Plugin URI: https://github.com/versusbassz/entity-viewer/
Description: Displays properties and custom fields of WordPress entities (posts, users, terms, comments) for debugging/development purposes.
Version: 0.4.0
Requires at least: 5.7.0
Requires PHP: 7.4
Text Domain: entity-viewer

Author: Vladimir Sklyar
Author URI: https://versusbassz.com/

License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

use Versusbassz\EntityViewer\Plugin;

defined('ABSPATH') || exit;

define('ENTITY_VIEWER_PLUGIN_VERSION',  '0.4.0');
define('ENTITY_VIEWER_ENTRY_FILE_PATH', __FILE__);
define('ENTITY_VIEWER_MIN_PHP_VERSION', '7.4.0');
define('ENTITY_VIEWER_MIN_WP_VERSION', '5.7.0');

entview_start_plugin();

function entview_start_plugin() {
    if (! apply_filters('entview/plugin_enabled', true)) {
        return;
    }

    $supported_php_version = version_compare(PHP_VERSION, ENTITY_VIEWER_MIN_PHP_VERSION, '>=');

    if (! $supported_php_version) {
        add_action('admin_notices', 'entview_display_php_requirement_notice' );
    }

    global $wp_version;
    $supported_wp_version = version_compare($wp_version, ENTITY_VIEWER_MIN_WP_VERSION, '>=');

    if (! $supported_wp_version) {
        add_action('admin_notices', 'entview_display_wp_core_requirement_notice' );
    }

    if (! $supported_php_version || ! $supported_wp_version) {
        return;
    }

    require_once __DIR__ . '/src/Plugin.php';
    require_once __DIR__ . '/src/Fixture/TestDataGenerator.php';
    require_once __DIR__ . '/src/Fetcher/EntityFetcher.php';
    require_once __DIR__ . '/src/Fetcher/MetaFetcher.php';
    require_once __DIR__ . '/src/inc/logic.php';

    add_action('init', [Plugin::class, 'init']);
}

function entview_display_php_requirement_notice() {
    $message = esc_html__('The plugin "Entity viewer" doesn\'t support your PHP version and doesn\'t get initialized because of that.', 'entity-viewer');
    $message .= ' ';
    $message .= sprintf(esc_html__('The minimum required PHP version is: %s', 'entity-viewer'), ENTITY_VIEWER_MIN_PHP_VERSION);
    entview_display_admin_notice($message);
}

function entview_display_wp_core_requirement_notice() {
    $message = esc_html__('The plugin "Entity viewer" doesn\'t support your WordPress version and doesn\'t get initialized because of that.', 'entity-viewer');
    $message .= ' ';
    $message .= sprintf(esc_html__('The minimum required WordPress version is: %s', 'entity-viewer'), ENTITY_VIEWER_MIN_WP_VERSION);
    entview_display_admin_notice($message);
}

function entview_display_admin_notice($message) {
    if (! $message) {
        return;
    }

    $class = 'notice notice-error is-dismissible';
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}
