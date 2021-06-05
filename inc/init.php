<?php
defined('ABSPATH') || exit;

if (! is_admin() || wp_doing_ajax() || ! current_user_can('manage_options')) {
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
