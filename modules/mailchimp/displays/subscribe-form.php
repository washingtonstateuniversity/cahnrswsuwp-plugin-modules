<div class="mc_embed_signup">
	<form action="<?php echo esc_url( $host ); ?>/subscribe/post-json?u=<?php echo esc_attr( $key ); ?>&amp;id=<?php echo esc_attr( $list_id ); ?>&c=?" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate="novalidate">
		<div id="mc_embed_signup_scroll">
	<?php if ( ! empty( $title ) ) : ?><<?php echo esc_html( $title_tag ); ?>><?php echo esc_html( $title ); ?></<?php echo esc_html( $title_tag ); ?>><?php endif; ?>
		<fieldset>
	<div class="mc-field-group">
		<label for="mce-EMAIL">Email Address
	</label>
		<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" aria-required="true">
	</div>
	<?php if ( ! empty( $show_names ) ) : ?>
	<div class="mc-field-group">
		<label for="mce-FNAME">First Name </label>
		<input type="text" value="" name="FNAME" class="" id="mce-FNAME">
	</div>
	<div class="mc-field-group">
		<label for="mce-LNAME">Last Name </label>
		<input type="text" value="" name="LNAME" class="valid" id="mce-LNAME" aria-invalid="false">
	</div>
	<?php endif; ?>
		<div id="mce-responses" class="clear">
			<div class="response" id="mce-error-response" style="display:none"></div>
			<div class="response" id="mce-success-response" style="display:none"></div>
		</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
		<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_<?php echo esc_attr( $key ); ?>_<?php echo esc_attr( $list_id ); ?>" tabindex="-1" value=""></div>
		<div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
		</div>
		</fieldset>
	</form>
</div>
