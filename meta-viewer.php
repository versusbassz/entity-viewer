<?php
/*
Plugin name: VS meta viewer
Requires PHP: 5.4 // TODO ???
*/

defined('ABSPATH') || exit;

if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
    add_action('admin_init', 'vs_init_meta_viewer_plugin');
}

function vs_init_meta_viewer_plugin() {
    require_once dirname(__FILE__) . '/inc/logic.php';
    require_once dirname(__FILE__) . '/init.php';
}
