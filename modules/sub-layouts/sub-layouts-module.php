<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Modules;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use WSUWP\Plugin_Modules\Module as Module;

class Sub_Layouts_Module extends Module {

	/**
	 * Version of taxonomy search module used the module.
	 *
	 * @since 0.0.1
	 * @var string $version Version of module.
	 */
	protected $version = '0.0.1';

	/**
	 * Slug/ID for the module.
	 *
	 * @since 0.0.1
	 * @var string|bool $slug
	 */
	public $slug = 'cahnrswsuwp_sub_layouts';

	/**
	 * Registration args for the module
	 *
	 * @since 0.0.1
	 * @var array $register_args Array of registration args
	 */
	public $register_args = array(
		'owner'        => 'CAHNRS',
		'title'        => 'Sub Layouts',
		'description'  => 'Enable Sub Layout Options',
		'priority'     => 10,
		'capability'   => 'Administrator',
		'default_on'   => false,
	); // End $register_args


	/**
	 * Sub-layout options
	 *
	 * @since 0.0.1
	 * @var array $sub_layouts Array of sublayouts
	 */
	public $sub_layouts = array(
		'default'      => 'Default',
		'left-column'  => 'Sidebar Left',
		'right-column' => 'Sidebar Right',
		'none'         => 'None',
	);


	private $post_types = array( 'post', 'page' );


	/**
	 * Do the module. This should already have checked for active.
	 *
	 * @since 0.0.1
	 *
	 */
	protected function init_module() {

		if ( is_admin() ) {

			add_action( 'add_meta_boxes', array( $this, 'add_menu_meta_box' ), 10, 2 );

			add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

		} // End if

		// Add filter to include the sublayou. Theme must support the 'theme_content_html' filter.
		add_filter( 'theme_content_html', array( $this, 'add_sublayout' ) );

		add_action( 'layout_column_sidebar_before', array( $this, 'add_sidebar_menu' ) );

	} // End do_module


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
	 * Add menu metabox to edit page
	 *
	 * @since 0.0.1
	 *
	 * @param string $post_type Current post type
	 * @param WP_Post $post
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
	 *
	 * @since 0.0.1
	 *
	 * @param WP_Post $post
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


	/**
	 * Add the sublayout to the theme.
	 *
	 * @since 0.0.1
	 *
	 * @param string $html HTML of the post content.
	 *
	 * @return string Filtered HTML of the post content.
	 */
	public function add_sublayout( $html ) {

		$layout = $this->get_layout();

		if ( ! empty( $layout ) ) {

			$sidebar = 'sidebar';

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


	protected function get_layout() {

		$sublayout = false;

		if ( is_singular() ) {

			$post_id = get_the_ID();

			$post_type = get_post_type();

			if ( $post_id ) {

				$sublayout = $this->get_layout_by_id( $post_id );

			} // End if
		} // End if

		return $sublayout;

	} // End get_layout_option


	protected function get_layout_by_id( $post_id ) {

		$meta_key = '_core_sublayout';

		$sublayout = get_post_meta( $post_id, '_core_sublayout', true );

		if ( ( empty( $sublayout ) || 'default' === $sublayout ) ) {

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


	protected function get_menu() {

		$menu = false;

		if ( is_singular() ) {

			$post_id = get_the_ID();

			$post_type = get_post_type();

			if ( $post_id ) {

				$menu = $this->get_menu_by_id( $post_id );

			} // End if
		} // End if

		return $menu;

	} // End get_menu


	protected function get_menu_by_id( $post_id ) {

		$meta_key = '_core_sublayout_menu';

		$menu = get_post_meta( $post_id, $meta_key, true );

		if ( ( empty( $menu ) || 'default' === $menu ) ) {

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


	public function save_post( $post_id, $post ) {

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {

			return false;

		} // end if

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {

			return false;

		} // end if

		if ( in_array( $post->post_type, $this->post_types, true ) ) {

			if ( isset( $_REQUEST['core_sublayout_module'] ) ) {

				$nonce = $_REQUEST['core_sublayout_module'];

				if ( ! wp_verify_nonce( $nonce, 'core_sublayout_module_save_post' ) ) {

					die( 'Security check' );

				} else {

					$keys = array( '_core_sublayout', '_core_sublayout_menu' );

					foreach ( $keys as $key ) {

						if ( isset( $_REQUEST[ $key ] ) ) {

							$value = sanitize_text_field( $_REQUEST[ $key ] );

							update_post_meta( $post_id, $key, $value );

						} // End if
					} // End foreach
				} // End if
			} // End if
		} // End if

	} // End save_post

} // End Video_Module

$cahnrswsuwp_sub_layouts_module = new Sub_Layouts_Module();
