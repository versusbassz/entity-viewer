<?php
/*
Plugin name: VS meta viewer
Requires PHP: 7.3
*/

defined('ABSPATH') || exit;

vs_start_meta_viewer_plugin();

function vs_start_meta_viewer_plugin() {
	if (! version_compare(PHP_VERSION, '7.3.0', '>=')) {
		add_action('admin_notices', 'vs_display_php_requirement_notice_for_meta_viewer_plugin');
	}

	add_action('admin_init', 'vs_init_meta_viewer_plugin');
}

function vs_display_php_requirement_notice_for_meta_viewer_plugin() {
	$class = 'notice notice-error is-dismissible';
	$message = 'The plugin "Meta viewer" doesn\'t support your PHP version and isn\'t initialized because of that.';

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

function vs_init_meta_viewer_plugin() {
    require_once dirname(__FILE__) . '/inc/logic.php';
    require_once dirname(__FILE__) . '/init.php';
}
