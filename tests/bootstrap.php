<?php
require_once __DIR__ . '/../vendor/autoload.php';

define('WP_TESTS_PHPUNIT_POLYFILLS_PATH', __DIR__ . '/../vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php');

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	// $_tests_dir = '/tmp/wordpress-tests-lib';
    $_tests_dir = '/var/folders/wf/4gsm9db91fzgr09sjjmpd8dc0000gn/T/wordpress-tests-lib';

}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __DIR__ ) . '/relevantly.php';
}
// load the plugin
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
