<?php if ( ! empty( $publications_content ) ) : ?><div class="grant-publications">
	<h3>Publications</h3>
	<?php echo wp_kses_post( $publications_content ); ?>
</div><?php endif; ?>
<?php if ( ! empty( $funding_content ) ) : ?><div class="grant-funding">
	<h3>Additional Funds Leveraged</h3>
	<?php echo wp_kses_post( $funding_content ); ?>
</div><?php endif; ?>
<?php if ( ! empty( $impact_content ) ) : ?><div class="grant-publications">
	<h3>Impacts</h3>
	<?php echo wp_kses_post( $impact_content ); ?>
</div><?php endif; ?>

