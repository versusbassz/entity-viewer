<?php
defined('ABSPATH') || exit;

define('VSM_DEFAULT_CAPABILITY', 'delete_users');

if (! is_admin() || ! current_user_can(VSM_DEFAULT_CAPABILITY)) {
    return;
}

// AJAX-handler: Refresh metabox data
if (wp_doing_ajax()) {
	add_action('wp_ajax_vsm_refresh_data', '\\VsMetaViewer\\handle_refreshing_data_via_ajax');
	return;
}

// post
add_action('add_meta_boxes', '\\VsMetaViewer\\register_post_meta_box', 1000, 1);

// user
add_action('edit_user_profile', '\\VsMetaViewer\\show_metabox', 1000, 1);
add_action('show_user_profile', '\\VsMetaViewer\\show_metabox', 1000, 1);

// comment
add_action('add_meta_boxes_comment', '\\VsMetaViewer\\register_comment_meta_box', 1000, 1);

// term
add_action('admin_print_scripts', '\\VsMetaViewer\\register_term_meta_box', 1000, 1);


// frontend assets
add_action('admin_enqueue_scripts', '\\VsMetaViewer\\enqueue_scripts');
