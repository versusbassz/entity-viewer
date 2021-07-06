<?php

use function VsEntityViewer\is_plugin_allowed;

defined('ABSPATH') || exit;

if (! is_admin() || ! is_plugin_allowed(get_current_user_id())) {
    return;
}

// AJAX-handler: Refresh metabox data
if (wp_doing_ajax()) {
	add_action('wp_ajax_vsm_refresh_data', '\\VsEntityViewer\\handle_refreshing_data_via_ajax');
	return;
}

if (! apply_filters('vsm/is_i18n_enabled', true)) {
    add_filter('override_load_textdomain', '\\VsEntityViewer\\disable_i18n_for_plugin', 10, 3);
}

// post
add_action('add_meta_boxes', '\\VsEntityViewer\\register_post_meta_box', 1000, 1);

// user
add_action('edit_user_profile', '\\VsEntityViewer\\show_metabox', 1000, 1);
add_action('show_user_profile', '\\VsEntityViewer\\show_metabox', 1000, 1);

// comment
add_action('add_meta_boxes_comment', '\\VsEntityViewer\\register_comment_meta_box', 1000, 1);

// term
add_action('admin_print_scripts', '\\VsEntityViewer\\register_term_meta_box', 1000, 1);


// frontend assets
add_action('admin_enqueue_scripts', '\\VsEntityViewer\\enqueue_scripts');
