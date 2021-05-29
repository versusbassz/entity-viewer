<?php
/*
Plugin name: VS meta viewer
Requires at least: 5.6.4
Requires PHP: 7.3

Global PHP prefix: vsm
*/

defined('ABSPATH') || exit;

vsm_start_plugin();

function vsm_start_plugin() {
	if (! version_compare(PHP_VERSION, '7.3.0', '>=')) {
		add_action('admin_notices', 'vsm_display_php_requirement_notice');
	}

	global $wp_version;

	if (! version_compare($wp_version, '5.6.4', '>=')) {
		add_action('admin_notices', 'vsm_display_wp_core_requirement_notice');
	}

	add_action('admin_init', 'vsm_init_plugin');
}

function vsm_display_php_requirement_notice() {
	$message = 'The plugin "Meta viewer" doesn\'t support your PHP version and doesn\'t get initialized because of that.';
	vsm_display_admin_notice( $message );
}

function vsm_display_wp_core_requirement_notice() {
	$message = 'The plugin "Meta viewer" doesn\'t support your WordPress version and doesn\'t get initialized because of that.';
	vsm_display_admin_notice( $message );
}

function vsm_display_admin_notice($message) {
	if (! $message) {
		return;
	}

	$class = 'notice notice-error is-dismissible';
	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

function vsm_init_plugin() {
    require_once dirname(__FILE__) . '/inc/logic.php';
    require_once dirname(__FILE__) . '/init.php';
}
