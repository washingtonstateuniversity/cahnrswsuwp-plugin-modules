<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Modules;

if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly

} // End if


/**
 * Links Module
 * @version 0.0.1
 * @author CAHNRS Communications, Danial Bleile
 *
 * Adds Links post type to site
 * Adds Investigators taxonomy to site
 * Adds Status taxonomy to site
 * Adds Link Categories taxonomy to site
 * Adds Link metabox to Links edit page
 *
 * @uses vendor/Settings_API_Adapter
 * @uses vendor/Save_Post_Data
 *
 */


class Links_Module extends Core_Module {

	// @var string $version Current version of the module.
	public $version = '0.0.1';

	// @var string $slug Slug for the module (no spaces, dashes, or numbers).
	public $slug = 'core_links'; // The ID for the module _ only.

	// @var array $register_args Args to use to register the module.
	public $register_args = array(
		'label'          => 'Links', // (required) string Label of the Module on the activation page.
		'helper_text'    => 'Links content type.', // (required) string Description of the Module on the activation page.
	);

	// @var array $save_args Passed to the Save_Post_API
	protected $save_args = array(
		'post_types'             => array( 'links' ), // Post types to do save on
		'nonce_name'             => 'core_links_module', // Nonce name used on the metabox or edit form 
		'nonce_action'           => 'core_links_module_save_post', // Nonce action used on the metabox or edit form 
		'save_setting_callback'  => 'save_link_setting', // Custom callback for editing data before save
		'add_actions'            => true, // Add save actions. Set to false if you want to add actions manually
	);

	// @var $post_settings Settings for the Save_Post_API to use. 
	protected $post_settings = array(
		'_link' => array( // Settings key
			'sanitize_type'      => 'custom', // Type of data - used to sanitize the data
			'default'            => '', // Default value
			'check_isset'        => true, // Do a check if isset, otherwise will use default value 
			'ignore_empty'       => true, // Ignore if data is an empty string
			'sanitize_callback'  => 'sanitize_links_post_meta', // Custom sanitization callback 
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

			// Action for adding the metabox to post type.
			add_action( 'add_meta_boxes', array( $this, 'add_link_meta_box' ) );

		} // End if

	} // End init


	/**
	 * Add action for the metabox for the Links page.
	 * @since 0.0.1
	 */
	public function add_link_meta_box() {

		// Wp action for adding metabox.
		add_meta_box(
			'core_link_info',
			'Link Information',
			array( $this, 'the_link_metabox' )
		);

	} // End add_link_meta_box




	/**
	 * Add the metabox for the Links page.
	 * @since 0.0.1
	 *
	 * @var WP_Post $post WP post object.
	 */
	public function the_link_metabox( $post ) {

		// Check if links post type
		/*if ( 'links' === $post->post_type ) {

			// Get the links meta data - stored as an array under a single key.
			$links_meta = get_post_meta( $post->ID, '_link', true );

			// Add nonce field to metabox.
			wp_nonce_field( 'core_links_module_save_post', 'core_links_module' );

			$publications_content = ''; // string HTML for publications.
			$funding_content      = ''; // string HTML for funding.
			$impact_content       = ''; // string HTML for impact.
			$admin_content        = ''; // string HTML for admin.
			$project_id           = ''; // string Project ID
			$investigators        = $this->get_investigators_terms(); // array Term_ID => Term Name Investigators taxonomy terms.
			$annual_entries       = $this->get_annual_entries( $post ); // array Annual entries array.
			$additional_funds     = $this->get_additional_funds( $post ); // array Additional funds array.

			// Get the content to display.
			include __DIR__ . '/displays/links-meta-box.php';

		} // End if*/

	} // End if


	/**
	 * Register the post type with WP.
	 * @since 0.0.1
	 */
	protected function register_post_type() {

		$labels = array(
			'name'               => _x( 'Links', 'post type general name', 'cahnrswsuwp-plugin-core' ),
			'singular_name'      => _x( 'Link', 'post type singular name', 'cahnrswsuwp-plugin-core' ),
			'menu_name'          => _x( 'Links', 'admin menu', 'cahnrswsuwp-plugin-core' ),
			'name_admin_bar'     => _x( 'Link', 'add new on admin bar', 'cahnrswsuwp-plugin-core' ),
			'add_new'            => _x( 'Add New', 'link', 'cahnrswsuwp-plugin-core' ),
			'add_new_item'       => __( 'Add New Link', 'cahnrswsuwp-plugin-core' ),
			'new_item'           => __( 'New Link', 'cahnrswsuwp-plugin-core' ),
			'edit_item'          => __( 'Edit Link', 'cahnrswsuwp-plugin-core' ),
			'view_item'          => __( 'View Link', 'cahnrswsuwp-plugin-core' ),
			'all_items'          => __( 'All Links', 'cahnrswsuwp-plugin-core' ),
			'search_items'       => __( 'Search Links', 'cahnrswsuwp-plugin-core' ),
			'parent_item_colon'  => __( 'Parent Links:', 'cahnrswsuwp-plugin-core' ),
			'not_found'          => __( 'No links found.', 'cahnrswsuwp-plugin-core' ),
			'not_found_in_trash' => __( 'No links found in Trash.', 'cahnrswsuwp-plugin-core' ),
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

		register_post_type( 'link', $args );

	} // End register_post_type


	/**
	 * Register the taxonomies with WP.
	 * @since 0.0.1
	 */
	protected function register_taxonomies() {

		// Link Category Taxonomy

		$categories_labels = array(
			'name'              => _x( 'Link Categories', 'taxonomy general name', 'cahnrswsuwp-plugin-core' ),
			'singular_name'     => _x( 'Link Category', 'taxonomy singular name', 'cahnrswsuwp-plugin-core' ),
			'search_items'      => __( 'Search Link Categories', 'cahnrswsuwp-plugin-core' ),
			'all_items'         => __( 'All Link Categories', 'cahnrswsuwp-plugin-core' ),
			'parent_item'       => __( 'Parent Link Category', 'cahnrswsuwp-plugin-core' ),
			'parent_item_colon' => __( 'Parent Link Category:', 'cahnrswsuwp-plugin-core' ),
			'edit_item'         => __( 'Edit Link Category', 'cahnrswsuwp-plugin-core' ),
			'update_item'       => __( 'Update Link Category', 'cahnrswsuwp-plugin-core' ),
			'add_new_item'      => __( 'Add New Link Category', 'cahnrswsuwp-plugin-core' ),
			'new_item_name'     => __( 'New Link Category Name', 'cahnrswsuwp-plugin-core' ),
			'menu_name'         => __( 'Link Category', 'cahnrswsuwp-plugin-core' ),
		);

		$categories_args = array(
			'hierarchical'      => true,
			'labels'            => $categories_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
		);

		// Register taxonomy and add to the links post type
		register_taxonomy( 'link_categories', array( 'link' ), $categories_args );

	} // End register_taxonomies

} // End Sub_Layouts


// Initialize the class.
$ccore_links_module = new Links_Module();
