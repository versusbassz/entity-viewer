<?php
if (! is_admin() || wp_doing_ajax() || ! current_user_can('edit_users')) {
    return;
}

define('VS_META_VIEWER_PLUGIN_URL', plugins_url('', __FILE__));

add_action('edit_user_profile', '\\VsMetaViewer\\show_user_info_metabox', 1000, 1);
add_action('show_user_profile', '\\VsMetaViewer\\show_user_info_metabox', 1000, 1);

add_action('admin_enqueue_scripts', '\\VsMetaViewer\\enqueue_scripts');
