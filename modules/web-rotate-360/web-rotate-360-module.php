<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Core;

if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly

} // End if


/**
 * Uses theme filters to add sub layouts to the page with sidebars.
 *
 * @version 0.0.1
 * @author CAHNRS Communications, Danial Bleile
 */
class Web_Rotate_Module extends Core_Module {

	public $slug = 'web_rotate';

	public $register_args = array(
		'label'          => 'Web Rotate 360',
		'helper_text'    => 'Web Rotator Viewer.',
	);

	public $default_atts = array(
		'xml'     => '',
		'height'  => '550px',
		'width'   => '100%',
	);


	/**
	 * Init the module here
	 */
	public function init() {

		add_shortcode( 'web_rotator', array( $this, 'render_shortcode' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'add_public_scripts' ) );

	} // End init


	public function add_public_scripts() {

		// TODO make version pull from plugin version
		wp_enqueue_style( 'web-rotator-css', ccore_get_plugin_url() . 'modules/web-rotate-360/css/thin.css', array(), '0.0.1' );

		// TODO make version pull from plugin version
		wp_enqueue_script( 'web-rotator-js', ccore_get_plugin_url() . 'modules/web-rotate-360/js/imagerotator.js', array( 'jquery' ), '0.0.1', false );

	} // End add_public_scripts


	public function render_shortcode( $atts, $content = '', $tag ) {

		$html = '';

		$atts = shortcode_atts( $this->default_atts, $atts, $tag );

		$height = $atts['height'];

		$width = $atts['width'];

		$xml = $atts['xml'];

		ob_start();

		include __DIR__ . '/displays/web-rotator.php';

		$html = ob_get_clean();

		return $html;

	} // End render_shortcode


} // End Sub_Layouts

$web_rotate_module = new Web_Rotate_Module();
