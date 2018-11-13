<?php foreach ( $options as $value => $label ) : ?>
	<p>
		<label>
<input type="checkbox" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $id ); ?>[]" <?php if ( in_array( $value, $current_values, true ) ) : ?><?php echo 'checked="checked"'; ?><?php endif; ?> /> <?php echo esc_html( $label ); ?>
		</label>
	</p>
<?php endforeach; ?>
