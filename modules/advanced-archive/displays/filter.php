<div class="core-advanced-archive-filter">
	<label><?php echo esc_html( $label ); ?></label>
	<select name="<?php echo esc_attr( $taxonomy ); ?>" onchange="this.form.submit()">
		<option value="">Select...</option>
	<?php foreach ( $term_filters as $value => $term_label ) : ?>
		<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $selected_value, true ); ?>><?php echo esc_html( $term_label ); ?></option>
	<?php endforeach; ?>
	</select>
</div>
