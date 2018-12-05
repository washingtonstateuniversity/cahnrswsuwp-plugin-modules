<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Modules;

if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly

} // End if


class Modules {


	public function __construct() {

		add_action( 'init', array( $this, 'add_modules' ), 1 );

	} // End __construct


	public function add_modules() {

		if ( class_exists( 'WSUWP\Plugin_Modules\Module' ) ) {

			include_once __DIR__ . '/modules/sub-layouts/sub-layouts-module.php';

		} // End if

		if ( function_exists( 'wsuwp_toolbox_register_module' ) ) {

			wsuwp_toolbox_register_module( 'cahnrswsuwp_sub_layouts', __NAMESPACE__ . '\Sub_Layouts_Module' );

		} // End if

	} // End add_modules


} // End Modules

$ccore_modules = new Modules();
