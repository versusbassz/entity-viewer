<?php

namespace Versusbassz\WpBatcher\Tests;

const DEBUG = false;

/**
 * dump_method( __METHOD__ );
 *
 * @param $method string
 *
 * @return void
 */
function dump_method( $method ) {
	if ( php_sapi_name() !== 'cli' ) {
		return;
	}

	echo PHP_EOL;
	var_dump( $method );
	echo PHP_EOL;
}

function log( $message ) {
	DEBUG && dump( $message );
}
