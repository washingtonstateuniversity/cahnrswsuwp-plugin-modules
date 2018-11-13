<?php
/**
 * @var array  $sublayuts Sublayouts to use in the select field value => label.
 * @var string $sublayout Current selected sublayout
 * @var array  $menu_options Menus to display in the select field value => label.
 * @var string $menu Current selected menu.
 */
?><div class="core-form">
	<fieldset>
		<div class="core-form-content">
			<div class="core-field core-select-field core-field-full-width core-field-tight">
				<label>Layout</label>
				<select name="_core_sublayout">
					<?php foreach ( $sublayouts as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $sublayout ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="core-field core-select-field core-field-full-width core-field-tight">
				<label>Sidebar Menu</label>
				<select name="_core_sublayout_menu">
					<?php foreach ( $menu_options as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $menu ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		<div class="core-form-content">
	</fieldset>
</div>
