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
class Extension_Publications_Module extends Core_Module {

	public $slug = 'extension_publications';

	public $register_args = array(
		'label'          => 'Extension Publications',
		'helper_text'    => 'Shortcode for Extension Publications.',
	);

	public $default_atts = array(
		'per_page'       => -1,
		'protocol'       => 'http',
		'host'           => 'pubs.cahnrs.wsu.edu/publications',
		'rest_path'      => '/wp-json/wp/v2/',
		'endpoint'       => 'publication',
		'tags'           => '',
		'categories'     => '',
		'show_images'    => '1',
		'show_summary'   => '1',
		'order'          => 'alpha',
		'topics'         => '',
		'topic_relation' => 'AND',
	);


	/**
	 * Init the module here
	 */
	public function init() {

		add_shortcode( 'extension_publications', array( $this, 'render_shortcode' ) );

	} // End init


	public function render_shortcode( $atts, $content = '', $tag ) {

		$html = '';

		$atts = shortcode_atts( $this->default_atts, $atts, $tag );

		$request_url = $this->get_request_url( $atts );

		$publications = $this->get_publications( $request_url, $atts );

		foreach ( $publications as $publication ) {

			$html .= $this->get_publications_card( $publication, $atts );

		} // End foreach

		return $html;

	} // End render_shortcode


	protected function get_publications_card( $publication, $atts ) {

		$title = $publication['title'];
		$link = $publication['link'];
		$image = $publication['img_src'];
		$summary = $publication['summary'];

		ob_start();

		include __DIR__ . '/displays/publication-card.php';

		return ob_get_clean();

	} // End get_publications_card


	protected function get_publications( $request_url, $atts ) {

		$publications = array();

		$args = array(
			'sslverify'   => false,
			'timeout'     => 10,
		);

		$response = wp_remote_get( $request_url, $args );

		if ( is_array( $response ) ) {

			$body = wp_remote_retrieve_body( $response );

			$publications_array = json_decode( $body );

			if ( is_array( $publications_array ) ) {

				foreach ( $publications_array as $pub ) {

					$summary = $pub->excerpt->rendered;

					$summary = wp_strip_all_tags( $summary );

					$summary = wp_trim_words( $summary, 25 );

					$publication = array(
						'title' => $pub->title->rendered,
						'link' => $pub->link,
						'img_src' => $pub->image_url,
						'summary' => $summary,
					);

					$publications[] = $publication;

				} // End foreach
			} // end if
		} // End if

		return $publications;

	}


	public function get_request_url( $atts ) {

		$query_params = array();

		if ( ! empty( $atts['categories'] ) ) {

			$query_params['categories'] = $atts['categories'];

		} // End if

		if ( ! empty( $atts['tags'] ) ) {

			$query_params['tags'] = $atts['tags'];

		} // End if

		if ( ! empty( $atts['topics'] ) ) {

			$query_params['topic'] = $atts['topics'];

		} // End if

		$request_url = $atts['protocol'] . '://' . $atts['host'] . $atts['rest_path'] . $atts['endpoint'];

		if ( ! empty( $query_params ) ) {

			$request_url .= '?' . http_build_query( $query_params );

		} // End if

		return $request_url;

	}


} // End Sub_Layouts

$ccore_extension_publications_module = new Extension_Publications_Module();
