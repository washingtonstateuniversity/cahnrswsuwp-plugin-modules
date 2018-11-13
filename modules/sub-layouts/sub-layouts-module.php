<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Modules;

if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly

} // End if


/**
 * Sublayout Module
 * @version 0.0.1
 * @author CAHNRS Communications, Danial Bleile
 *
 * Adds sublayout support to theme content.
 * Sublayouts can be chosen by post type or can be set on
 * an individual page/post. Sublayouts can also be inherited from
 * parent content allowing a whole section of the site to have a defined layout.
 *
 * @uses vendor/Settings_API_Adapter
 *
 */
class Sub_Layouts_Module extends Core_Module {

	// @var string $version Current version of the module.
	public $version = '0.0.1';

	// @var string $slug Slug for the module (no spaces, dashes, or numbers).
	public $slug = 'sub_layouts';

	// @var array $register_args Args to use to register the module.
	public $register_args = array(
		'label'          => 'Content Layout',
		'helper_text'    => 'Where supported by Theme.',
		'settings_page'  => array(
			'page_title'     => 'Core Sub Layout Settings',
			'menu_title'     => 'Sub Layouts',
			'capabilities'   => 'manage_options',
			'page_slug'      => 'core_sublayouts',
			'callback'       => 'render_options_page',
		),
	);

	// @var array Settings for the 'settings_page' to register and save.
	public $settings = array(
		'core_sublayout_format' => array(
			'type'              => 'string',
			'description'       => 'Base layout to use on theme',
			'show_in_rest'      => false,
			'default'           => '',
		),
		'core_sublayout_menu' => array(
			'type'              => 'string',
			'description'       => 'Base layout to use on theme',
			'show_in_rest'      => false,
			'default'           => '',
		),
		'core_sublayout_inherit' => array(
			'type'              => 'string',
			'description'       => 'Allow Inheritance',
			'show_in_rest'      => false,
			'default'           => '',
		),
	);

	// @var array $save_args Passed to the Save_Post_API
	protected $save_args = array(
		'post_types'             => array( 'post', 'page' ), // Post types to do save on
		'nonce_name'             => 'core_sublayout_module', // Nonce name used on the metabox or edit form
		'nonce_action'           => 'core_sublayout_module_save_post', // Nonce action used on the metabox or edit form
	);

	// @var $post_settings Settings for the Save_Post_API to use.
	protected $post_settings = array(
		'_core_sublayout' => array( // Settings key
			'sanitize_type'      => 'text', // Type of data - used to sanitize the data
			'default'            => '', // Default value
			'ignore_empty'       => true, // Ignore if data is an empty string
		),
		'_core_sublayout_menu' => array( // Settings key
			'sanitize_type'      => 'text', // Type of data - used to sanitize the data
			'default'            => '', // Default value
			'ignore_empty'       => true, // Ignore if data is an empty string
		),
	);

	// @var array Sublayouts available in the theme.
	public $sub_layouts = array(
		'default'      => 'Default',
		'left-column'  => 'Sidebar Left',
		'right-column' => 'Sidebar Right',
	);


	/**
	 * Init the module. This is called after from the 'init' action in the parent class.
	 * @since 0.0.1
	 */
	public function init() {

		// Add filter to include the sublayou. Theme must support the 'theme_content_html' filter.
		add_filter( 'theme_content_html', array( $this, 'add_sublayout' ) );

		if ( is_admin() ) {

			add_action( 'add_meta_boxes', array( $this, 'add_menu_meta_box' ), 10, 2 );

		} // End if

		add_action( 'layout_column_sidebar_before', array( $this, 'add_sidebar_menu' ) );

	} // End init


	/**
	 * Add menu metabox to edit page
	 * @since 0.0.1
	 *
	 */
	public function add_menu_meta_box( $post_type, $post ) {

		$post_types = array( 'post', 'page' );

		add_meta_box(
			'cor_sublayout_options',
			'Layout Options',
			array( $this, 'render_menu_metabox' ),
			$post_types,
			'side',
			'default'
		);

	} // End add_menu_meta_box


	/**
	 * Renders custom layout options metabox
	 * @since 2.1.1
	 *
	 * @param
	 */
	public function render_menu_metabox( $post ) {

		$sublayouts = $this->sub_layouts;

		$post_id = $post->ID;

		$sublayout = get_post_meta( $post_id, '_core_sublayout', true );

		$menu = get_post_meta( $post_id, '_core_sublayout_menu', true );

		$menu_options = $this->get_menu_terms();

		// Add nonce field to metabox.
		wp_nonce_field( 'core_sublayout_module_save_post', 'core_sublayout_module' );

		include __DIR__ . '/displays/metabox.php';

	} // End render_menu_metabox


	public function add_sidebar_menu() {

		$menu_id = $this->get_menu();

		if ( ! empty( $menu_id ) && ! in_array( $menu_id, array( 'default', 'none' ), true ) ) {

			$menu = wp_get_nav_menu_object( $menu_id );

			$name = $menu->name;

			$class = $menu->slug . '-core-column-menu';

			$menu_args = array(
				'menu' => $menu_id,
			);

			include __DIR__ . '/displays/column-menu.php';

		} // End if

	}


	/**
	 * Add the sublayout to the theme.
	 * @since 0.0.1
	 *
	 * @param string $html HTML of the post content.
	 *
	 * @return string Filtered HTML of the post content.
	 */
	public function add_sublayout( $html ) {

		$layout = $this->get_layout();

		if ( isset( $_GET['test'] ) ) {

			var_dump( is_front_page() );
			
			var_dump( $layout );

			die();
		
		};

		if ( ! empty( $layout ) ) {

			$sidebar = $this->get_sidebar();

			switch ( $layout ) {

				case 'left-column':
					ob_start();
					include __DIR__ . '/displays/left-column.php';
					$html = ob_get_clean();
					break;

				case 'right-column':
					ob_start();
					include __DIR__ . '/displays/right-column.php';
					$html = ob_get_clean();
					break;

			} // End switch
		} // End if

		return $html;

	} // End add_sublayout


	protected function get_sidebar() {

		$sidebar = '';

		if ( is_singular() || is_post_type_archive() ) {

			$post_type = get_post_type();

			$sidebar = $this->get_sidebar_by_post_type( $post_type );

		} // End if

		return $sidebar;

	} // End get_sidebar

	protected function get_sidebar_by_post_type( $post_type ) {

		$sidebar = '';

		if ( ! empty( $post_type ) ) {

			$sidebar = get_option( 'core_sublayout_sidebar_' . $post_type, '' );

		} // End if

		return $sidebar;

	} // End get_sidebar_by_post_type


	protected function get_menu_by_id( $post_id ) {

		$meta_key = '_core_sublayout_menu';

		$menu = get_post_meta( $post_id, $meta_key, true );

		$allow_inherit = get_option( 'core_sublayout_inherit' );

		if ( ( empty( $menu ) || 'default' === $menu ) && ! empty( $allow_inherit ) ) {

			$ancestor_ids = get_post_ancestors( $post_id );

			foreach ( $ancestor_ids as $a_post_id ) {

				$a_menu = get_post_meta( $a_post_id, $meta_key, true );

				if ( ! empty( $a_menu ) || 'default' === $a_menu ) {

					$menu = $a_menu;

					break;

				} // End if
			} // End foreach
		} // End if

		return $menu;

	} // End get_layout_by_id


	protected function get_menu_by_post_type( $post_type ) {

		$menu = get_option( 'core_sublayout_menu_' . $post_type, '' );

		return $menu;

	} // End if


	protected function get_menu() {

		$menu = false;

		if ( is_singular() ) {

			$post_id = get_the_ID();

			$post_type = get_post_type();

			if ( $post_id ) {

				$menu = $this->get_menu_by_id( $post_id );

				if ( empty( $menu ) || 'default' === $menu ) {

					$menu = $this->get_menu_by_post_type( $post_type );

				} // End if
			} else {

				$menu = $this->get_menu_by_post_type( $post_type );

			} // End if
		} elseif ( is_post_type_archive() ) {

			$post_type = get_post_type();

			$menu = $this->get_menu_by_post_type( $post_type );

		} // End if

		if ( empty( $menu ) || 'default' === $menu ) {

			$menu = get_option( 'core_sublayout_menu', '' );

		} // End if

		return $menu;

	} // End get_menu


	protected function get_layout_by_id( $post_id ) {

		$meta_key = '_core_sublayout';

		$sublayout = get_post_meta( $post_id, '_core_sublayout', true );

		$allow_inherit = get_option( 'core_sublayout_inherit' );

		if ( ( empty( $sublayout ) || 'default' === $sublayout ) && ! empty( $allow_inherit ) ) {

			$ancestor_ids = get_post_ancestors( $post_id );

			foreach ( $ancestor_ids as $a_post_id ) {

				$a_sublayout = get_post_meta( $a_post_id, '_core_sublayout', true );

				if ( ! empty( $a_sublayout ) || 'default' === $a_sublayout ) {

					$sublayout = $a_sublayout;

					break;

				} // End if
			} // End foreach
		} // End if

		return $sublayout;

	} // End get_layout_by_id


	protected function get_layout_by_post_type( $post_type ) {

		$sublayout = false;

		$key = 'core_sublayout_format_' . $post_type;

		$sublayout = get_option( $key, '' );

		return $sublayout;

	} // End if


	protected function get_layout() {

		$sublayout = false;

		if ( is_front_page() ) {

			$sublayout = $this->get_layout_by_post_type( 'front_page' );

		} elseif ( is_singular() ) {

			$post_id = get_the_ID();

			$post_type = get_post_type();

			if ( $post_id ) {

				$sublayout = $this->get_layout_by_id( $post_id );

				if ( empty( $sublayout ) || 'default' === $sublayout ) {

					$sublayout = $this->get_layout_by_post_type( $post_type );

				} // End if
			} else {

				$sublayout = $this->get_layout_by_post_type( $post_type );

			} // End if
		} elseif ( is_post_type_archive() ) {

			$post_type = get_post_type();

			$sublayout = $this->get_layout_by_post_type( $post_type );

		} // End if

		if ( empty( $sublayout ) || 'default' === $sublayout ) {

			$sublayout = get_option( 'core_sublayout_format', '' );

		} // End if

		return $sublayout;

	} // End get_layout_option


	protected function get_menu_terms() {

		$menu_options = array(
			'default' => 'Default',
			'none'    => 'None',
		);

		$menu_terms = get_terms( 'nav_menu', array( 'hide_empty' => true ) );

		if ( ! empty( $menu_terms ) && is_array( $menu_terms ) ) {

			foreach ( $menu_terms as $menu_term ) {

				$menu_options[ $menu_term->term_id ] = $menu_term->name;

			} // End foreach
		} // End if

		return $menu_options;

	} // End get_menu_terms


	public function add_admin_settings() {

		$settings_adapter = get_settings_api_adapter();

		$page_slug = $this->get_settings_page_slug();

		$section = 'core_sublayouts';

		$menu_options = $this->get_menu_terms();

		$post_types = ccore_get_post_types_select();

		$sidebars = ccore_get_registered_sidebars();

		$post_types['front_page'] = 'Front Page';

		$settings = $this->settings;

		foreach ( $post_types as $slug => $label ) {

			$settings[ 'core_sublayout_format_' . $slug ] = array(
				'type'              => 'string',
				'description'       => 'Base layout to use on theme',
				'show_in_rest'      => false,
				'default'           => '',
			);

			$settings[ 'core_sublayout_menu_' . $slug ] = array(
				'type'              => 'string',
				'description'       => 'Base layout to use on theme',
				'show_in_rest'      => false,
				'default'           => '',
			);

			$settings[ 'core_sublayout_sidebar_' . $slug ] = array(
				'type'              => 'string',
				'description'       => 'Base layout to use on theme',
				'show_in_rest'      => false,
				'default'           => '',
			);

		} // End foreach


		// Register settings

		$settings_adapter->register_settings(
			$page_slug,
			$settings
		);

		$settings_adapter->add_section(
			$section,
			'Core Sub Layout Options',
			$page_slug,
			''
		);

		$settings_adapter->add_select_field(
			'core_sublayout_format',
			'Base Layout Format',
			$page_slug,
			$section,
			$this->sub_layouts,
			get_option( 'core_sublayout_format' )
		);

		$settings_adapter->add_select_field(
			'core_sublayout_menu',
			'Base Layout Menu',
			$page_slug,
			$section,
			$menu_options,
			get_option( 'core_sublayout_menu' )
		);

		foreach ( $post_types as $slug => $label ) {

			$settings_adapter->add_select_field(
				'core_sublayout_format_' . $slug,
				$label . ' Layout Format',
				$page_slug,
				$section,
				$this->sub_layouts,
				get_option( 'core_sublayout_format_' . $slug, '' )
			);

			$settings_adapter->add_select_field(
				'core_sublayout_menu_' . $slug,
				$label . ' Layout Menu',
				$page_slug,
				$section,
				$menu_options,
				get_option( 'core_sublayout_menu_' . $slug, '' )
			);

			$settings_adapter->add_select_field(
				'core_sublayout_sidebar_' . $slug,
				$label . ' Layout Sidebar',
				$page_slug,
				$section,
				$sidebars,
				get_option( 'core_sublayout_sidebar_' . $slug, '' )
			);

		}

		$settings_adapter->add_checkbox_field(
			'core_sublayout_inherit',
			'Inherit Parent Layout and Menu',
			$page_slug,
			$section,
			get_option( 'core_sublayout_inherit' )
		);

	} // End add_settings

} // End Sub_Layouts

$ccore_sub_layouts_module = new Sub_Layouts_Module();
