<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Modules;

if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly

} // End if


class Scripts {


	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'add_public_scripts' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ) );

	}


	public function add_public_scripts( $hook ) {

		// TODO make version pull from plugin version
		wp_enqueue_style( 'core-css', ccore_get_plugin_url() . '/css/core-public.css', array(), '0.0.1' );

		// TODO make version pull from plugin version
		wp_enqueue_script( 'core-js', ccore_get_plugin_url() . '/js/core-public.js', array( 'jquery' ), '0.0.1', true );

	} // End add_public_scripts


	public function add_admin_scripts( $hook ) {

		if ( 'post-new.php' === $hook || 'post.php' == $hook ) {

			// TODO make version pull from plugin version
			wp_enqueue_style( 'core-admin-post-editor-css', ccore_get_plugin_url() . '/css/core-edit-post.css', array(), '0.0.1' );

		} // End if

	} // End add_public_scripts


} // End Modules

$ccore_scripts = new Scripts();
