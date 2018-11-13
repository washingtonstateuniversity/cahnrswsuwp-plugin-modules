<div class="core-post-feed-item promo<?php if ( ! empty( $image ) || ! empty( $image_placeholder ) ) : ?> has-image<?php endif; ?>">
	<?php if ( ! empty( $image ) || ! empty( $image_placeholder ) ) : ?>
	<div class="item-image-wrapper">
		<div class="item-image" style="background-image:url(<?php echo esc_url( $image ); ?> )">
		</div>
	</div>
	<?php endif; ?>
	<div class="item-caption-wrapper">
		<?php if ( ! empty( $title ) ) : ?>
			<<?php echo esc_html( $title_tag ); ?> class="item-title"><?php echo esc_html( $title ); ?></<?php echo esc_html( $title_tag ); ?>>
		<?php endif; ?>
		<?php if ( ! empty( $meta ) ) : ?>
			<div class="item-meta">
				<?php echo wp_kses_post( implode( ' | ', $meta ) ); ?>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $excerpt ) ) : ?>
			<div class="item-excerpt">
				<?php echo wp_kses_post( $excerpt ); ?>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $link ) ) : ?>
			<div class="item-link">
				<a href="<?php echo esc_url( $link ); ?>">Visit <?php echo esc_html( $title ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</div>
