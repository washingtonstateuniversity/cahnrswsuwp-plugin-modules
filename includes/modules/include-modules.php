<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Core;

if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly

} // End if


class Modules {


	public function __construct() {

		$this->add_modules();

		add_action( 'admin_menu', array( $this, 'add_modules_options' ) );

	} // End __construct


	protected function add_modules() {

		$plugin_base = ccore_get_plugin_dir();

		include_once $plugin_base . '/classes/class-core-module.php';

		include_once $plugin_base . '/modules/sub-layouts/sub-layouts-module.php';

		include_once $plugin_base . '/modules/expire-content/expire-content-module.php';

		include_once $plugin_base . '/modules/extension-publications/extension-publications-module.php';

		include_once $plugin_base . '/modules/print-view/print-view-module.php';

		include_once $plugin_base . '/modules/web-rotate-360/web-rotate-360-module.php';

		include_once $plugin_base . '/modules/publications/publications-module.php';

		include_once $plugin_base . '/modules/grants/grants-module.php';

		include_once $plugin_base . '/modules/taxonomies/taxonomies-module.php';

		include_once $plugin_base . '/modules/advanced-archive/advanced-archive-module.php';

		include_once $plugin_base . '/modules/breadcrumbs/breadcrumb-module.php';

		include_once $plugin_base . '/modules/post-feed/post-feed-module.php';

		include_once $plugin_base . '/modules/mailchimp/mailchimp-module.php';

		//include_once $plugin_base . '/modules/links/links-module.php';

	} // End add_modules


	public function add_modules_options() {

		$options_slug = 'core-options';

		add_menu_page(
			'CANRS Core Modules',
			'Core Modules',
			'manage_options',
			$options_slug,
			array( $this, 'render_options_page' )
		);

		$modules = ccore_get_registered_modules();

		foreach ( $modules as $slug => $module ) {

			if ( ! empty( $module['settings_page'] ) ) {

				$sp = $module['settings_page'];

				add_submenu_page(
					'core-options',
					$sp['page_title'],
					$sp['menu_title'],
					$sp['capabilities'],
					$sp['page_slug'],
					$sp['callback']
				);

			} // End if
		} // End foreach

	} // End add_modules_options


	public function render_options_page() {

		$modules = ccore_get_registered_modules();

		include __DIR__ . '/displays/module-options-form.php';

	} // End render_options_page


} // End Modules

$ccore_modules = new Modules();
