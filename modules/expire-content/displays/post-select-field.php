<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Core;

?><div class="misc-pub-section">
	<label>Expire After: (mm/dd/yyyy)</label><br />
	<select name="_expire_in_month">
		<option value="0">mm</option>
		<?php for ( $m = 1; $m < 13; $m++ ) :
			$month = ( 2 > strlen( $m ) ) ? '0' . $m : $m; ?>
		<option value="<?php echo esc_attr( $month ); ?>" <?php selected( $month, $current_expire_month ); ?> ><?php echo esc_html( $month ); ?></option>
		<?php endfor; ?>
	</select>/<select name="_expire_in_day">
		<option value="0">dd</option>
		<?php for ( $d = 1; $d < 32; $d++ ) :
			$day = ( 2 > strlen( $d ) ) ? '0' . $d : $d; ?>
		<option value="<?php echo esc_attr( $day ); ?>" <?php selected( $day, $current_expire_day ); ?> ><?php echo esc_html( $day ); ?></option>
		<?php endfor; ?>
	</select>/<select name="_expire_in_year">
		<option value="0">yyyy</option>
		<?php
		$y_start = (int) date( 'Y' );
		for ( $y = $y_start; $y < 2040; $y++ ) : ?>
		<option value="<?php echo esc_attr( $y ); ?>" <?php selected( $y, $current_expire_year ); ?> ><?php echo esc_html( $y ); ?></option>
		<?php endfor; ?>
	</select>
</div>
