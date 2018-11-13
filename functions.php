<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Modules;

/**
 * Plugin functions go here
 */


function ccore_get_plugin_dir( $path = '' ) {

	return __DIR__ . $path;

} // End ccore_get_plugin_dir


function ccore_get_plugin_url( $path = '' ) {

	return plugin_dir_url( __FILE__ ) . $path;

} // End ccore_get_plugin_dir


function get_settings_api_adapter() {

	$settings_api = new \Settings_API_Adapter();

	return $settings_api;

} // End get_settings_api_adapter

/**
 * Registers a module with the core settings
 * @since 0.0.6
 *
 * @param string $slug Slug for the module
 * @param array $args Plugin args
 *
 * @return bool True if registered, false if not
 */
function ccore_register_module( $slug, $args = array() ) {

	if ( empty( $slug ) ) {

		return false;

	} else {

		// Default args used to register module
		$default_args = array(
			'icon'           => ccore_get_plugin_url() . '/images/icons/default-icon.png', // Image icon used for module settings page
			'label'          => 'Why you no add Label?', // Label used for module settings page
			'helper_text'    => '', // Additional module info
			'settings_page'  => false, // Add setting page under core modules
			'default_active' => 0, // Module default on or off
			'priority'       => 10, // Order to display module (Alpha if set to 10)
			'restrict_admin' => 0, // Restrict to Network/Global admin on network install
		);

		$args = array_merge( $default_args, $args );

		global $ccore_modules;

		// Check if ccore_modules is an array, if not make it one.
		if ( ! is_array( $ccore_modules ) ) {

			$ccore_modules = array();

		} // End if

		$ccore_modules[ $slug ] = $args;

		return true;

	} // end if

} // end ccore_register_module


function ccore_get_registered_modules() {

	global $ccore_modules;

	// Check if ccore_modules is an array, if not make it one.
	if ( ! is_array( $ccore_modules ) ) {

		$ccore_modules = array();

	} // End if

	return $ccore_modules;

} // End ccore_get_registered_modules


/**
 * Check if module is active for cahnrs core
 * @since 0.0.6
 *
 * @param string $slug Slug for the module
 *
 * @return bool True if active, false if not
 */
function ccore_is_active_module( $slug ) {

	// TO DO: Build out settings page for activating individual modules
	return true;

} // End ccore_is_active_module

/**
 * Get post types as slug => label
 * @since 0.0.6
 *
 * @param array $exclude Post types to exclude
 * @param bool $public Include only public post types
 *
 * @return array
 */
function ccore_get_post_types_select( $exclude = array(), $public = true ) {

	$post_type_select = array();

	$post_types = get_post_types(
		array(
			'public' => $public,
		),
		'objects'
	);

	foreach ( $post_types as $post_type ) {

		$post_type_select[ $post_type->name ] = $post_type->label;

	} // End foreach

	return $post_type_select;

} // End get_post_types_select

/**
 * Get post types as slug => label
 * @since 0.0.6
 *
 * @param bool $as_select Return as ID => Name
 *
 * @return array
 */
function ccore_get_registered_sidebars( $as_select = true ) {

	$sidebars = array();

	global $wp_registered_sidebars;

	if ( $as_select ) {

		foreach ( $wp_registered_sidebars as $sidebar ) {

			$sidebars[ $sidebar['id'] ] = $sidebar['name'];

		} // end foreach
	} else {

		$sidebars = $wp_registered_sidebars;

	} // End if

	return $sidebars;

} // End ccore_get_registered_sidebars
