<?php

class Settings_API_Section {

	public $id;

	public $title;

	public $page;

	public $html_content;

	public $callback;

	public $args;


	public function __construct( $id, $title, $page, $html_content = '', $callback = false, $args = array() ) {

		$this->id           = $id;
		$this->title        = $title;
		$this->page         = $page;
		$this->html_content = $html_content;
		$this->callback     = $callback;
		$this->args         = $args;

		if ( ! $this->callback ) {

			$this->callback = array( $this, 'section_callback' );

		} // End if

		add_settings_section(
			$this->id,
			$this->title,
			$this->callback,
			$this->page
		);

	}


	public function section_callback( $args ) {

		echo wp_kses_post( $this->html_content );

	} // End section_callback

}
