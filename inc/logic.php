<?php
namespace VsMetaViewer;

function show_user_info_metabox(\WP_User $user)
{
    global $wpdb;

    $user_id = $user->ID;

    $user_meta_raw = $wpdb->get_results(
        $wpdb->prepare("
            SELECT * 
            FROM {$wpdb->usermeta} 
            WHERE user_id = %d
            ORDER BY meta_key ASC
        ", $user_id),
        ARRAY_A
    );

    $user_meta = array_map(function (array $item) {

        $is_value_serialized = is_serialized($item['meta_value']);
        $value_pretty = $is_value_serialized ? var_export(unserialize($item['meta_value']), true) : '';

        if ($is_value_serialized) {
            $value_pretty = preg_replace('/array[\s]*\(([\s]+)\)/ims', 'array ()', $value_pretty);
        }

        return [
            'id' => $item['umeta_id'],
            'key' => $item['meta_key'],
            'value_pretty' => $value_pretty,
            'value' => $item['meta_value'],
        ];
    }, $user_meta_raw);
    ?>

    <div class="vs-metaviewer-metabox js-metaviewer-metabox">

        <h2 class="vs-metaviewer-metabox__header js-metaviewer-metabox-header">User meta</h2>

        <div class="vs-metaviewer-metabox-content js-metaviewer-metabox-content">

            <table class="vs-table">
                <thead>
                <tr>
                    <th class="vs-table__column table__column_type_th vs-table__column_content_umeta-id" data-sort="int">Meta ID</th>
                    <th class="vs-table__column table__column_type_th" data-sort="string" data-sort-onload="yes">Key</th>
                    <th class="vs-table__column table__column_type_th">
                        <a href="javascript:void(0)"
                           class="vs-pretty-code-button js-pretty-code-button-all"
                           data-current-type="plain"
                        >{}</a>
                    </th>
                    <th class="vs-table__column table__column_type_th">Value</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($user_meta as $item) { ?>
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
                                <pre>&#39;<?= esc_html($item['value']) ?>&#39;</pre>
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

        </div>

    </div>

    <style type="text/css">
        .vs-metaviewer-metabox {
            margin:30px 0;
            background: #fff;
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
            padding-bottom:10px;
        }

        .vs-table {
            margin-left:10px;
            margin-right:10px;
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

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var $metabox = $('.js-metaviewer-metabox');
            var $header = $metabox.find('.js-metaviewer-metabox-header');
            var $content = $metabox.find('.js-metaviewer-metabox-content');

            var $pretty_links = $metabox.find('.js-pretty-code-button');
            var $pretty_all_link = $metabox.find('.js-pretty-code-button-all');

            var table = $metabox.find('.vs-table').stupidtable();

            $header.click(function () {
                $content.toggle();
            });

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
        });
    </script>

    <?php
}


function enqueue_scripts()
{
    global $pagenow;

    if (! in_array($pagenow, ['profile.php', 'user-edit.php'])) {
        return;
    }

    wp_enqueue_script('vs-jquery-stupid-table-plugin', VS_META_VIEWER_PLUGIN_URL . '/assets/js/stupidtable.min.js', ['jquery'], '1.1.3', true);
}
