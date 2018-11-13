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
class Taxonomies_Module extends Core_Module {

	public $slug = 'core_taxonomies'; // The ID for the module _ only

	public $register_args = array(
		'label'          => 'Taxonomies', // Edit This | Shows on activate module panel
		'helper_text'    => 'Add custom taxonomies.', // Edit This | Shows on activate module panel
		'settings_page'  => array(
			'page_title'     => 'Select Taxonomies to Add',
			'menu_title'     => 'Taxnonmies',
			'capabilities'   => 'manage_options', // Don't touch | Role that can see this
			'page_slug'      => 'core_taxonomies', // Edit This | similar to ID
			'callback'       => 'render_options_page', // Don't touch
		),
	);

	/**
	 * These are the settings for the settings page above
	 */
	public $settings = array(
		'core_taxnonomies' => array( // Edit This
			'type'              => 'custom', // Edit This | Used to sanitize the setting
			'description'       => 'Array of Taxonomies',
			'show_in_rest'      => false,
			'default'           => '', // Edit This | Default value of the setting
		),
	);

	public $taxonomies = array(
		'topics' => 'Topics',
	);


	/**
	 * Init the module here
	 */
	public function init() {

		$current_taxonomies = get_option( 'core_taxnonomies', array() );

		if ( ! empty( $current_taxonomies ) ) {

			foreach ( $this->taxonomies as $slug => $label ) {

				if ( ! empty( $current_taxonomies[ $slug ] ) && is_array( $current_taxonomies[ $slug ] ) ) {

					switch ( $slug ) {

						case 'topics':
							$this->register_taxonomy_topics( $current_taxonomies[ $slug ] );
							break;

					} // End switch
				} // End if
			} // End foreach
		} // End if

	} // End init


	protected function register_taxonomy_topics( $post_types ) {

		$labels = array(
			'name'              => _x( 'Topics', 'taxonomy general name', 'cahnrswsuwp-plugin-core' ),
			'singular_name'     => _x( 'Topic', 'taxonomy singular name', 'cahnrswsuwp-plugin-core' ),
			'search_items'      => __( 'Search Topics', 'cahnrswsuwp-plugin-core' ),
			'all_items'         => __( 'All Topics', 'cahnrswsuwp-plugin-core' ),
			'parent_item'       => __( 'Parent Topic', 'cahnrswsuwp-plugin-core' ),
			'parent_item_colon' => __( 'Parent Topic:', 'cahnrswsuwp-plugin-core' ),
			'edit_item'         => __( 'Edit Topic', 'cahnrswsuwp-plugin-core' ),
			'update_item'       => __( 'Update Topic', 'cahnrswsuwp-plugin-core' ),
			'add_new_item'      => __( 'Add New Topic', 'cahnrswsuwp-plugin-core' ),
			'new_item_name'     => __( 'New Topic Name', 'cahnrswsuwp-plugin-core' ),
			'menu_name'         => __( 'Topic', 'cahnrswsuwp-plugin-core' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
		);

		register_taxonomy( 'topics', $post_types, $args );

	} // End register_taxonomy_topics


	public function add_admin_settings() {

		$settings_adapter = get_settings_api_adapter(); // Don't touch | Custom settings wrapper to make using it easier

		$page_slug = $this->get_settings_page_slug(); // Don't touch | Gets the page slug for this setting

		$section = 'add_taxonomy'; // Edit This | Define your section here

		// Register settings

		$settings_adapter->register_settings( // Don't touch | Registers all of your settings from $this->settings
			$page_slug,
			$this->get_settings()
		);

		$settings_adapter->add_section( // Edit This | Add a custom section
			$section,
			'Add Custom Taxonomies',
			$page_slug,
			'' // Edit This | Descriptor text for the section
		);

		$taxonomies = $this->taxonomies;

		$post_types = $this->get_post_types_select();

		$current_taxonomies = get_option( 'core_taxnonomies' );

		foreach ( $taxonomies as $tax_slug => $tax_label ) {

			$current_taxonomies = ( ! empty( $current_taxonomies[ $tax_slug ] ) ) ? $current_taxonomies[ $tax_slug ] : array();

			$id = 'core_taxnonomies[' . $tax_slug . ']';

			$settings_adapter->add_multi_check_field( // Edit This | Add a select field
				'core_taxnonomies[' . $tax_slug . ']',
				'Add ' . $tax_label . ' To:',
				$page_slug, // Don't touch
				$section, // Don't touch
				$post_types,
				$current_taxonomies // Edit This | Current value of 'setting_key_id'
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

$ccore_taxonomies_module = new Taxonomies_Module(); // Edit This
