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
        'vsmt-post-meta',
        'Post meta',
        '\\VsMetaViewer\\show_metabox',
        $typenow,
        'normal'
    );
}

function register_comment_meta_box()
{
    add_meta_box(
        'vsmt-comment-meta',
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
    render_metabox_styles();
    render_metabox_scripts();

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

function render_metabox_styles()
{
?>

<style type="text/css">
    .vs-metaviewer-metabox {
        margin:30px 0;
        background: #fff;
    }

    .js-metaviewer-metabox-closed .vs-metaviewer-metabox-content {
        display:none;
    }

    .vs-metaviewer-metabox__header {
        margin: 0 0 10px 0;
        border-bottom: 1px solid #e5e5e5;
        padding: 8px 20px;

        font-size: 14px;
        line-height: 1.4;
    }

    .vs-metaviewer-metabox__header:hover {
        cursor:pointer;
    }

    .vs-metaviewer-metabox-content {
        padding: 0 10px 10px;
    }

    .vs-not-exists-message {
        padding-left:10px;
    }

    .vs-table {
        border-collapse: collapse;
    }

    .vs-table a {
        text-decoration:none;
    }

    .vs-table__row {
        /**/
    }

    .vs-table__row:hover {
        background-color:#f0f0f0;
    }

    .vs-table__column {
        padding:5px 10px;
        text-align:left;
    }

    .vs-table__column_type_th {
        border-bottom: 1px solid #e1e1e1;
        font-weight:bold;
    }

    .table__column_type_th[data-sort] {
        text-decoration:dashed !important;
    }

    .table__column_type_th[data-sort]:hover {
        cursor:pointer;
    }

    .vs-table__column_type_td {
        vertical-align:top;
    }


    .vs-table__column_content_umeta-id {
        min-width:65px;
    }

    .vs-table__column_content_value {
        /**/
    }

    .vs-table__column_content_value pre {
        margin-top:0;
        margin-bottom:0;
    }

    .vs-table__column_content_value div {
        font-family: monospace;
        word-break: break-all;
    }

    .vs-arrow {
        margin-left:6px;
        font-weight:bold;

        border: solid black;
        border-width: 0 2.3px 2.3px 0;
        display: inline-block;
        padding: 2.3px;
        position:relative;
        top:-1px;
    }

    .vs-arrow_dir_up {
        transform: rotate(-135deg);
        -webkit-transform: rotate(-135deg);
    }

    .vs-arrow_dir_down {
        transform: rotate(45deg);
        -webkit-transform: rotate(45deg);
    }

    .vs-pretty-code-button {
        display: inline-block;
        border:1px solid #e2e2e2;
        padding: 0 5px;
        font-weight: bold;

    }
    .vs-pretty-code-button:focus {
        box-shadow:none;
    }

    .vs-pretty-code-button_activated {
        /*background-color:#d3ffcb;*/
        background-color:#e2f9de;
    }
</style>

<?php
}

function render_metabox_scripts()
{
?>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        var $metabox = $('.js-metaviewer-metabox');
        var $header = $metabox.find('.js-metaviewer-metabox-header');
        var $content = $metabox.find('.js-metaviewer-metabox-content');

        $header.click(function () {
            $metabox.toggleClass('js-metaviewer-metabox-closed');
            handle_open_close_state();
        });

        var $data_table = $('.js-metaviewer-data');

        var $pretty_links = $data_table.find('.js-pretty-code-button');
        var $pretty_all_link = $data_table.find('.js-pretty-code-button-all');

        var table = $data_table.stupidtable();

        table.bind('aftertablesort', function (event, data) {
            var arrow_class = 'vs-arrow';

            var th = $(this).find("th");
            th.find('.' + arrow_class).remove();

            var dir = $.fn.stupidtable.dir;

            var arrow_dir_class = data.direction === dir.ASC ? "vs-arrow_dir_up" : "vs-arrow_dir_down";
            th.eq(data.column).append('<span class="' + arrow_class + ' ' + arrow_dir_class + '"></span>');
        });

        $pretty_links.click(function (e) {

            var types = ['plain', 'pretty'];

            var $target = $( e.target );
            var prev_type = $target.attr('data-current-type');

            var new_type = types.filter(function(item) {
                return item !== prev_type;
            })[0];

            if (new_type === 'pretty') {
                $target.addClass('vs-pretty-code-button_activated');
            } else {
                $target.removeClass('vs-pretty-code-button_activated');
            }

            $target.attr('data-current-type', new_type);

            var $value_cell = $target.parent().siblings('.vs-table__column_content_value');
            $value_cell
                .find('[data-type="' + new_type + '"]')
                .show()
                .siblings()
                .hide();
        });

        $pretty_all_link.click(function (e) {

            var types = ['plain', 'pretty'];

            var $target = $(e.target);
            var previous_type = $target.attr('data-current-type');

            var new_type = types.filter(function(item) {
                return item !== previous_type;
            })[0];

            if (new_type === 'pretty') {
                $target.addClass('vs-pretty-code-button_activated');
            } else {
                $target.removeClass('vs-pretty-code-button_activated');
            }

            $target.attr('data-current-type', new_type);

            $pretty_links.attr('data-current-type', previous_type);
            $pretty_links.trigger('click');
        });

        // Saving open/close metabox's state on entities' pages without Metabox API
        var open_close_cookie_name = 'vs-metaviewer-metabox-closed-for-' + $metabox.attr('data-entity-type');
        var open_close_cookie_values = ['opened', 'closed'];
        var open_close_handler_enabled = false;
        var open_close_handler_was_lauched = false;

        if ($metabox.length) {
            open_close_handler_enabled = true;
            handle_open_close_state();
        }

        function handle_open_close_state() {

            if (! open_close_handler_enabled) {
                return;
            }

            var cookie_value = Cookies.get(open_close_cookie_name);

            if (! jQuery.inArray(cookie_value, open_close_cookie_values)) {
                cookie_value = 'opened';
                Cookies.set(open_close_cookie_name, 'opened');
            }

            var current_state = $metabox.hasClass('js-metaviewer-metabox-closed') ? 'closed' : 'opened';

            if (open_close_handler_was_lauched) {
                Cookies.set(open_close_cookie_name, current_state);
            } else {

                open_close_handler_was_lauched = true;

                if (current_state !== cookie_value && cookie_value === 'closed') {
                     $metabox.addClass('js-metaviewer-metabox-closed');
                }
            }
        }
    });
</script>

<?php
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
