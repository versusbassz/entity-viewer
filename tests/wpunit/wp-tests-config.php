<?php

/* Path to the WordPress codebase you'd like to test. Add a forward slash in the end. */
define( 'ABSPATH', dirname( __DIR__, 2 ) . '/custom/wp-core' . '/' );

/*
 * Path to the theme to test with.
 *
 * The 'default' theme is symlinked from test/phpunit/data/themedir1/default into
 * the themes directory of the WordPress installation defined above.
 */
define( 'WP_DEFAULT_THEME', 'default' );

/*
 * Test with multisite enabled.
 * Alternatively, use the tests/phpunit/multisite.xml configuration file.
 */
// define( 'WP_TESTS_MULTISITE', true );

/*
 * Force known bugs to be run.
 * Tests with an associated Trac ticket that is still open are normally skipped.
 */
// define( 'WP_TESTS_FORCE_KNOWN_BUGS', true );

// Test with WordPress debug mode (default).
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', true);

// ** Database settings ** //

/*
 * This configuration file will be used by the copy of WordPress being tested.
 * wordpress/wp-config.php will be ignored.
 *
 * WARNING WARNING WARNING!
 * These tests will DROP ALL TABLES in the database with the prefix named below.
 * DO NOT use a production database or one that is shared with something else.
 */

define( 'DB_HOST', 'mariadb' );
define( 'DB_NAME', 'wordpress_test' );
define( 'DB_USER', 'wordpress' );
define( 'DB_PASSWORD', 'wordpress' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 */
define('AUTH_KEY',         ' U2?~qA=2;ssBcL^>66e3hzJs/^F3$n)dXWk)v9gj/8((4_,Bgh{/mgu=>1h)l.y');
define('SECURE_AUTH_KEY',  '$+>]-r1QHH@Lyf}Q7eEw(GLy|S~7F!51lV^f([o}I@jq9+92~~|qV0Ib+r[WvWNh');
define('LOGGED_IN_KEY',    'j0dFV%6_.Ywm$eKKA37S>Fou^OdIT,*.h8{Ue,o&J6)1L(H5i|m0v &!rG-7Fajo');
define('NONCE_KEY',        'P=2H/{B,,PPq~-g|7De+8!,5zz0&i|] O-ot>-:7hy3C/#M+:9I$-1~M<(sG/]kU');
define('AUTH_SALT',        '`(}tB%O,a]7k__5T?7J:w{U[2FKV=|x2k|8^J?Y]W46D-oTIO7|8kD=i3I8|Y<,`');
define('SECURE_AUTH_SALT', '+X](TrK;v/aS09RhyVbs 5|VV)[~ T!1xWujAJ$v+.[?08d,}==|&p7G|0m-II-*');
define('LOGGED_IN_SALT',   '2B]~$9b;b&kp+2nA`m)3FrP=-#wfLV?9<odlBB+[R+kz?@Qc4-zXzG(hrA|]|t]D');
define('NONCE_SALT',       's:XP8u_K9{Bk<ami3^j=@(HHMXp<O*!|.NaX{%V!EpVOUh[Y.&@xLKJo|*Rpw-T<');

$table_prefix = 'wpunit_';   // Only numbers, letters, and underscores please!

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );
