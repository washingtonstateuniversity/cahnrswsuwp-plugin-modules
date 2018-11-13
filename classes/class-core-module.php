<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Core;

class Core_Module {

	public $save_post = false;

	public $post_types = array();

	public $slug = false;

	protected $save = false;

	public $register_args = array(
		'icon'           => '',
		'label'          => 'Why you no add Label?',
		'helper_text'    => '',
	);

	public $module_settings = array(
		'init_priority' => 11,
	);

	public $settings = array();

	protected $post_settings = array();

	protected $save_args = array();

	protected $save_api = false;

	public function __construct() {

		add_action( 'init', array( $this, 'register_module' ) );

		if ( is_admin() ) {

			if ( ! empty( $this->save_args ) ) {

				$this->do_save_post_module();

			} // End if

			if ( ! empty( $this->register_args['settings_page'] ) ) {

				add_action( 'admin_init', array( $this, 'add_admin_settings' ) );

			} // End if

			if ( ! empty( $this->settings ) ) {

				$this->fill_settings();

			} // End if
		} // End if

		add_action( 'init', array( $this, 'init_module' ), $this->module_settings['init_priority'] );

	} // End construct


	protected function fill_settings() {

		foreach ( $this->settings as $key => $setting ) {

			if ( ! empty( $setting['sanitize_callback'] ) && is_array( $setting['sanitize_callback'] ) ) {

				$this->settings[ $key ]['sanitize_callback'] = array_merge( array( $this ), $setting['sanitize_callback'] );

			} // end if
		} // End foreach

	} // End fill_settings


	protected function do_save_post_module() {

		if ( is_admin() ) {

			if ( ! empty( $this->save_args ) ) {

				$default_save_args = array(
					'post_types'             => array(),
					'nonce_name'             => '',
					'nonce_action'           => '',
					'save_setting_callback'  => false,
					'add_actions'           => true,
				);

				if ( ! empty( $this->save_args['save_setting_callback'] ) && ! is_array( $this->save_args['save_setting_callback'] ) ) {

					$this->save_args['save_setting_callback'] = array( $this, $this->save_args['save_setting_callback'] );

				} // End if

				$save_args = array_merge( $default_save_args, $this->save_args );

				$save_settings = $this->post_settings;

				foreach ( $save_settings as $key => $setting ) {

					if ( ! empty( $setting['sanitize_callback'] ) && ! is_array( $setting['sanitize_callback'] ) ) {

						$save_settings[ $key ]['sanitize_callback'] = array( $this, $setting['sanitize_callback'] );

					} // End if
				} // End foreach

				require_once ccore_get_plugin_dir() . '/vendor/save-post/save-post.php';

				$this->save_api = new \Save_Post_Data(
					$save_settings,
					$save_args['post_types'],
					$save_args['nonce_name'],
					$save_args['nonce_action'],
					$save_args['save_setting_callback'],
					$save_args['add_actions']
				);

			} // End if
		} // End if

	} // End do_save_post_module


	public function add_admin_settings() {

		return false;

	} // End add_settings


	/**
	 * Register the module so it shows up on the Core settings page
	 */
	public function register_module() {

		$register_args = $this->register_args;

		if ( ! empty( $register_args['settings_page'] ) ) {

			$register_args['settings_page']['callback'] = array( $this, 'render_options_page' );

		} // End if

		ccore_register_module( $this->slug, $register_args );

	} // end register_module


	/**
	 * Check if module is active, if is active do module stuff.
	 */
	public function init_module() {

		if ( $this->slug && ccore_is_active_module( $this->slug ) ) {

			$this->init();

		} // End if

	} // end init_module


	/**
	 * This should be overwritten in child module class
	 */
	public function init() {

		return false;

	} // End init


	public function render_options_page() {

		if ( method_exists( $this, 'render_sub_options_page' ) ) {

			$this->render_sub_options_page();

		} else {

			echo '<marquee><h1 style="padding-top: 60px;">You need to add the render_sub_options_page to your module if you want this to work :)</marquee><p>And yes this is a marquee because you deserve it.</p>';

		} // End if

	} // End render_options_page


	public function get_settings_page_slug() {

		return $this->register_args['settings_page']['page_slug'];

	}


	public function render_sub_options_page() {

		$page_slug = $this->get_settings_page_slug();

		include dirname( __DIR__ ) . '/includes/modules/displays/module-settings.php';

	}


	public function get_nonce_name() {

		return 'core_' . $this->slug;

	}


	public function get_nonce_action() {

		return 'do_core_' . $this->slug;

	}


	public function get_settings() {

		return $this->settings;

	}


} // End Core_Module
