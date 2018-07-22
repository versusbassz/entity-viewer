<?php
/*
Plugin name: VS meta viewer
Requires PHP: 5.4 // TODO ???
*/

if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
    add_action('admin_init', 'vs_init_meta_viewer_plugin');
}

function vs_init_meta_viewer_plugin() {
    require_once __DIR__ . '/inc/logic.php';
    require_once __DIR__ . '/init.php';
}
