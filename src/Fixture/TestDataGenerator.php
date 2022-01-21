<?php

declare(strict_types = 1);

namespace VsEntityViewer\Fixture;

use Exception;
use WP_CLI;
use WP_Post;
use WP_Comment;

/**
 * Generates data (posts, users, etc.) for local development environment (dev-env)
 * It's not initialized on env=production
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

        $meta_fields = [
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
        ];

        $posts = [
            [
                'post_type' => 'post',
                'post_title' => 'Test Post - Multiple serialized values',
                'post_status' => 'publish',
                'post_author' => $default_user_id,
                'post_content' => '...',
                'meta_input' => $meta_fields,
            ],
            [
                'post_type' => 'post',
                'post_title' => 'Test Post - Spoilers testing',
                'post_status' => 'publish',
                'post_author' => $default_user_id,
                'post_content' => implode(',', range(0,1000)),
                'meta_input' => array_merge($meta_fields, [
                    'ev-large-field' => implode(',', range(0,1000)),
                    'ev-large-field-serialized' => array_fill(0, 20, implode(',', range(0,100))),
                ]),
            ],
        ];

        foreach ($posts as $post) {
            $insert_result = wp_insert_post($post, true);

            if (is_wp_error($insert_result)) {
                WP_CLI::error(var_export($insert_result, true));
            } else {
                WP_CLI::log(sprintf(__('Added item: post, %s', 'entity-viewer'), $insert_result));
            }
        }

        $comments = [
            [
                'comment_post_ID' => 1,
                'comment_content' => 'Test comment content',
                'comment_meta' => $meta_fields,
            ],
        ];

        foreach ($comments as $comment) {
            $insert_result = wp_insert_comment($comment);

            if (is_wp_error($insert_result)) {
                WP_CLI::error(var_export($insert_result, true));
            } else {
                WP_CLI::log(sprintf(__('Added item: comment, %s', 'entity-viewer'), $insert_result));
            }
        }

        WP_CLI::success(__('Command finished', 'entity-viewer'));
    }

    /**
     * The command for removing the test data
     */
    public static function remove(): void
    {
        $meta_query = [
            [
                'key' => self::TRACK_FIELD_NAME,
                'value' => self::TRACK_FIELD_VALUE,
            ],
        ];

        $posts = get_posts([
            'post_type' => ['post', 'attachment'],
            'post_status' => ['publish', 'inherit', 'trash'],
            'nopaging' => 'true',
            'meta_query' => $meta_query,
        ]);

        foreach ($posts as $post) {
            $deletion_result = wp_delete_post($post->ID, true);

            if (! $deletion_result instanceof WP_Post) {
                WP_CLI::log(sprintf(__('Error on deleting post #%d', 'entity-viewer'), $post->ID));
                WP_CLI::error(var_export($deletion_result, true));
            } else {
                WP_CLI::log(sprintf(__('Removed item: post, %s', 'entity-viewer'), $post->ID));
            }
        }

        /** @var WP_Comment[] $comments */
        $comments = get_comments([
            'meta_query' => $meta_query,
        ]);

        foreach ($comments as $comment) {
            $comment_id = (int) $comment->comment_ID;

            $deletion_result = wp_delete_comment($comment_id, true);

            if (! $deletion_result ) {
                WP_CLI::log(sprintf(__('Error on deleting comment #%d', 'entity-viewer'), $comment_id));
                WP_CLI::error(var_export($deletion_result, true));
            } else {
                WP_CLI::log(sprintf(__('Removed item: comment, %s', 'entity-viewer'), $comment_id));
            }
        }

        WP_CLI::success(__('Command finished', 'entity-viewer'));
    }
}
