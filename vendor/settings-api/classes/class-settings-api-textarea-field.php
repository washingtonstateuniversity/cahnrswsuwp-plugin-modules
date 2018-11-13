<?php

class Settings_API_Textarea_Field {

	public $id;

	public $label;

	public $page;

	public $section;

	public $value;

	public $callback;

	public $args;


	public function __construct( $id, $label, $page, $section, $value, $args = array(), $callback = false ) {

		$this->id           = $id;
		$this->label        = $label;
		$this->page         = $page;
		$this->section      = $section;
		$this->value        = $value;
		$this->callback     = $callback;
		$this->args         = $args;

		if ( ! $this->callback ) {

			$this->callback = array( $this, 'field_callback' );

		} // End if

		add_settings_field(
			$this->id,                      // ID used to identify the field throughout the theme
			$this->label,                           // The label to the left of the option interface element
			$this->callback,   // The name of the function responsible for rendering the option interface
			$this->page,                          // The page on which this option will be displayed
			$this->section,         // The name of the section to which this field belongs
			$this->args
		);

	}


	public function field_callback( $args ) {

		$value = $this->value;

		$id = $this->id;

		include dirname( __DIR__ ) . '/displays/fields/textarea-field.php';

	} // End section_callback

}
