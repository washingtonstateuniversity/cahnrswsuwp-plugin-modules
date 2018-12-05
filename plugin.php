<?php
/*
Plugin Name: CAHNRSWSUWP Toolbox
Version: 0.0.6
Description: Core feature set for CAHNRS sites.
Author: washingtonstateuniversity, Danial Bleile
Author URI: http://cahnrs.wsu.edu/communications/
Plugin URI: https://github.com/washingtonstateuniversity/CAHNRSWSUWP-Plugin-Core
Text Domain: cahnrswsuwp-plugin-core
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// This plugin uses namespaces and requires PHP 5.3 or greater.
if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
	add_action( 'admin_notices', create_function( '', // phpcs:ignore WordPress.PHP.RestrictedPHPFunctions.create_function_create_function
	"echo '<div class=\"error\"><p>" . __( 'WSUWP Plugin Skeleton requires PHP 5.3 to function properly. Please upgrade PHP or deactivate the plugin.', 'cahnrswsuwp-plugin-core' ) . "</p></div>';" ) );
	return;
} else {

	include_once __DIR__ . '/functions.php';

	include_once __DIR__ . '/cahnrswsuwp-modules.php';

	include_once __DIR__ . '/includes/scripts/include-scripts.php';

}
