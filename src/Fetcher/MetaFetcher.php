<?php
declare(strict_types = 1);

namespace Versusbassz\EntityViewer\Fetcher;

use WP_Error;
use function Versusbassz\EntityViewer\construct_meta_data_mapper;
use function Versusbassz\EntityViewer\get_meta_id_column_for_entity;

class MetaFetcher
{
    public static function getData(string $entity_name, int $item_id): array
    {
        $meta_id_key = get_meta_id_column_for_entity($entity_name);
        $meta_raw = self::fetchDataFromDB($entity_name, $item_id);

        $result = [
            'tab_title' => __('Meta', 'entity-viewer'),
            'section_title' => __('Meta', 'entity-viewer'),
            'fields' => [],
            'error' => false,
            'has_serialized_values' => false, // not used in JS for now
        ];

        if (is_wp_error($meta_raw)) {
            $result['error'] = $meta_raw->get_error_code();
            return $result;
        }

        $has_serialized_values = false;

        $fields = array_map(
            construct_meta_data_mapper($meta_id_key, $has_serialized_values),
            $meta_raw
        );

        $result['fields'] = $fields;
        $result['has_serialized_values'] = $has_serialized_values;

        return $result;
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
