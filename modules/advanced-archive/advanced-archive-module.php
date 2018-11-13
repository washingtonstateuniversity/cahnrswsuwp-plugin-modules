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
class Advanced_Archive_Module extends Core_Module {

	public $slug = 'core_advanced_archive'; // The ID for the module _ only

	public $register_args = array(
		'label'          => 'Advanced Archive', // Edit This | Shows on activate module panel
		'helper_text'    => 'Add Archive Features.', // Edit This | Shows on activate module panel
		'settings_page'  => array(
			'page_title'     => 'Advanced Archive Settings',
			'menu_title'     => 'Advanced Archive',
			'capabilities'   => 'manage_options', // Don't touch | Role that can see this
			'page_slug'      => 'core_advanced_archive', // Edit This | similar to ID
			'callback'       => 'render_options_page', // Don't touch
		),
	);

	/**
	 * These are the settings for the settings page above
	 */
	public $settings = array(
		'core_archive_filters' => array( // Edit This
			'type'              => 'array-array', // Edit This | Used to sanitize the setting
			'description'       => 'Filters for Archives',
			'show_in_rest'      => false,
			'default'           => '', // Edit This | Default value of the setting
		),
	);


	/**
	 * Init the module here
	 */
	public function init() {

		add_action( 'template_before_content', array( $this, 'add_archive_filter' ) );

	} // End init


	public function add_archive_filter() {

		if ( is_post_type_archive() ) {

			$post_type = get_post_type();

			if ( $post_type ) {

				$archive_options = get_option( 'core_archive_filters', array() );

				if ( $this->check_has_filters( $post_type, $archive_options ) ) {

					echo '<form method="get" action="' . esc_url( get_post_type_archive_link( $post_type ) ) . '"><div class="core-advanced-archive-filter-wrapper">';

					$filters_settings = $archive_options[ $post_type ];

					for ( $i = 0; $i < 5; $i++ ) {

						if ( ! empty( $filters_settings[ $i ]['taxonomy'] ) ) {

							$this->the_filter( $filters_settings[ $i ] );

						} // End if
					} // End for

					echo '</div></form>';

				} // End if
			} // End if
		} // End if

	} // End add_archive_filter


	protected function check_has_filters( $post_type, $archive_options ) {

		if ( array_key_exists( $post_type, $archive_options ) ) {

			$filters_settings = $archive_options[ $post_type ];

			for ( $i = 0; $i < 5; $i++ ) {

				if ( ! empty( $filters_settings[ $i ]['taxonomy'] ) ) {

					return true;

				} // End if
			} // End for
		} // End for

		return false;

	} // End check_has_filters


	public function the_filter( $filter_settings ) {

		$html = '';

		$taxonomy = ( ! empty( $filter_settings['taxonomy'] ) ) ? $filter_settings['taxonomy'] : '';

		$label = ( ! empty( $filter_settings['label'] ) ) ? $filter_settings['label'] : '';

		$terms = ( ! empty( $filter_settings['terms'] ) ) ? $filter_settings['terms'] : '';

		$selected_value = ( isset( $_REQUEST[ $taxonomy ] ) ) ? sanitize_text_field( $_REQUEST[ $taxonomy ] ) : '';

		$term_filters = $this->get_fitler_terms( $taxonomy, $terms );

		include __DIR__ . '/displays/filter.php';

	} // End get_filter


	public function get_fitler_terms( $taxonomy, $terms ) {

		$term_filters = array();

		if ( ! empty( $terms ) ) {

			$term_ids = explode( ',', $terms );

			foreach ( $term_ids as $term_id ) {

				$term = get_term_by( 'id', $term_id, $taxonomy );

				$term_filters[ $term->slug ] = $term->name;

			} // End foreach
		} else {

			$args = array();

			$terms = get_terms( $taxonomy, $args );

			if ( is_array( $terms ) ) {

				foreach ( $terms as $index => $term ) {

					$term_filters[ $term->slug ] = $term->name;

				} // End foreach
			} // End if
		} // End if

		return $term_filters;

	} // End get_fitler_terms


	public function add_admin_settings() {

		$settings_adapter = get_settings_api_adapter(); // Don't touch | Custom settings wrapper to make using it easier

		$page_slug = $this->get_settings_page_slug(); // Don't touch | Gets the page slug for this setting

		$section = 'archive_section'; // Edit This | Define your section here

		// Register settings

		$settings_adapter->register_settings( // Don't touch | Registers all of your settings from $this->settings
			$page_slug,
			$this->get_settings()
		);

		$settings_adapter->add_section( // Edit This | Add a custom section
			$section,
			'Add Filters to Post Types',
			$page_slug,
			'' // Edit This | Descriptor text for the section
		);

		$post_types = $this->get_post_types_select();

		foreach ( $post_types as $slug => $label ) { // $id, $label, $page, $section, $args = array(), $callback = false

			$settings_adapter->add_custom_field( // Edit This | Add a select field
				'core_archive_filters[' . $slug . ']',
				$label . ' Archive Filters',
				$page_slug, // Don't touch
				$section, // Don't touch
				array(
					'name'      => 'core_archive_filters',
					'post_type' => $slug,
				),
				array( $this, 'the_post_archive_fields' )
			);

		} // End foreach

		/*$settings_adapter->add_select_field( // Edit This | Add a select field
			'setting_key_id',
			'Setting Label Here',
			$page_slug, // Don't touch
			$section, // Don't touch
			array( // Edit This | Select options as an array
				'value' => 'Value Label',
			),
			get_option( 'setting_key_id' ) // Edit This | Current value of 'setting_key_id'
		);*/

	} // End add_settings


	public function the_post_archive_fields( $args ) {

		$archive_options = get_option( 'core_archive_filters', array() );

		$post_type = ( ! empty( $args['post_type'] ) ) ? $args['post_type'] : false;

		$name = ( ! empty( $args['name'] ) ) ? $args['name'] : false;

		if ( $post_type && $name ) {

			$post_type_filters = ( ! empty( $archive_options[ $post_type ] ) ) ? $archive_options[ $post_type ] : array();

			for ( $i = 0; $i < 5; $i++ ) {

				$tax_filters = ( ! empty( $post_type_filters[ $i ] ) ) ? $post_type_filters[ $i ] : array();

				$taxonomy = ( ! empty( $tax_filters['taxonomy'] ) ) ? $tax_filters['taxonomy'] : '';

				$label = ( ! empty( $tax_filters['label'] ) ) ? $tax_filters['label'] : '';

				$terms = ( ! empty( $tax_filters['terms'] ) ) ? $tax_filters['terms'] : '';

				$name = $args['name'] . '[' . $args['post_type'] . '][' . $i . ']';

				include __DIR__ . '/displays/settings_filter_select.php';

			} // End for
		} // End if

	} // End the_post_archive_fields


	protected function get_post_types_select( $exclude = array(), $public = true ) {

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

} // End Sub_Layouts

$ccore_advanced_archive_module = new Advanced_Archive_Module(); // Edit This
