<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Modules;

if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly

} // End if


/**
 * Grants Module
 * @version 0.0.1
 * @author CAHNRS Communications, Danial Bleile
 *
 * Adds Grants post type to site
 * Adds Investigators taxonomy to site
 * Adds Status taxonomy to site
 * Adds Grant Categories taxonomy to site
 * Adds Grant metabox to Grants edit page
 *
 * @uses vendor/Settings_API_Adapter
 * @uses vendor/Save_Post_Data
 *
 */


class Grants_Module extends Core_Module {

	// @var string $version Current version of the module.
	public $version = '0.0.1';

	// @var string $slug Slug for the module (no spaces, dashes, or numbers).
	public $slug = 'core_grants'; // The ID for the module _ only.

	// @var array $register_args Args to use to register the module.
	public $register_args = array(
		'label'          => 'Grants', // (required) string Label of the Module on the activation page.
		'helper_text'    => 'Grants content type.', // (required) string Description of the Module on the activation page.
		'settings_page'  => array( // (optional) array Adds the setting page to WP.
			'page_title'     => 'Grants', // (required) string Settings page title.
			'menu_title'     => 'Grants', // (required) string Settings page menu label.
			'capabilities'   => 'manage_options', // (optional) string Capability level that can edit this page.
			'page_slug'      => 'core_grants', // (required) string Settings page slug - used in URL.
			'callback'       => 'render_options_page', // (optional) mixed Callback to render settings page.
		),
	);

	// @var array $save_args Passed to the Save_Post_API
	protected $save_args = array(
		'post_types'             => array( 'grants' ), // Post types to do save on
		'nonce_name'             => 'core_grants_module', // Nonce name used on the metabox or edit form
		'nonce_action'           => 'core_grants_module_save_post', // Nonce action used on the metabox or edit form
		'save_setting_callback'  => 'save_grant_setting', // Custom callback for editing data before save
		'add_actions'            => true, // Add save actions. Set to false if you want to add actions manually
	);

	// @var $post_settings Settings for the Save_Post_API to use.
	protected $post_settings = array(
		'_grant' => array( // Settings key
			'sanitize_type'      => 'custom', // Type of data - used to sanitize the data
			'default'            => '', // Default value
			'check_isset'        => true, // Do a check if isset, otherwise will use default value
			'ignore_empty'       => true, // Ignore if data is an empty string
			'sanitize_callback'  => 'sanitize_grants_post_meta', // Custom sanitization callback
		),
		'_grant_publications_content' => array( // Settings key
			'sanitize_type'      => 'custom', // Type of data - used to sanitize the data
			'default'            => '', // Default value
			'check_isset'        => true, // Do a check if isset, otherwise will use default value
			'ignore_empty'       => true, // Ignore if data is an empty string
			'sanitize_callback'  => 'sanitize_grants_content', // Custom sanitization callback
		),
		'_grant_funding_content' => array( // Settings key
			'sanitize_type'      => 'custom', // Type of data - used to sanitize the data
			'default'            => '', // Default value
			'check_isset'        => true, // Do a check if isset, otherwise will use default value
			'ignore_empty'       => true, // Ignore if data is an empty string
			'sanitize_callback'  => 'sanitize_grants_content', // Custom sanitization callback
		),
		'_grant_impacts_content' => array( // Settings key
			'sanitize_type'      => 'custom', // Type of data - used to sanitize the data
			'default'            => '', // Default value
			'check_isset'        => true, // Do a check if isset, otherwise will use default value
			'ignore_empty'       => true, // Ignore if data is an empty string
			'sanitize_callback'  => 'sanitize_grants_content', // Custom sanitization callback
		),
		'_grant_admin_content' => array( // Settings key
			'sanitize_type'      => 'custom', // Type of data - used to sanitize the data
			'default'            => '', // Default value
			'check_isset'        => true, // Do a check if isset, otherwise will use default value
			'ignore_empty'       => true, // Ignore if data is an empty string
			'sanitize_callback'  => 'sanitize_grants_content', // Custom sanitization callback
		),
	);


	/**
	 * Init the module. This is called after from the 'init' action in the parent class.
	 * @since 0.0.1
	 */
	public function init() {

		// Register the post type with WP.
		$this->register_post_type();

		// Register the taxonomies with WP.
		$this->register_taxonomies();

		// Check if is WP admin.
		if ( is_admin() ) {

			/**
			 * Check to see if we are trying to edit a post and start adding the
			 * metabox. This is to keep from adding unnecessary actions to WP.
			 */
			// Start adding metabox to post edit page.
			add_action( 'load-post.php', array( $this, 'init_metabox' ) );

			// Start adding metabox to new post edit page.
			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );

		} // End if

		add_filter( 'core_grants_legacy_meta', array( $this, 'add_legacy_support' ), 10, 2 );

		add_filter( 'the_content', array( $this, 'add_grant_meta_content' ), 2 );

		add_filter( 'core_post_feed_items_html', array( $this, 'add_grant_list_display' ), 10, 3 );

		add_filter( 'core_post_feed_local_item_array', array( $this, 'add_grant_item_values' ), 10, 3 );

	} // End init


	public function add_grant_item_values( $item, $post_id, $atts ) {

		if ( 'grants' === $item['post_type'] ) {

			$post = get_post( $post_id );

			// Get the grants meta data - stored as an array under a single key.
			$grants_meta = get_post_meta( $post->ID, '_grant', true );

			$grants_array = array(
				'project_id'           => ( ! empty( $grants_meta['project_id'] ) ) ? $grants_meta['project_id'] : '', // string Project ID
				'annual_entries'       => $this->get_annual_entries( $post ), // array Annual entries array.
			);

			$item['project_id']    = $grants_array['project_id'];
			$item['pi']            = array();

			if ( is_array( $grants_array['annual_entries'] ) && ! empty( $grants_array['annual_entries'] ) ) {

				foreach ( $grants_array['annual_entries'] as $entry ) {

					if ( ! empty( $entry['year'] ) && ! empty( $entry['pi'] ) ) {

						$investigators = $this->get_investigators( $entry['pi'] );

						foreach ( $investigators as $id => $name ) {

							if ( ! in_array( $name, $item['pi'], true ) ) {

								$item['pi'][] = $name;

							} // End if
						} // End foreach
					} // End if
				} // End foreach
			} // End if
		} // End if

		return $item;

	} // End add_grant_item_values


	public function add_grant_list_display( $html, $items, $atts ) {

		if ( ! empty( $atts['display'] ) && 'grant-list' === $atts['display'] ) {

			ob_start();

			foreach ( $items as $item ) {

				//var_dump( $item );
				$title_tag  = ( ! empty( $atts['title_tag'] ) ) ? $atts['title_tag'] : 'h3';
				$project_id = ( ! empty( $item['project_id'] ) ) ? $item['project_id'] : '';
				$title      = ( ! empty( $item['title'] ) ) ? $item['title'] : '';
				$link       = ( ! empty( $item['link'] ) ) ? $item['link'] : '';
				$pi         = ( ! empty( $item['pi'] ) ) ? implode( ', ', $item['pi'] ) : '';

				include __DIR__ . '/displays/grant-list.php';

			} // End foreach

			$html .= ob_get_clean();

		} // End if

		return $html;

	} // End add_grant_list_display


	public function add_legacy_support( $grants_array, $post ) {

		// Get the grants meta data - stored as an array under a single key.
		$grants_meta = get_post_meta( $post->ID, '_grant', true );

		if ( empty( $grants_array['publications_content'] ) && ! empty( $grants_meta['publications'] ) ) {

			$grants_array['publications_content'] = $grants_meta['publications'];

		} // End if

		if ( empty( $grants_array['funding_content'] ) && ! empty( $grants_meta['additional_funds'] ) ) {

			$grants_array['funding_content'] = $grants_meta['additional_funds'];

		} // End if

		if ( empty( $grants_array['impact_content'] ) && ! empty( $grants_meta['impacts'] ) ) {

			$grants_array['impact_content'] = $grants_meta['impacts'];

		} // End if

		if ( empty( $grants_array['admin_content'] ) && ! empty( $grants_meta['admin_comments'] ) ) {

			$grants_array['admin_content'] = $grants_meta['admin_comments'];

		} // End if

		if ( empty( $grants_array['additional_funding'][0]['amount'] ) && ! empty( $grants_meta['csanr_funds'] ) ) {

			$grants_array['additional_funding'][0]['label'] = 'CSANR Funds';
			$grants_array['additional_funding'][0]['amount'] = $grants_meta['csanr_funds'];

		} // End if

		if ( empty( $grants_array['additional_funding'][1]['amount'] ) && ! empty( $grants_meta['arc_funds'] ) ) {

			$grants_array['additional_funding'][1]['label'] = 'ARC Funds';
			$grants_array['additional_funding'][1]['amount'] = $grants_meta['arc_funds'];

		} // End if

		return $grants_array;

	}


	/**
	 * Sanitize the post data before saving. This is a custom callback passed in the 'sanitize_callback' of the setting.
	 * @since 0.0.1
	 */
	public function sanitize_grants_post_meta( $key, $sent_value ) {

		$clean = array(
			'project_id'       => ( ! empty( $sent_value['project_id'] ) ) ? sanitize_text_field( $sent_value['project_id'] ) : '',
			'annual_entries'   => ( ! empty( $sent_value['annual_entries'] ) ) ? $this->save_api->sanitize_array( $sent_value['annual_entries'] ) : array(),
			'additional_funding' => ( ! empty( $sent_value['additional_funding'] ) ) ? $this->save_api->sanitize_array( $sent_value['additional_funding'] ) : array(),
		);

		return $clean;

	} // End sanitize_grants_post_meta


	/**
	 * Sanitize the post data before saving. This is a custom callback passed in the 'sanitize_callback' of the setting.
	 * @since 0.0.1
	 */
	public function sanitize_grants_content( $key, $sent_value ) {

		$clean = wp_kses_post( $sent_value );

		return $clean;

	} // End sanitize_grants_content


	/**
	 * Init the metabox for the Grants page.
	 * @since 0.0.1
	 */
	public function init_metabox() {

		// Action for adding the metabox to post type.
		add_action( 'add_meta_boxes', array( $this, 'add_grant_meta_box' ) );

	} // End init_metabox


	public function add_grant_meta_content( $content ) {

		if ( is_singular( 'grants' ) ) {

			$post_id = get_the_ID();

			$post = get_post( $post_id );

			// Get the grants meta data - stored as an array under a single key.
			$grants_meta = get_post_meta( $post->ID, '_grant', true );

			$grants_array = array(
				'publications_content' => get_post_meta( $post->ID, '_grant_publications_content', true ), // string HTML for publications.
				'funding_content'      => get_post_meta( $post->ID, '_grant_funding_content', true ), // string HTML for funding.
				'impact_content'       => get_post_meta( $post->ID, '_grant_impacts_content', true ), // string HTML for impact.
				'admin_content'        => get_post_meta( $post->ID, '_grant_admin_content', true ), // string HTML for admin.
				'project_id'           => ( ! empty( $grants_meta['project_id'] ) ) ? $grants_meta['project_id'] : '', // string Project ID
				'status'               => $this->get_grant_status( $post->ID ),
				'investigators'        => $this->get_investigators_terms(), // array Term_ID => Term Name Investigators taxonomy terms.
				'annual_entries'       => $this->get_annual_entries( $post ), // array Annual entries array.
				'additional_funding'   => $this->get_additional_funds( $post ), // array Additional funds array.
			);

			$grants_array = apply_filters( 'core_grants_legacy_meta', $grants_array, $post );

			$status               = $grants_array['status'];
			$publications_content = $grants_array['publications_content'];
			$funding_content      = $grants_array['funding_content'];
			$impact_content       = $grants_array['impact_content'];
			$admin_content        = $grants_array['admin_content'];
			$project_id           = $grants_array['project_id'];
			$investigators        = $grants_array['investigators'];
			$annual_entries       = $grants_array['annual_entries'];
			$additional_funding   = $grants_array['additional_funding'];

			ob_start();

			echo '<div class="core-grant-content">';

			include __DIR__ . '/displays/grant-meta-content.php';

			foreach ( $annual_entries as $entry ) {

				if ( ! empty( $entry['year'] ) ) {

					$year                       = ( ! empty( $entry['year'] ) ) ? $entry['year'] : '';
					$progress_report_url        = ( ! empty( $entry['progress'] ) ) ? $entry['progress'] : '';
					$additional_progress_report = ( ! empty( $entry['additional_progress'] ) ) ? $entry['additional_progress'] : '';
					$grant_amount               = ( ! empty( $entry['amount'] ) ) ? $entry['amount'] : '';
					$pi                         = ( ! empty( $entry['pi'] ) ) ? $this->get_investigators( $entry['pi'] ) : array();
					$additional_investigators   = ( ! empty( $entry['additional'] ) ) ? $this->get_investigators( $entry['additional'] ) : array();
					$students                   = ( ! empty( $entry['students'] ) ) ? $this->get_investigators( $entry['students'] ) : array();

					include __DIR__ . '/displays/grant-annual-entry-content.php';

				} // End if
			} // End foreach

			include __DIR__ . '/displays/grant-content.php';

			echo '</div>';

			$content .= ob_get_clean();

		} // End if

		return $content;

	} // End add_grant_meta_content


	private function get_grant_status( $post_id ) {

		$status = array();

		$status_terms = wp_get_post_terms( $post_id, 'status' );

		if ( is_array( $status_terms ) ) {

			foreach ( $status_terms as $term ) {

				$status[] = $term->name;

			} // End foreach
		} // End if

		return implode( ', ', $status );

	} // End get_grant_status


	private function get_investigators( $term_ids ) {

		$investigators = array();

		if ( is_array( $term_ids ) ) {

			foreach ( $term_ids as $term_id ) {

				$term = get_term( $term_id, 'investigators' );

				if ( $term ) {

					$investigators[] = $term->name;

				} // End if
			} // End foreach
		} // End if

		return $investigators;

	} // End get_investigators


	/**
	 * Add action for the metabox for the Grants page.
	 * @since 0.0.1
	 */
	public function add_grant_meta_box() {

		// Wp action for adding metabox.
		add_meta_box(
			'core_grant_info',
			'Grant Information',
			array( $this, 'the_grant_metabox' )
		);

	} // End add_grant_meta_box


	public function save_grant_setting( $key, $value, $post_id, $data, $settings ) {

		return $value;

	} // End save_grant_setting


	/**
	 * Add the metabox for the Grants page.
	 * @since 0.0.1
	 *
	 * @var WP_Post $post WP post object.
	 */
	public function the_grant_metabox( $post ) {

		// Check if grants post type
		if ( 'grants' === $post->post_type ) {

			// Get the grants meta data - stored as an array under a single key.
			$grants_meta = get_post_meta( $post->ID, '_grant', true );

			if ( isset( $_GET['debut'] ) ) {

				var_dump( $grants_meta );

			};

			// Add nonce field to metabox.
			wp_nonce_field( 'core_grants_module_save_post', 'core_grants_module' );

			$grants_array = array(
				'publications_content' => get_post_meta( $post->ID, '_grant_publications_content', true ), // string HTML for publications.
				'funding_content'      => get_post_meta( $post->ID, '_grant_funding_content', true ), // string HTML for funding.
				'impact_content'       => get_post_meta( $post->ID, '_grant_impacts_content', true ), // string HTML for impact.
				'admin_content'        => get_post_meta( $post->ID, '_grant_admin_content', true ), // string HTML for admin.
				'project_id'           => ( ! empty( $grants_meta['project_id'] ) ) ? $grants_meta['project_id'] : '', // string Project ID
				'investigators'        => $this->get_investigators_terms(), // array Term_ID => Term Name Investigators taxonomy terms.
				'annual_entries'       => $this->get_annual_entries( $post ), // array Annual entries array.
				'additional_funding'   => $this->get_additional_funds( $post ), // array Additional funds array.
			);

			$grants_array = apply_filters( 'core_grants_legacy_meta', $grants_array, $post );

			$publications_content = $grants_array['publications_content'];
			$funding_content      = $grants_array['funding_content'];
			$impact_content       = $grants_array['impact_content'];
			$admin_content        = $grants_array['admin_content'];
			$project_id           = $grants_array['project_id'];
			$investigators        = $grants_array['investigators'];
			$annual_entries       = $grants_array['annual_entries'];
			$additional_funding   = $grants_array['additional_funding'];

			// Get the content to display.
			include __DIR__ . '/displays/grants-meta-box.php';

		} // End if

	} // End if


	/**
	 * Get the terms for the investigators post type
	 * @since 0.0.1
	 *
	 * @return array Investigators terms as term_id => label.
	 */
	protected function get_investigators_terms() {

		// Add terms to this array later on.
		$terms = array();

		// Get the term objects and don't include empty ones.
		$term_objs = get_terms( 'investigators', array( 'hide_empty' => false ) );

		// Check if is array, will be WP_Error object.
		if ( is_array( $term_objs ) ) {

			// Loop through the term objects.
			foreach ( $term_objs as $term ) {

				// Add to the terms array with term_id as key and name as value.
				$terms[ $term->term_id ] = $term->name;

			} // End foreach
		} // End if

		// Return the filled in terms.
		return $terms;

	} // End get_investigators_terms


	/**
	 * Get the array of additional funds and set defaults
	 * @since 0.0.1
	 *
	 * @return array Additional funds as an array.
	 */
	protected function get_additional_funds( $post ) {

		// Default funds array
		$funds = array(
			array(
				'label' => '',
				'amount' => '',
			),
			array(
				'label' => '',
				'amount' => '',
			),
			array(
				'label' => '',
				'amount' => '',
			),
		);

		// Get the grants meta data - stored as an array under a single key.
		$grants_meta = get_post_meta( $post->ID, '_grant', true );

		// Check if the value is array, will return empty string if not set.
		// Check if additional funds has a value.
		if ( is_array( $grants_meta ) && ! empty( $grants_meta['additional_funding'] ) ) {

			// Set this to a var - somewhat redundant to below.
			$funds_meta = $grants_meta['additional_funding'];

			// Loop through set funds.
			foreach ( $funds_meta as $index => $fund ) {

				// Add to funds array.
				$funds[ $index ] = $fund;

			} // End foreach
		} // End if

		// Return funds array.
		return $funds;

	} // End get_additional_funds


	/**
	 * Get the array of annual entries
	 * @since 0.0.1
	 *
	 * @var WP_Post WP post object.
	 *
	 * @return array Annual entries with defaults.
	 */
	protected function get_annual_entries( $post ) {

		// Add terms to this array later on.
		$annual_entries = array();

		// Get the grants meta data - stored as an array under a single key.
		$grants_meta = get_post_meta( $post->ID, '_grant', true );

		// Default entry data
		$entry_data = array(
			'year'                => '',
			'pi'                  => array(),
			'additional'          => array(),
			'students'            => array(),
			'progress'            => '',
			'additional_progress' => '',
			'amount'              => '',
			'title'               => 'New Annual Entry',

		);

		// Check if the value is array, will return empty string if not set.
		// Check if annual_entries has a value.
		if ( is_array( $grants_meta ) && ! empty( $grants_meta['annual_entries'] ) ) {

			// Loop through annual entries.
			foreach ( $grants_meta['annual_entries'] as $index => $entry ) {

				if ( ! empty( $entry['year'] ) ) {

					// Set the entry title from the year.
					$entry['title'] = ( ! empty( $entry['year'] ) ) ? 'Entry For: ' . $entry['year'] : 'Error In Entry';

					// Merge entry with default data (will overwrite default) and add to $annual_entries.
					$annual_entries[ $index ] = array_merge( $entry_data, $entry );

				} // End if
			} // End foreach;
		} // End if

		// Add and empty entry to the array for adding a new entry.
		$annual_entries[] = $entry_data;

		// Return array.
		return $annual_entries;

	} // End get_annual_entries


	/**
	 * Register the post type with WP.
	 * @since 0.0.1
	 */
	protected function register_post_type() {

		$labels = array(
			'name'               => _x( 'Grants', 'post type general name', 'cahnrswsuwp-plugin-core' ),
			'singular_name'      => _x( 'Grant', 'post type singular name', 'cahnrswsuwp-plugin-core' ),
			'menu_name'          => _x( 'Grants', 'admin menu', 'cahnrswsuwp-plugin-core' ),
			'name_admin_bar'     => _x( 'Grant', 'add new on admin bar', 'cahnrswsuwp-plugin-core' ),
			'add_new'            => _x( 'Add New', 'grant', 'cahnrswsuwp-plugin-core' ),
			'add_new_item'       => __( 'Add New Grant', 'cahnrswsuwp-plugin-core' ),
			'new_item'           => __( 'New Grant', 'cahnrswsuwp-plugin-core' ),
			'edit_item'          => __( 'Edit Grant', 'cahnrswsuwp-plugin-core' ),
			'view_item'          => __( 'View Grant', 'cahnrswsuwp-plugin-core' ),
			'all_items'          => __( 'All Grants', 'cahnrswsuwp-plugin-core' ),
			'search_items'       => __( 'Search Grants', 'cahnrswsuwp-plugin-core' ),
			'parent_item_colon'  => __( 'Parent Grants:', 'cahnrswsuwp-plugin-core' ),
			'not_found'          => __( 'No grants found.', 'cahnrswsuwp-plugin-core' ),
			'not_found_in_trash' => __( 'No grants found in Trash.', 'cahnrswsuwp-plugin-core' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'cahnrswsuwp-plugin-core' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		);

		register_post_type( 'grants', $args );

	} // End register_post_type


	/**
	 * Register the taxonomies with WP.
	 * @since 0.0.1
	 */
	protected function register_taxonomies() {

		// Investigators Taxonomy

		$investigator_labels = array(
			'name'              => _x( 'Investigators', 'taxonomy general name', 'cahnrswsuwp-plugin-core' ),
			'singular_name'     => _x( 'Investigator', 'taxonomy singular name', 'cahnrswsuwp-plugin-core' ),
			'search_items'      => __( 'Search Investigators', 'cahnrswsuwp-plugin-core' ),
			'all_items'         => __( 'All Investigators', 'cahnrswsuwp-plugin-core' ),
			'parent_item'       => __( 'Parent Investigator', 'cahnrswsuwp-plugin-core' ),
			'parent_item_colon' => __( 'Parent Investigator:', 'cahnrswsuwp-plugin-core' ),
			'edit_item'         => __( 'Edit Investigator', 'cahnrswsuwp-plugin-core' ),
			'update_item'       => __( 'Update Investigator', 'cahnrswsuwp-plugin-core' ),
			'add_new_item'      => __( 'Add New Investigator', 'cahnrswsuwp-plugin-core' ),
			'new_item_name'     => __( 'New Investigator Name', 'cahnrswsuwp-plugin-core' ),
			'menu_name'         => __( 'Investigator', 'cahnrswsuwp-plugin-core' ),
		);

		$investigator_args = array(
			'hierarchical'      => true,
			'labels'            => $investigator_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
		);

		// Register taxonomy and add to the grants post type
		register_taxonomy( 'investigators', array( 'grants' ), $investigator_args );

		// Status Taxonomy

		$status_labels = array(
			'name'              => _x( 'Status', 'taxonomy general name', 'cahnrswsuwp-plugin-core' ),
			'singular_name'     => _x( 'Status', 'taxonomy singular name', 'cahnrswsuwp-plugin-core' ),
			'search_items'      => __( 'Search Status', 'cahnrswsuwp-plugin-core' ),
			'all_items'         => __( 'All Status', 'cahnrswsuwp-plugin-core' ),
			'parent_item'       => __( 'Parent Status', 'cahnrswsuwp-plugin-core' ),
			'parent_item_colon' => __( 'Parent Status:', 'cahnrswsuwp-plugin-core' ),
			'edit_item'         => __( 'Edit Status', 'cahnrswsuwp-plugin-core' ),
			'update_item'       => __( 'Update Status', 'cahnrswsuwp-plugin-core' ),
			'add_new_item'      => __( 'Add New Status', 'cahnrswsuwp-plugin-core' ),
			'new_item_name'     => __( 'New Status Name', 'cahnrswsuwp-plugin-core' ),
			'menu_name'         => __( 'Status', 'cahnrswsuwp-plugin-core' ),
		);

		$status_args = array(
			'hierarchical'      => true,
			'labels'            => $status_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
		);

		// Register taxonomy and add to the grants post type
		register_taxonomy( 'status', array( 'grants' ), $status_args );

		// Grant Category Taxonomy

		$categories_labels = array(
			'name'              => _x( 'Grant Categories', 'taxonomy general name', 'cahnrswsuwp-plugin-core' ),
			'singular_name'     => _x( 'Grant Category', 'taxonomy singular name', 'cahnrswsuwp-plugin-core' ),
			'search_items'      => __( 'Search Grant Categories', 'cahnrswsuwp-plugin-core' ),
			'all_items'         => __( 'All Grant Categories', 'cahnrswsuwp-plugin-core' ),
			'parent_item'       => __( 'Parent Grant Category', 'cahnrswsuwp-plugin-core' ),
			'parent_item_colon' => __( 'Parent Grant Category:', 'cahnrswsuwp-plugin-core' ),
			'edit_item'         => __( 'Edit Grant Category', 'cahnrswsuwp-plugin-core' ),
			'update_item'       => __( 'Update Grant Category', 'cahnrswsuwp-plugin-core' ),
			'add_new_item'      => __( 'Add New Grant Category', 'cahnrswsuwp-plugin-core' ),
			'new_item_name'     => __( 'New Grant Category Name', 'cahnrswsuwp-plugin-core' ),
			'menu_name'         => __( 'Grant Category', 'cahnrswsuwp-plugin-core' ),
		);

		$categories_args = array(
			'hierarchical'      => true,
			'labels'            => $categories_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
		);

		// Register taxonomy and add to the grants post type
		register_taxonomy( 'grant-categories', array( 'grants' ), $categories_args );

	} // End register_taxonomies


	/**
	 * Add admin settings page content.
	 * @since 0.0.1
	 */
	public function add_admin_settings() {

	} // End add_settings

} // End Sub_Layouts


// Initialize the class.
$ccore_grants_module = new Grants_Module();
