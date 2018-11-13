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
class Expire_Content_Module extends Core_Module {

	public $save_post = true;

	public $post_types = array();

	public $slug = 'expire_content';

	public $register_args = array(
		'label'          => 'Expire Content',
		'helper_text'    => 'Auto remove old content',
		'settings_page'  => array(
			'page_title'     => 'Expire Content Settings',
			'menu_title'     => 'Expire Content',
			'capabilities'   => 'manage_options',
			'page_slug'      => 'core_expire_content',
			'callback'       => 'render_options_page',
		),
	);

	public $expire_options = array(
		0      => 'Default',
		-1     => 'Never',
		180    => '6 Months',
		365    => '1 Year',
		545    => '1.5 Year',
		730    => '2 years',
		910    => '2.5 years',
		1095   => '3 years',
		1460   => '4 years',
		1825   => '5 Years',
		2190   => '6 years',
	);

	public $post_settings = array(
		'_expire_in_month' => array(),
		'_expire_in_day' => array(),
		'_expire_in_year' => array(),
	);


	public function __construct() {

		register_activation_hook( ccore_get_plugin_dir( '/plugin.php' ), array( $this, 'add_check_expires_event' ) );

		register_deactivation_hook( ccore_get_plugin_dir( '/plugin.php' ), array( $this, 'remove_check_expires_event' ) );

		add_action( 'check_expires_post', array( $this, 'remove_expired_posts' ) );

		add_action( 'check_expires_post', array( $this, 'remove_custom_expired_posts' ) );

		$this->post_types = array_keys( $this->get_post_types_select() );

		parent::__construct();

	} // End __construct


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


	public function add_check_expires_event() {

		if ( ! wp_next_scheduled( 'check_expires_post' ) ) {

			wp_schedule_event( time(), 'twicedaily', 'check_expires_post' );

		} // End if

	} // End add_check_expires_event


	public function remove_check_expires_event() {

		wp_clear_scheduled_hook( 'check_expires_post' );

	} // End remove_check_expires_event


	public function remove_expired_posts() {

		$settings_adapter = get_settings_api_adapter();

		$post_types = $this->get_post_types_select();

		foreach ( $post_types as $slug => $label ) {

			$expire_date = get_option( 'core_expire_' . $slug );

			if ( ! empty( $expire_date ) ) {

				// TODO Better way to do this?
				$expired = strtotime( '-' . $expire_date . ' days' );

				$args = array(
					'posts_per_page' => 15,
					'post_type'      => $slug,
					'post_status'    => 'publish',
					'order'          => 'DESC',
					'date_query'     => array(
						'before' => date( 'Y-m-d', $expired ),
					),
					'meta_key'       => '_expire_in',
					'meta_compare'   => 'NOT EXISTS',
				);

				$posts = get_posts( $args );

				if ( ! empty( $posts ) ) {

					foreach ( $posts as $post ) {

						$post_array = array(
							'ID'          => $post->ID,
							'post_status' => 'draft',
						);

						wp_update_post( $post_array );

					} // End foreach
				} // End if
			} // End if
		} // End foreach

	} // End remove_expired_posts


	public function remove_custom_expired_posts() {

		$args = array(
			'posts_per_page' => -1,
			'post_type'      => 'any',
			'post_status'    => 'publish',
			'order'          => 'DESC',
			'meta_query'     => array(
				array(
					'key'     => '_expire_in_year',
					'value'   => 0,
					'type'    => 'numeric',
					'compare' => '>',
				),
			),
		);

		$posts = get_posts( $args );

		if ( ! empty( $posts ) ) {

			foreach ( $posts as $post ) {

				$expire_year = get_post_meta( $post->ID, '_expire_in_year', true );

				$expire_month = get_post_meta( $post->ID, '_expire_in_month', true );

				$expire_day = get_post_meta( $post->ID, '_expire_in_day', true );

				$expire_date = $expire_year . '-' . $expire_month . '-' . $expire_day;

				$date = date( 'Y-m-d' );

				if ( $date > $expire_date ) {

					$post_array = array(
						'ID'          => $post->ID,
						'post_status' => 'draft',
					);

					wp_update_post( $post_array );

				} // End if
			} // End foreach
		} // End if

	} // End remove_expired_posts


	/**
	 * Init the module here
	 */
	public function init() {

		add_action( 'post_submitbox_misc_actions', array( $this, 'add_expires_settings_to_post' ) );

		$settings = array(
			'_expire_in' => array(),
		);

		if ( is_admin() ) {

			$save_post = new \Save_Post_Data( $settings, 'page', 'expire_post', 'do_expire_post' );

		} // End if

		if ( isset( $_GET['do-expire'] ) ) {

			$this->remove_custom_expired_posts();

		} // End if

	} // End init


	public function add_expires_settings_to_post( $post ) {

		$expire_options = $this->expire_options;

		$current_expire_month = get_post_meta( $post->ID, '_expire_in_month', true );
		$current_expire_day = get_post_meta( $post->ID, '_expire_in_day', true );
		$current_expire_year = get_post_meta( $post->ID, '_expire_in_year', true );

		include __DIR__ . '/displays/post-select-field.php';

		wp_nonce_field( $this->get_nonce_action(), $this->get_nonce_name() );

	} // End add_expires_settings_to_post


	public function add_admin_settings() {

		$settings_adapter = get_settings_api_adapter();

		$page_slug = $this->get_settings_page_slug();

		$section = $this->slug . '_section';

		$post_types = $this->get_post_types_select();

		foreach ( $post_types as $slug => $label ) {

			$this->settings[ 'core_expire_' . $slug ] = array(
				'type'              => 'string',
				'description'       => 'Set expire time for ' . $label,
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
			'Set Expires for post types',
			$page_slug,
			''
		);

		foreach ( $post_types as $slug => $label ) {

			$settings_adapter->add_select_field(
				'core_expire_' . $slug,
				'Expires on ' . $label,
				$page_slug,
				$section,
				$this->expire_options,
				get_option( 'core_expire_' . $slug )
			);

		} // End foreach

	} // End add_settings


} // End Sub_Layouts

$ccore_expire_content_module = new Expire_Content_Module();
