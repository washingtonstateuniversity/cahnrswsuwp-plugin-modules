<div class="core-form">
	<fieldset>
		<div class="core-form-content">
			<div class="core-field core-text-field">
				<label>Permanent Project ID</label>
				<input type="text" name="_grant[project_id]" value="<?php echo esc_attr( $project_id ); ?>">
			</div>
		<div class="core-form-content">
	</fieldset>
	<?php foreach ( $annual_entries as $index => $entry ) : ?>
	<fieldset class="core-fieldset-group">
		<p class="core-fieldset-title"><?php echo esc_html( $entry['title'] ); ?></p>
		<div class="core-form-content">
			<div class="core-field core-select-field core-field-full-width core-field-small">
				<label>Year</label>
				<div class="core-field-helper-text">* Setting Year to "Select" will remove this entry.</div>
				<select name="_grant[annual_entries][<?php echo esc_attr( $index ); ?>][year]">
					<option value="">Select</option>
					<?php for ( $y = 2000; $y < 2050; $y++ ) : ?>
					<option value="<?php echo esc_attr( $y ); ?>" <?php selected( $entry['year'], $y ); ?>><?php echo esc_html( $y ); ?></option>
					<?php endfor; ?>
				</select>
			</div>
			<div class="core-field core-select-field core-field-third-width">
				<label>PI(s)</label>
				<select multiple="multiple" name="_grant[annual_entries][<?php echo esc_attr( $index ); ?>][pi][]">
					<option value="">Select</option>
					<?php foreach ( $investigators as $id => $label ) : ?>
					<option value="<?php echo esc_attr( $id ); ?>" <?php if ( in_array( (string) $id, $entry['pi'], true ) ) : ?>selected="selected"<?php endif; ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="core-field core-select-field core-field-third-width">
				<label>Additional Investigator(s)</label>
				<select multiple="multiple" name="_grant[annual_entries][<?php echo esc_attr( $index ); ?>][additional][]">
					<option value="">Select</option>
					<?php foreach ( $investigators as $id => $label ) : ?>
					<option value="<?php echo esc_attr( $id ); ?>" <?php if ( in_array( (string) $id, $entry['additional'], true ) ) : ?>selected="selected"<?php endif; ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="core-field core-select-field core-field-third-width">
				<label>Student(s)</label>
				<select multiple="multiple" name="_grant[annual_entries][<?php echo esc_attr( $index ); ?>][students][]">
					<option value="">Select</option>
					<?php foreach ( $investigators as $id => $label ) : ?>
					<option value="<?php echo esc_attr( $id ); ?>" <?php if ( in_array( (string) $id, $entry['students'], true ) ) : ?>selected="selected"<?php endif; ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="core-field core-text-field core-field-full-width">
				<label>Progress Report</label>
				<input type="text" name="_grant[annual_entries][<?php echo esc_attr( $index ); ?>][progress]" value="<?php echo esc_attr( $entry['progress'] ); ?>">
			</div>
			<div class="core-field core-text-field core-field-full-width">
				<label>Additional Progress Report</label>
				<input type="text" name="_grant[annual_entries][<?php echo esc_attr( $index ); ?>][additional_progress]" value="<?php echo esc_attr( $entry['additional_progress'] ); ?>">
			</div>
			<div class="core-field core-text-field core-field-small">
				<label>Grant Amount</label>
				$<input type="text" name="_grant[annual_entries][<?php echo esc_attr( $index ); ?>][amount]" value="<?php echo esc_attr( $entry['amount'] ); ?>">
			</div>
		</div>
	</fieldset>
	<?php endforeach; ?>
	<fieldset>
		<p class="core-fieldset-title">Additional Funds</p>
		<div class="core-form-content">
			<?php foreach ( $additional_funds as $index => $fund ) : ?>
			<div class="core-field core-text-field">
				<label>Fund Name</label>
				<input type="text" name="_grant[additional_funds][<?php echo esc_attr( $index ); ?>][label]" value="<?php echo esc_attr( $fund['label'] ); ?>">
			</div>
			<div class="core-field core-text-field">
				<label>Fund Amount</label>
				$<input type="text" name="_grant[additional_funds][<?php echo esc_attr( $index ); ?>][amount]" value="<?php echo esc_attr( $fund['amount']  ); ?>">
			</div>
			<?php endforeach; ?>
		</div>
	</fieldset>
	<fieldset>
		<div class="core-form-content">
			<div class="core-field core-wp-editor-field">
				<label>Publications</label>
				<?php wp_editor( $publications_content, '_grants_publications_content', array( 'editor_height' => '200px' ) ); ?>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<div class="core-form-content">
			<div class="core-field core-wp-editor-field">
				<label>Additional Funds Leveraged</label>
				<?php wp_editor( $funding_content, '_grants_additional_funding_content', array( 'editor_height' => '200px' ) ); ?>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<div class="core-form-content">
			<div class="core-field core-wp-editor-field">
				<label>Impacts and Outcomes</label>
				<?php wp_editor( $impact_content, '_grants_impacts_content', array( 'editor_height' => '200px' ) ); ?>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<div class="core-form-content">
			<div class="core-field core-wp-editor-field">
				<label>Administrative Comments</label>
				<?php wp_editor( $admin_content, '_grants_admin_content', array( 'editor_height' => '200px' ) ); ?>
			</div>
		</div>
	</fieldset>
</div>
