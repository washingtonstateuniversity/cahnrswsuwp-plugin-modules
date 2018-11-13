<div class="event-item">
	<div class="event-calendar-icon">
		<div class="event-month"><?php echo esc_html( $date_month ); ?></div>
		<div class="event-day"><?php echo esc_html( $date_day ); ?></div>
	</div>
	<div class="event-content">
		<h2 class="event-title"><?php echo esc_html( $title ); ?></h2>
		<?php if ( ! empty( $venu ) ) : ?><div class="event-venue"><?php echo esc_html( $venu ); ?></div><?php endif; ?>
		<div class="event-link"><a href="<?php echo esc_url( $link ); ?>" rel="bookmark">View Event Details</a></div>
	</div>
</div>
