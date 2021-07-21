<?php
declare(strict_types = 1);

namespace VsEntityViewer;

use WP_Error;

defined('ABSPATH') || exit;

/**
 * TODO needs comments
 */
function show_metabox(object $item): void
{
    $item_class = get_class($item);
    $entity_name = str_replace('wp_', '', strtolower($item_class));

    $id_property = get_id_property_for_entity($entity_name);
    // WP_Post->ID in attachment metaboxes is string somehow (WP v5.7.2)
    $item_id = (int) $item->$id_property;

    $fields_data = get_fields_data($entity_name, $item_id);

    $data = [
        'metabox_type' => in_array($entity_name, ['post', 'comment']) ? 'content' : 'full',
        'metabox_header' => get_metabox_title_for_entity($entity_name),
        'has_serialized_values' => $fields_data['has_serialized_values'],
        'entity_type' => $entity_name,
        'fields' => $fields_data['fields'],
        'fetched_initial' => time(),
    ];

    render_metabox($data, $entity_name, $item_id);
}

function get_fields_data(string $entity_name, int $item_id): array
{
    $meta_id_key = get_meta_id_column_for_entity($entity_name);
    $meta_raw = get_meta_from_db($entity_name, $item_id);
    $has_serialized_values = false;

    $fields = array_map(
        construct_meta_data_mapper($meta_id_key, $has_serialized_values ),
        $meta_raw
    );

    return [
        'fields' => $fields,
        'has_serialized_values' => $has_serialized_values
    ];
}

/**
 * Returns a name of "entity"-id column in main (not meta) table
 * E.g., post -> post_id, user -> ID.
 */
function get_id_property_for_entity(string $entity_name): string
{
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

    return $id_property;
}

/**
 * Returns a name of "id" column in target meta table
 * E.g., post -> meta_id, user -> umeta_id.
 */
function get_meta_id_column_for_entity(string $entity_name): string
{
    return $entity_name === 'user' ? 'umeta_id' : 'meta_id';
}

/**
 * @deprecated It should be transformed to a constant-value in the future
 */
function get_metabox_title_for_entity(string $entity_name): string
{
    return (string) __('Entity viewer', 'entity-viewer');
}

function get_refreshing_nonce_name(string $entity_name, int $item_id): string
{
    return "_vsm_refresh_data__{$entity_name}_{$item_id}";
}

/**
 * AJAX-handler for "Refresh data"
 *
 * @return WP_Error|array
 */
function handle_refreshing_data_via_ajax()
{
    $send_response = function($raw_data, int $status = 200) {
        $is_error = is_wp_error($raw_data);
        $data = $is_error ? ['error' => $raw_data->get_error_message()] : $raw_data;

        status_header($status);
        echo json_encode($data);
        die();
    };

    if (! is_plugin_allowed(get_current_user_id())) {
        $send_response(new WP_Error("access_restricted", esc_html__("Access restricted.", 'entity-viewer')), 403);
    }

    $args = $_GET;

    $valid_entities = ['post', 'term', 'user', 'comment'];

    if (! isset($args['entity']) || ! in_array($args['entity'], $valid_entities) ) {
        $send_response(new WP_Error("invalid_param", esc_html__("Invalid parameter: entity", 'entity-viewer')), 400);
    }

    if (! isset($args['id']) || ! is_numeric($args['id'])) {
        $send_response(new WP_Error("invalid_param", esc_html__("Invalid parameter: id", 'entity-viewer')), 400);
    }

    $entity_name = $args['entity'];
    $item_id = absint($args['id']);

    if (! wp_verify_nonce($args['nonce'], get_refreshing_nonce_name($entity_name, $item_id))) {
        $send_response(new WP_Error("nonce_verification_failed", esc_html__("Nonce verification failed", 'entity-viewer')), 403);
    }

    $fields_data = get_fields_data($entity_name, $item_id);

    $send_response($fields_data['fields']);
}

function render_metabox(array $data, string $entity_name, $item_id): void
{
    add_action('admin_footer', '\\VsEntityViewer\\render_metabox_scripts', 200);

    echo '<div id="js-vsm-metabox"></div>' . PHP_EOL;
    echo sprintf('<input type="hidden" id="js-vsm-fields-data" style="display: none !important;" value="%s"></div>' . PHP_EOL, esc_attr(json_encode($data)));

    $nonce_name = get_refreshing_nonce_name($entity_name, $item_id);
    $settings = [
        'ajax_url' => get_admin_url(null, '/admin-ajax.php'),
        'query_args' => [
            'action' => 'vsm_refresh_data',
            'entity' => $entity_name,
            'id' => $item_id,
            'nonce' => wp_create_nonce($nonce_name),
        ],
    ];

    $settings['i18n'] = [
        'search_placeholder' => esc_html__('Search', 'entity-viewer'),
        'refresh_data' => esc_html__('Refresh data', 'entity-viewer'),
        'loading' => esc_html__('Loading...', 'entity-viewer'),
        'done' => esc_html__('Done!', 'entity-viewer'),
        'last_updated' => esc_html__('Last updated', 'entity-viewer'),
        'th_id' => esc_html__('Meta id', 'entity-viewer'),
        'th_key' => esc_html__('Key', 'entity-viewer'),
        'th_value' => esc_html__('Value', 'entity-viewer'),
        'incorrect_response' => esc_html__('Incorrect response, see dev-tools (console) for details', 'entity-viewer'),
        'http_error' => esc_html__('HTTP error: {{status}}, see dev-tools (console) for details', 'entity-viewer'),
        'loading_initial_state' => esc_html__('The initial state is loading...', 'entity-viewer'),
        'fields_not_found' => esc_html__('There are no meta fields for this item.', 'entity-viewer'),
        'fields_not_found_for_search_query' => esc_html__('There are no meta fields for this search query.', 'entity-viewer'),
        'see_raw_value' => esc_html__('see the raw value for search results', 'entity-viewer'),
    ];

    echo sprintf('<script type="text/javascript">window.vsm = %s;</script>' . PHP_EOL, json_encode($settings));
}

function register_post_meta_box($post_type): void
{
    global $typenow;

    if (! $typenow) {
        return;
    }

    add_meta_box(
        'vsm-post-meta',
        get_metabox_title_for_entity('post'),
        '\\VsEntityViewer\\show_metabox',
        $typenow,
        'normal'
    );
}

function register_comment_meta_box(): void
{
    add_meta_box(
        'vsm-comment-meta',
        get_metabox_title_for_entity('comment'),
        '\\VsEntityViewer\\show_metabox',
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


function register_term_meta_box(): void
{
    global $pagenow, $taxnow;

    if ($pagenow === 'term.php' && $taxnow) {
        add_action("{$taxnow}_edit_form", '\\VsEntityViewer\\show_metabox', 1000, 2);
    }
}

function render_metabox_scripts(): void
{
    $url = plugins_url('assets/build/entity-viewer.build.js', __DIR__);
    echo sprintf('<script type="text/javascript" src="%s"></script>', esc_attr($url));
}

function construct_meta_data_mapper(string $meta_id_key, bool & $has_serialized_values): callable
{
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

function get_meta_from_db(string $entity_name, int $item_id) {
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

/**
 * @deprecated maybe add styles for the metabox here?
 */
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

    // ...
}

/**
 * @see https://wordpress.org/support/article/roles-and-capabilities/#administrator
 */
function is_plugin_allowed(int $user_id): bool
{
    $capability = is_multisite() ? 'manage_options' : 'create_users';
    $allowed = user_can($user_id, $capability);
    $allowed_filtered = apply_filters('vsm/is_plugin_allowed', $allowed, $user_id);

    return $allowed_filtered;
}

/**
 * @param bool   $override Whether to override the .mo file loading. Default false.
 * @param string $domain   Text domain. Unique identifier for retrieving translated strings.
 * @param string $mofile   Path to the MO file.
 *
 * Note: it's not a good idea to use static typing here
 */
function disable_i18n_for_plugin($override, $domain, $mofile)
{
    if ($domain === 'entity-viewer') {
        return true;
    }

    return $override;
}
