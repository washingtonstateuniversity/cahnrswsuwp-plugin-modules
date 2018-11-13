<?php

class Settings_API_Adapter {


	public function __construct() {

		include_once 'classes/class-settings-api-section.php';

		include_once 'classes/class-settings-api-text-field.php';

		include_once 'classes/class-settings-api-textarea-field.php';

		include_once 'classes/class-settings-api-select-field.php';

		include_once 'classes/class-settings-api-checkbox-field.php';

		include_once 'classes/class-settings-api-multi-check-field.php';

		include_once 'classes/class-settings-api-custom-field.php';

	}

	public function register_setting( $group, $id, $args ) {

		register_setting(
			$group,
			$id,
			$args
		);

	} // End add_section

	public function register_settings( $group, $settings ) {

		foreach ( $settings as $key => $setting_args ) {

			$this->register_setting( $group, $key, $setting_args );

		} // End foreach

	} // End register_settings


	public function add_section( $id, $title, $page, $html_content = '', $callback = false, $args = array() ) {

		$section = new Settings_API_Section(
			$id,
			$title,
			$page,
			$html_content,
			$callback,
			$args
		);

	} // End add_section


	public function add_text_field( $id, $label, $page, $section, $value, $args = array(), $callback = false ) {

		$field = new Settings_API_Text_Field(
			$id,
			$label,
			$page,
			$section,
			$value,
			$args,
			$callback
		);

	}


	public function add_textarea_field( $id, $label, $page, $section, $value, $args = array(), $callback = false ) {

		$field = new Settings_API_Textarea_Field(
			$id,
			$label,
			$page,
			$section,
			$value,
			$args,
			$callback
		);

	}


	public function add_select_field( $id, $label, $page, $section, $options, $current_value, $args = array(), $callback = false ) {

		$field = new Settings_API_Select_Field(
			$id,
			$label,
			$page,
			$section,
			$options,
			$current_value,
			$args,
			$callback
		);

	}

	public function add_multi_check_field( $id, $label, $page, $section, $options, $current_value, $args = array(), $callback = false ) {

		$field = new Settings_API_Multi_Check_Field(
			$id,
			$label,
			$page,
			$section,
			$options,
			$current_value,
			$args,
			$callback
		);

	}


	public function add_checkbox_field( $id, $label, $page, $section, $current_value, $value = 1, $args = array(), $callback = false ) {

		$field = new Settings_API_Checkbox_Field(
			$id,
			$label,
			$page,
			$section,
			$current_value,
			$value,
			$args,
			$callback
		);

	}


	public function add_custom_field( $id, $label, $page, $section, $args = array(), $callback = false ) {

		$field = new Settings_API_Custom_Field(
			$id,
			$label,
			$page,
			$section,
			$args,
			$callback
		);

	}

}
