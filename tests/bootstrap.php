<?php
/**
 * Bootstrap file for PHPUnit tests.
 * Loads the WordPress test suite and the plugin itself.
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
    $_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
    echo "Could not find $_tests_dir/includes/functions.php" . PHP_EOL;
    exit( 1 );
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Load the plugin before WordPress is fully set up.
 */
function _load_darkadmin_plugin() {
    require dirname( __DIR__ ) . '/darkadmin.php';
}
tests_add_filter( 'muplugins_loaded', '_load_darkadmin_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
