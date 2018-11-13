<!--<h1>Sub Layout Settings</h1>
<form method="post">
	<input name="option_page" value="reading" type="hidden"><input name="action" value="update" type="hidden"><input id="_wpnonce" name="_wpnonce" value="b1b00629a9" type="hidden"><input name="_wp_http_referer" value="/core/wp-admin/options-reading.php" type="hidden">
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">Select Sub Layout</th>
				<td id="core_sublayout-option">
					<select name="core_sublayout" id="core_sublayout_layout_select" class="postform">
						<option class="level-0" value="default">Default</option>
						<option class="level-0" value="left-column">Left Column</option>
						<option class="level-0" value="right-column">Right Column</option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit">
		<input name="submit" id="submit" class="button button-primary" value="Save Changes" type="submit">
	</p>
</form>-->
<div class="wrap">
	<div id="icon-options-general" class="icon32"></div>
	<h1>Sub Layout Options</h1>
	<form method="post" action="options.php">
		<?php

			//add_settings_section callback is displayed here. For every new section we need to call settings_fields.
			\settings_fields( $page_slug );

			// all the add_settings_field callbacks is displayed here
			\do_settings_sections( $page_slug );

			// Add the submit button to serialize the options
			\submit_button();

		?>
	</form>
</div>