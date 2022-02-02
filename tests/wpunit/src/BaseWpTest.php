<?php

namespace Versusbassz\EntityViewer\Tests;

class BaseWpTest extends \WP_UnitTestCase
{
    public function testNothing()
    {
        self::factory()->post->create_many( 20 );

        $posts = get_posts([
            'nopaging' => true,
        ]);
        $this->assertCount(20, $posts);
    }
}
