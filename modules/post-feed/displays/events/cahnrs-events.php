<div class="cahnrs-event">
	<div class="cahnrs-event-link"><a href="<?php echo esc_url( $link ); ?>" rel="bookmark">View Event Details</a></div>
	<div class="cahnrs-event-calendar-details">
		<span class="cahnrs-day"><?php echo esc_html( $date_day ); ?></span>
		<span class="cahnrs-month"><?php echo esc_html( $date_month ); ?></span>
	</div>
	<div class="cahnrs-event-details"><h3><?php echo esc_html( $title ); ?></h3><?php if ( ! empty( $venue ) ) : ?><div class="cahnrs-event-venue"><?php echo esc_html( $venue ); ?></div><?php endif; ?>
		<div class="cahnrs-event-button">Event Details</div>
	</div>
</div>
