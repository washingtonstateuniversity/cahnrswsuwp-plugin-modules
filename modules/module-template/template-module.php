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
class Template_Module extends Core_Module {

	public $slug = 'module_id'; // The ID for the module _ only

	public $register_args = array(
		'label'          => 'Module Label Here', // Edit This | Shows on activate module panel
		'helper_text'    => 'Short 3 word helper text.', // Edit This | Shows on activate module panel
		'settings_page'  => array(
			'page_title'     => 'Settings Page Title Here',
			'menu_title'     => 'Menu Button Title',
			'capabilities'   => 'manage_options', // Don't touch | Role that can see this
			'page_slug'      => 'page_slug_here', // Edit This | similar to ID
			'callback'       => 'render_options_page', // Don't touch
		),
	);

	/**
	 * These are the settings for the settings page above
	 */
	public $settings = array(
		'setting_key_id' => array( // Edit This
			'type'              => 'string', // Edit This | Used to sanitize the setting
			'description'       => 'Desc of the setting',
			'show_in_rest'      => false,
			'default'           => '', // Edit This | Default value of the setting
		),
	);


	/**
	 * Init the module here
	 */
	public function init() {

		// Do module stuff here

	} // End init


	public function add_admin_settings() {

		$settings_adapter = get_settings_api_adapter(); // Don't touch | Custom settings wrapper to make using it easier

		$page_slug = $this->get_settings_page_slug(); // Don't touch | Gets the page slug for this setting

		$section = 'page_section'; // Edit This | Define your section here

		// Register settings

		$settings_adapter->register_settings( // Don't touch | Registers all of your settings from $this->settings
			$page_slug,
			$this->get_settings()
		);

		$settings_adapter->add_section( // Edit This | Add a custom section
			$section,
			'My Section Name Here',
			$page_slug,
			'Some random text here' // Edit This | Descriptor text for the section
		);

		$settings_adapter->add_select_field( // Edit This | Add a select field
			'setting_key_id',
			'Setting Label Here',
			$page_slug, // Don't touch
			$section, // Don't touch
			array( // Edit This | Select options as an array
				'value' => 'Value Label',
			),
			get_option( 'setting_key_id' ) // Edit This | Current value of 'setting_key_id'
		);

	} // End add_settings

} // End Sub_Layouts

$ccore_template_module = new Template_Module(); // Edit This
