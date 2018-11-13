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
class Breadcrumbs_Module extends Core_Module {

	public $slug = 'core_breadcrumbs'; // The ID for the module _ only

	public $register_args = array(
		'label'          => 'Breadcrumbs', // Edit This | Shows on activate module panel
		'helper_text'    => 'Adds breadcrumbs to the page', // Edit This | Shows on activate module panel
	);

	/**
	 * Init the module here
	 */
	public function init() {

		add_action( 'theme_template_after_banner', array( $this, 'add_breadcrumb' ) );

	} // End init


	public function add_breadcrumb() {

		$breadcrumb_array = array(
			array(
				'title' => 'Home',
				'link'  => get_home_url(),
			),
			// Home here so it always exits
		);

		if ( is_singular() ) {

			$breadcrumb_array = array_merge( $breadcrumb_array, $this->get_breadcrumb_array_singular() );

		} // End If

			$breadcrumb_html = '<ul class="breadcrumbs">';

		foreach ( $breadcrumb_array as $crumb ) {

			$breadcrumb_html .= '<li><a href="' . $crumb['link'] . '">' . $crumb['title'] . '</a></li>';

		}

		$breadcrumb_html .= '</ul>';

		echo wp_kses_post( $breadcrumb_html );

		remove_action( 'theme_template_after_banner', array( $this, 'add_breadcrumb' ) );

	} // End add_breadcrumb

	protected function get_breadcrumb_array_singular() {

		$breadcrumbs = array();

		$post_id = \get_the_ID();

		$ancestors = get_post_ancestors( $post_id );
		$ancestors = array_reverse( $ancestors );

		foreach ( $ancestors as $ancestor ) {
			$title = get_the_title( $ancestor );
			$link = get_permalink( $ancestor );

			$temp = array(
				'title' => $title,
				'link'  => $link,
			);

			$breadcrumbs[] = $temp;

		} //End foreach

		$title = get_the_title( $post_id );
		$link = get_permalink( $post_id );

		$temp = array(
			'title' => $title,
			'link'  => $link,
		);

		$breadcrumbs[] = $temp;

		return $breadcrumbs;

	}//end get_breadcrumb_array_singular

} // End Sub_Layouts

$ccore_breadcrumbs_module = new Breadcrumbs_Module(); // Edit This
