<?php
declare(strict_types = 1);

namespace VsEntityViewer\Fetcher;

use WP_Error;
use function VsEntityViewer\get_id_property_for_entity;

class EntityFetcher
{
    /**
     * @return array|WP_Error
     */
    public static function getData(string $entity_name, int $item_id)
    {
        $data = self::fetchDataFromDB($entity_name, $item_id);

        if (is_wp_error($data)) {
            return $data;
        }

        $fields = [];

        foreach ($data as $key => $value) {
            $fields[] = [
                'key' => $key,
                'value' => $value,
            ];
        }

        return [
            'fields' => $fields,
        ];
    }

    /**
     * @return array|WP_Error
     */
    public static function fetchDataFromDB(string $entity_name, int $item_id)
    {
        global $wpdb;

        $table = $entity_name . 's';
        $id_column = get_id_property_for_entity($entity_name);

        $result = $wpdb->get_row(
            $wpdb->prepare("
                SELECT *
                FROM {$wpdb->$table}
                WHERE {$id_column} = %d
            ", $item_id),
            ARRAY_A
        );

        if (is_null($result)) {
            return new WP_Error('empty_entity_db_response');
        }

        return $result;
    }
}
