<?php

// composer
require_once dirname( __DIR__, 2 ) . '/vendor/autoload.php';

// own helpers
require_once __DIR__ . '/helpers.php';

// wp tests environment
const WP_TESTS_CONFIG_FILE_PATH = __DIR__ . '/wp-tests-config.php';
require_once dirname( __DIR__, 2 ) . '/custom/wp-tests-lib/includes/bootstrap.php'; // uses WP_TESTS_CONFIG_FILE_PATH
