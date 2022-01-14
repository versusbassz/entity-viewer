<?php

namespace VsEntityViewer\Fixture;

use Exception;
use WP_CLI;
use WP_Post;

/**
 * Generates data (posts, users, etc.) for local development environment (dev-env)
 */
class TestDataGenerator
{
    const TRACK_FIELD_NAME = 'ev-track-field';
    const TRACK_FIELD_VALUE = 'ev-track-value';

    /**
     * Register WP_CLI commands if a current SAPI is CLI
     *
     * @throws Exception
     */
    public static function init(): void
    {
        if (defined(WP_CLI::class) && wp_get_environment_type() !== 'production') {
            WP_CLI::add_command('ev test-data generate', [self::class, 'generate']);
            WP_CLI::add_command('ev test-data remove', [self::class, 'remove']);
        }
    }

    /**
     * The command for generating the test data
     */
    public static function generate(): void
    {
        $default_user_id = 1;

        $obj = (new \stdClass());
        $obj->prop1 = 'value1';
        $obj->prop2 = 'value2';

        $posts = [
            [
                'post_type' => 'post',
                'post_title' => 'Test Post - multiple serialized values',
                'post_status' => 'publish',
                'post_author' => $default_user_id,
                'meta_input' => [
                    self::TRACK_FIELD_NAME => self::TRACK_FIELD_VALUE,
                    'ev-simple-zero' => 0,
                    'ev-simple-int' => 123,
                    'ev-simple-empty-string' => '',
                    'ev-simple-string' => 'asdf',
                    'ev-simple-true' => true,
                    'ev-simple-false' => false,
                    'ev-simple-null' => null,
                    'ev-simple-empty-array' => [],
                    'ev-serialized-1' => [1, 2, 3, 4, 5],
                    'ev-serialized-2' => ['one', 'two', 'three', 'four', 'five'],
                    'ev-serialized-3' => [1, 2, 3, [4, 5], [6, 7]],
                    'ev-serialized-4' => $obj,
                ],
            ],
        ];

        foreach ($posts as $post) {
            $insert_result = wp_insert_post($post, true);

            if (is_wp_error($insert_result)) {
                WP_CLI::error(var_export($insert_result, true));
            } else {
                WP_CLI::log(sprintf("Added item: post, %s", $insert_result));
            }
        }

        WP_CLI::success('Command finished');
    }

    /**
     * The command for removing the test data
     */
    public static function remove(): void
    {
        $posts = get_posts([
            'post_type' => ['post', 'attachment'],
            'post_status' => ['publish', 'inherit', 'trash'],
            'nopaging' => 'true',
            'meta_query' => [
                [
                    'key' => self::TRACK_FIELD_NAME,
                    'value' => self::TRACK_FIELD_VALUE,
                ],
            ],
        ]);

        foreach ($posts as $post) {
            $deletion_result = wp_delete_post($post->ID, true);

            if (! $deletion_result instanceof WP_Post) {
                WP_CLI::log(sprintf('Error on deleting post #%d', $post->ID));
                WP_CLI::error(var_export($deletion_result, true));
            } else {
                WP_CLI::log(sprintf("Removed item: post, %s", $post->ID));
            }
        }

        WP_CLI::success('Command finished');
    }
}
