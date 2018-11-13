<fieldset class="core-post-feed-pagination<?php if ( $is_end ) : ?> pagination-after<?php endif; ?>">
	<div class="core-post-feed-pagination-summary">
		Now showing <?php echo esc_html( $start_index ); ?> - <?php echo esc_html( $end_index ); ?> of <?php echo esc_html( $total_items ); ?><?php if ( 1 < $pages ) : ?>
	</div>
	<div class="core-post-feed-pagination-nav">
		<button type="submit" name="pf_page" value="<?php echo esc_attr( $previous_page );?>"<?php if ( 1 > $previous_page ) : ?> disabled="disabled"<?php endif;?>>Previous</button>
		<button type="submit" name="pf_page" value="<?php echo esc_attr( $next_page );?>"<?php if ( $next_page > $pages ) : ?> disabled="disabled"<?php endif;?>>Next</button><?php endif; ?>
	</div>
</fieldset>
