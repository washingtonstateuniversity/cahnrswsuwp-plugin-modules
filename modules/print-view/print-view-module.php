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
class Print_View_Module extends Core_Module {

	public $slug = 'print_view';

	public $register_args = array(
		'label'          => 'Print View',
		'helper_text'    => 'Custom Print View for Content.',
		'settings_page'  => array(
			'page_title'     => 'Print View Settings',
			'menu_title'     => 'Print View',
			'capabilities'   => 'manage_options',
			'page_slug'      => 'core_print_view',
			'callback'       => 'render_options_page',
		),
	);


	/**
	 * Init the module here
	 */
	public function init() {

		if ( isset( $_REQUEST['print-view'] ) ) {

			add_filter( 'template_include', array( $this, 'get_print_template' ), 99999 );

		} // End if

		$this->add_print_sidebars();

		add_filter( 'the_content', array( $this, 'add_print_option' ), 9999999 );

	} // End init


	public function add_print_option( $title ) {

		if ( is_singular() && ! is_front_page() ) {

		$post_type = get_post_type();

		if ( ! empty( $post_type ) ) {

			$show_print = get_option( 'core_show_print_' . $post_type, false );

				if ( ! empty( $show_print ) ) {

					$post_id = get_the_ID();

					$link = get_permalink( $post_id ) . '?print-view=true';

					$title = '<a href="' . esc_url( $link ) . '" class="core-show-print">View Print Version</a>' . $title;

					remove_filter( 'the_content', array( $this, 'add_print_option' ), 9999999 );

				} // End if
			} // End if
		} // End if

		return $title;

	} // End if


	public function add_print_sidebars() {

		$header_args = array(
			'name'          => 'Print View: Header',
			'id'            => 'print_header',    // ID should be LOWERCASE  ! ! !
			'description'   => '',
			'class'         => '',
			'before_widget' => '<div class="print-view-widget">',
			'after_widget'  => '</div',
			'before_title'  => '',
			'after_title'   => '',
		);

		$footer_args = array(
			'name'          => 'Print View: Footer',
			'id'            => 'print_footer',    // ID should be LOWERCASE  ! ! !
			'description'   => '',
			'class'         => '',
			'before_widget' => '<div class="print-view-widget">',
			'after_widget'  => '</div',
			'before_title'  => '',
			'after_title'   => '',
		);

		register_sidebar( $header_args );

		register_sidebar( $footer_args );

	} // End add_print_sidebars


	public function get_print_template( $template ) {

		$template = __DIR__ . '/print-view-template.php';

		return $template;

	} // End get_print_template


	public function get_post_types_select( $exclude = array(), $public = true ) {

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


	public function add_admin_settings() {

		$settings_adapter = get_settings_api_adapter();

		$page_slug = $this->get_settings_page_slug();

		$section = $this->slug . '_section';

		$post_types = $this->get_post_types_select();

		foreach ( $post_types as $slug => $label ) {

			$this->settings[ 'core_show_print_' . $slug ] = array(
				'type'              => 'string',
				'description'       => 'Show print option on ' . $label,
				'show_in_rest'      => false,
				'default'           => '',
			);

		} // End foreach

		// Register settings

		$settings_adapter->register_settings(
			$page_slug,
			$this->settings
		);

		$settings_adapter->add_section(
			$section,
			'Set show print options for post types',
			$page_slug,
			''
		);

		foreach ( $post_types as $slug => $label ) {

			$settings_adapter->add_select_field(
				'core_show_print_' . $slug,
				'Show Print on ' . $label,
				$page_slug,
				$section,
				array( 
					0 => 'NO',
					1 => 'YES',
				),
				get_option( 'core_show_print_' . $slug )
			);

		} // End foreach

	} // End add_settings


} // End Sub_Layouts

$ccore_print_view_module = new Print_View_Module();
