<?php
declare(strict_types = 1);

namespace VsEntityViewer\Fetcher;

use WP_Error;
use function VsEntityViewer\construct_meta_data_mapper;
use function VsEntityViewer\get_meta_id_column_for_entity;

class MetaFetcher
{
    /**
     * @return array|WP_Error
     */
    public static function getData(string $entity_name, int $item_id)
    {
        $has_serialized_values = false;

        $meta_id_key = get_meta_id_column_for_entity($entity_name);
        $meta_raw = self::fetchDataFromDB($entity_name, $item_id);

        if (is_wp_error($meta_raw)) {
            return $meta_raw;
        }

        $fields = array_map(
            construct_meta_data_mapper($meta_id_key, $has_serialized_values),
            $meta_raw
        );

        return [
            'fields' => $fields,
            'has_serialized_values' => $has_serialized_values,
        ];
    }

    /**
     * @return array|WP_Error
     */
    public static function fetchDataFromDB(string $entity_name, int $item_id) {
        global $wpdb;

        $table = $entity_name . 'meta';

        $result = $wpdb->get_results(
            $wpdb->prepare("
                SELECT *
                FROM {$wpdb->$table}
                WHERE {$entity_name}_id = %d
                ORDER BY meta_key ASC
            ", $item_id),
            ARRAY_A
        );

        if (! is_array($result)) {
            return new WP_Error('incorrect_meta_db_response');
        }

        return $result;
    }
}
