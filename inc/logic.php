<?php
namespace VsMetaViewer;

defined('ABSPATH') || exit;

/**
 * TODO needs comments
 *
 * @param object $item
 */
function show_metabox($item)
{
    $item_class = get_class($item);
    $entity_name = str_replace('wp_', '', strtolower($item_class));

    $meta_id_key = $item instanceof \WP_User ? 'umeta_id' : 'meta_id';

    switch ($entity_name) {
        case 'post':
        case 'user':
            $id_property = 'ID';
            break;

        case 'comment':
            $id_property = 'comment_ID';
            break;

        default:
            $id_property = $entity_name . '_id';
            break;
    }

    $meta_raw = get_meta_from_db($entity_name, $item->$id_property);

    $meta = array_map(
        construct_meta_data_mapper($meta_id_key, $has_serialized_values),
        $meta_raw
    );

    $data = [
        'metabox_type' => in_array($entity_name, ['post', 'comment']) ? 'content' : 'full',
        'metabox_header' => ucfirst($entity_name) . ' meta',
        'has_serialized_values' => $has_serialized_values,
        'entity_type' => $entity_name,
        'fields' => $meta
    ];

    render_metabox($data);
}

function render_metabox(array $data)
{
	add_action('admin_footer', '\\VsMetaViewer\\render_metabox_scripts', 200);

	echo '<div id="js-vsm-metabox"></div>';
	echo sprintf('<input type="hidden" id="js-vsm-fields-data" style="display: none !important;" value="%s" />', esc_attr(json_encode($data)));
}

function register_post_meta_box($post_type)
{
    global $typenow;

    if (! $typenow) {
        return;
    }

    add_meta_box(
        'vsm-post-meta',
        'Post meta',
        '\\VsMetaViewer\\show_metabox',
        $typenow,
        'normal'
    );
}

function register_comment_meta_box()
{
    add_meta_box(
        'vsm-comment-meta',
        'Comment meta',
        '\\VsMetaViewer\\show_metabox',
        'comment',
        'normal' // context required!
    );

    // The context within the screen where the boxes should display.
    // Available contexts vary from screen to screen.
    //   Post edit screen contexts include 'normal', 'side' and 'advanced'.
    //   Comments screen contexts include 'normal' and 'side'.
    //   Menus meta boxes (accordion sections) all use the 'side' context.
    // Global default is 'advanced'.
}


function register_term_meta_box() {
    global $pagenow, $taxnow;

    if ($pagenow === 'term.php' && $taxnow) {
        add_action("{$taxnow}_edit_form", '\\VsMetaViewer\\show_metabox', 1000, 2);
    }
}

function render_metabox_scripts()
{
	$url = plugins_url('assets/build/meta-viewer.build.js', __DIR__);
	echo sprintf('<script type="text/javascript" src="%s"></script>', esc_attr($url));
}

function construct_meta_data_mapper($meta_id_key, & $has_serialized_values) {

    return function (array $item) use ($meta_id_key, & $has_serialized_values) {

        $is_value_serialized = is_serialized($item['meta_value']);
        $value_pretty = $is_value_serialized ? var_export(unserialize($item['meta_value']), true) : '';

        if ($is_value_serialized) {
            $has_serialized_values = true;
            $value_pretty = preg_replace('/array[\s]*\(([\s]+)\)/ims', 'array ()', $value_pretty);
        }

        return [
            'id' => (int) $item[$meta_id_key],
            'key' => $item['meta_key'],
            'value_pretty' => $value_pretty,
            'value' => $item['meta_value'],
        ];
    };
}

function get_meta_from_db($entity_name, $item_id) {
    global $wpdb;

    $table = $entity_name . 'meta';

    return $wpdb->get_results(
        $wpdb->prepare("
            SELECT * 
            FROM {$wpdb->$table} 
            WHERE {$entity_name}_id = %d
            ORDER BY meta_key ASC
        ", $item_id),
        ARRAY_A
    );
}

function enqueue_scripts()
{
    global $pagenow;

    $valid_admin_pages = [
        'profile.php',
        'user-edit.php',
        'post.php',
        'post-new.php',
        'comment.php',
        'term.php',
    ];

    if (! in_array($pagenow, $valid_admin_pages)) {
        return;
    }

    // TODO maybe add styles for the metabox here
}
