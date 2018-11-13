<select id="<?php echo esc_attr( $id ); ?>-text-field" name="<?php echo esc_attr( $id ); ?>" >
	<?php foreach ( $options as $value => $label ) : ?>
	<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $current_value, $value ); ?> ><?php echo esc_html( $label ); ?></option>
	<?php endforeach; ?>
</select>
