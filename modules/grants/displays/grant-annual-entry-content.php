<div class="grant-annual-entry">
<?php if ( ! empty( $year ) ) : ?><h3><?php echo esc_html( $year ); ?></h3><?php endif; ?>
	<ul>
		<?php if ( ! empty( $pi ) ) : ?><li class="grant-data-item grants-pi"><span class="grant-label">Principal Investigator(s):</span> <?php echo esc_html( implode( ', ', $pi ) ); ?></li><?php endif; ?>
		<?php if ( ! empty( $additional_investigators ) ) : ?><li class="grant-data-item grants-additional-investigators"><span class="grant-label">Investigator(s):</span> <?php echo esc_html( implode( ', ', $additional_investigators ) ); ?></li><?php endif; ?>
		<?php if ( ! empty( $students ) ) : ?><li class="grant-data-item grants-students"><span class="grant-label">Student(s):</span> <?php echo esc_html( implode( ', ', $students ) ); ?></li><?php endif; ?>
		<?php if ( ! empty( $grant_amount ) ) : ?><li class="grant-data-item grants-amount"><span class="grant-label">Grant Amount:</span> $<?php echo esc_html( $grant_amount ); ?></li><?php endif; ?>
		<?php if ( ! empty( $progress_report_url  ) ) : ?><li class="grant-button">
			<a href="<?php echo esc_url( $progress_report_url ); ?>" ><?php if ( ! empty( $year ) ) : ?><?php echo esc_html( $year ); ?><?php endif; ?> Progress Report</a>
		</li><?php endif; ?>
		<?php if ( ! empty( $additional_progress_report  ) ) : ?><li class="grant-button">
			<a href="<?php echo esc_url( $additional_progress_report ); ?>" ><?php if ( ! empty( $year ) ) : ?><?php echo esc_html( $year ); ?><?php endif; ?> Additional Progress Report</a>
		</li><?php endif; ?>
	</ul>
</div>
