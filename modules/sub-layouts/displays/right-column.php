<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Core;

?><div class="core-sublayout-wrapper core-layout-<?php echo esc_attr( $layout ); ?>">
	<div class="core-sublayout-inner">
		<div class="core-layout-column column-right lc-column-one add-mobile-break">
			<?php do_action( 'layout_column_content_before', $layout ); ?>
			<?php
			// @codingStandardsIgnoreStart html should already be properly sanitized.
			echo $html;
			// @codingStandardsIgnoreEnd
			?>
			<?php do_action( 'layout_column_content_after', $layout ); ?>
		</div>
		<div class="core-layout-column column-right lc-column-two add-mobile-break">
			<?php do_action( 'layout_column_sidebar_before', $layout ); ?>
			<div class="core-sidebar-content"><?php if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) : ?><?php dynamic_sidebar( $sidebar ); ?><?php endif; ?></div>
			<?php do_action( 'layout_column_sidebar_after', $layout ); ?>
		</div>
	</div>
</div>
