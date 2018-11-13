<fieldset class="core-post-feed-filters">
	<?php foreach ( $filters_array as $filter ) : ?>
	<div class="core-post-feed-filter <?php echo esc_attr( $filter['class'] ); ?>-filter">
		<label><?php echo esc_html( $filter['label'] ); ?></label>
		<div class="select-filter">
			<select name="<?php echo esc_attr( $filter['name'] ); ?>" onchange="this.form.submit()">
				<option value="">Select</option>
				<?php foreach ( $filter['options'] as $slug => $name ) : ?>
				<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $slug, $filter['current_value'] ); ?>><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<?php endforeach; ?>
</fieldset>
