<?php
/*
Plugin name: Entity viewer
Version: 0.2.1
Requires at least: 5.6.4
Requires PHP: 7.3
Text Domain: entity-viewer

Global PHP prefix: vsm
*/

defined('ABSPATH') || exit;

vsm_start_plugin();

function vsm_start_plugin() {
    $supported_php_version = version_compare(PHP_VERSION, '7.3.0', '>=');

    if (! $supported_php_version) {
        add_action('admin_notices', 'vsm_display_php_requirement_notice');
    }

    global $wp_version;
    $supported_wp_version = version_compare($wp_version, '5.6.4', '>=');

    if (! $supported_wp_version) {
        add_action('admin_notices', 'vsm_display_wp_core_requirement_notice');
    }

    if (! $supported_php_version || ! $supported_wp_version) {
        return;
    }

    add_action('admin_init', 'vsm_init_plugin');
}

function vsm_display_php_requirement_notice() {
    $message = esc_html__('The plugin "Entity viewer" doesn\'t support your PHP version and doesn\'t get initialized because of that.', 'entity-viewer');
    vsm_display_admin_notice($message);
}

function vsm_display_wp_core_requirement_notice() {
    $message = esc_html__('The plugin "Entity viewer" doesn\'t support your WordPress version and doesn\'t get initialized because of that.', 'entity-viewer');
    vsm_display_admin_notice($message);
}

function vsm_display_admin_notice($message) {
    if (! $message) {
        return;
    }

    $class = 'notice notice-error is-dismissible';
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}

function vsm_init_plugin() {
    require_once __DIR__ . '/src/Fetcher/EntityFetcher.php';
    require_once __DIR__ . '/inc/logic.php';
    require_once __DIR__ . '/inc/init.php';
}
