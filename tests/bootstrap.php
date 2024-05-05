<?php
/**
 * PHPUnit bootstrap file
 *
 * Composer autoloader must be loaded before WP_PHPUNIT__DIR will be available.
 */

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Give access to tests_add_filter() function.
require_once getenv( 'WP_PHPUNIT__DIR' ) . '/includes/functions.php';

// Start up the WP testing environment.
putenv( sprintf( 'WP_PHPUNIT__TESTS_CONFIG=%s/wp-config.php', __DIR__ ) );  // phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_putenv
require getenv( 'WP_PHPUNIT__DIR' ) . '/includes/bootstrap.php';

require_once __DIR__ . '/src/Autoloader.php';
$feature_toggles_autoloader = \FeatureToggles\Autoloader::generate( 'FeatureToggles', dirname( __DIR__ ) . '/feature-toggles/classes' );
$feature_toggles_autoloader->register();
