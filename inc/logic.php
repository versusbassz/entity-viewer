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

    $ui = [
        'metabox_header' => ucfirst($entity_name) . ' meta',
        'has_serialized_values' => $has_serialized_values,
        'entity_type' => $entity_name,
    ];

    if (in_array($entity_name, ['post', 'comment'])) {
        render_metabox_data($meta, $ui);
    } else {
        render_metabox_full($meta, $ui);
    }
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

function render_metabox_full(array $data, array $ui)
{
    ?>

    <div class="vs-metaviewer-metabox js-metaviewer-metabox" data-entity-type="<?= $ui['entity_type'] ?>">

        <h2 class="vs-metaviewer-metabox__header js-metaviewer-metabox-header"><?= $ui['metabox_header'] ?></h2>

        <div class="vs-metaviewer-metabox-content js-metaviewer-metabox-content">

            <?php render_metabox_data($data, $ui); ?>

        </div>

    </div>

    <?php
}

function render_metabox_data(array $data, array $ui)
{
    add_action('admin_footer', '\\VsMetaViewer\\render_metabox_scripts', 200);

    if (! count($data)) {
        ?>
            <div class="vs-not-exists-message">There are no meta fields for this item.</div>
        <?php
        return;
    }
    ?>

    <table class="vs-table js-metaviewer-data">
        <thead>
        <tr>
            <th class="vs-table__column table__column_type_th vs-table__column_content_umeta-id" data-sort="int">Meta id</th>
            <th class="vs-table__column table__column_type_th" data-sort="string" data-sort-onload="yes">Key</th>
            <th class="vs-table__column table__column_type_th">

                <?php if ($ui['has_serialized_values']) { ?>
                    <a href="javascript:void(0)"
                       class="vs-pretty-code-button js-pretty-code-button-all"
                       data-current-type="plain"
                    >{}</a>
                <?php } ?>
            </th>
            <th class="vs-table__column table__column_type_th">Value</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $item) { ?>
            <tr class="vs-table__row">
                <td class="vs-table__column vs-table__column_type_td"><?= esc_html($item['id']) ?></td>
                <td class="vs-table__column vs-table__column_type_td"><?= esc_html($item['key']) ?></td>

                <td class="vs-table__column vs-table__column_type_td">

                    <?php if ($item['value_pretty']) { ?>
                        <a href="javascript:void(0)"
                           class="vs-pretty-code-button js-pretty-code-button"
                           data-current-type="plain"
                        >{}</a>
                    <?php } ?>

                </td>

                <td class="vs-table__column vs-table__column_type_td vs-table__column_content_value">

                    <div data-type="plain">
                        <div>&#39;<?= esc_html($item['value']) ?>&#39;</div>
                    </div>

                    <?php if ($item['value_pretty']) { ?>
                        <div data-type="pretty" style="display: none;">
                            <pre><?= esc_html($item['value_pretty']) ?></pre>
                        </div>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <?php
}

function render_metabox_scripts()
{
	$url = VS_META_VIEWER_PLUGIN_URL . '/assets/build/meta-viewer.build.js';
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
            'id' => $item[$meta_id_key],
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

    wp_enqueue_script('vs-jquery-stupid-table-plugin', VS_META_VIEWER_PLUGIN_URL . '/assets/js/stupidtable.min.js', ['jquery'], '1.1.3', true);
    wp_enqueue_script('vs-cookie', VS_META_VIEWER_PLUGIN_URL . '/assets/js/js.cookie.min.js', [], '2.2.0', true);
}
