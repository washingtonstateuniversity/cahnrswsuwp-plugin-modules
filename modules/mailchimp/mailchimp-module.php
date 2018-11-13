<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Modules;

if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly

} // End if


/**
 * Uses theme filters to add sub layouts to the page with sidebars.
 *
 * @version 0.0.1
 * @author CAHNRS Communications, Danial Bleile
 */
class Mailchimp_Module extends Core_Module {

	public $slug = 'mailchimp'; // The ID for the module _ only

	public $register_args = array(
		'label'          => 'Mailchimp Tools', // Edit This | Shows on activate module panel
		'helper_text'    => 'Mailchimp Forms & Tools.', // Edit This | Shows on activate module panel
		'settings_page'  => array(
			'page_title'     => 'Mailchimp Module Settings',
			'menu_title'     => 'Mailchimp',
			'capabilities'   => 'manage_options', // Don't touch | Role that can see this
			'page_slug'      => 'core_mailchimp', // Edit This | similar to ID
			'callback'       => 'render_options_page', // Don't touch
		),
	);

	/**
	 * These are the settings for the settings page above
	 */
	public $settings = array(
		'setting_key_id' => array( // Edit This
			'type'              => 'string', // Edit This | Used to sanitize the setting
			'description'       => 'Desc of the setting',
			'show_in_rest'      => false,
			'default'           => '', // Edit This | Default value of the setting
		),
	);


	public $default_atts = array(
		'title'       => 'Subscribe',
		'list_id'     => '',
		'account_key' => '',
		'show_names'  => '1',
		'title_tag'   => 'h2',
		'host'        => '',
	);


	/**
	 * Init the module here
	 */
	public function init() {

		add_shortcode( 'mailchimp_subscribe', array( $this, 'render_shortcode' ) );

		add_filter( 'the_excerpt_rss', array( $this, 'remove_email_tags' ), 9999 );

	} // End init


	public function remove_email_tags( $content ) {

		$content = wp_strip_all_tags( $content );

		return $content;

	} // End the_content_rss


	public function render_shortcode( $atts, $content = '', $tag ) {

		$html = '';

		$atts = shortcode_atts( $this->default_atts, $atts, $tag );

		$host       = $atts['host'];
		$key        = $atts['account_key'];
		$list_id    = $atts['list_id'];
		$title      = $atts['title'];
		$show_names = $atts['show_names'];
		$title_tag  = $atts['title_tag'];

		if ( ! empty( $key ) && ! empty( $list_id ) && ! empty( $host ) ) {

			ob_start();

			include __DIR__ . '/displays/subscribe-form.php';

			$html .= ob_get_clean();

		} else {
			
			$html .= '* Your subscribe form needs a list_id to work properly. You also might need to set the account and host under module settings if you haven\'t already';

		} // End if

		return $html;

	}

} // End Sub_Layouts

$ccore_mailchimp_module = new Mailchimp_Module(); // Edit This
