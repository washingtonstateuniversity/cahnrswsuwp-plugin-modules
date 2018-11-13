<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Modules;


/**
 * Uses theme filters to add sub layouts to the page with sidebars.
 *
 * @version 0.0.1
 * @author CAHNRS Communications, Danial Bleile
 */
class Post_Feed_Display {


	public function the_display( $items, $settings ) {

		//var_dump( $settings );

		echo '<div class="core-post-feed"><form class="core-post-form" method="get">';

		$this->the_search( $settings );

		$this->the_filters( $settings );

		$this->the_pagination( $settings );

		$this->the_items_html( $items, $settings );

		$this->the_pagination( $settings, true );

		echo '</form></div>';

	} // End the_dipslay


	private function the_filters( $settings ) {

		if ( ! empty( $settings['filter_array'] ) ) {

			$filters_array = $settings['filter_array'];

			include __DIR__ . '/displays/filters.php';

		} // End if

	} // End the_filters


	private function the_items_html( $items, $settings ) {

		$display = ( ! empty( $settings['display'] ) ) ? $settings['display'] : 'promo';

		$post_type = ( ! empty( $settings['post_type'] ) ) ? $settings['post_type'] : '';

		echo '<div class="core-post-feed-items display-' . esc_attr( $display ) . ' post-type-' . esc_attr( $post_type ) . '">';

		$html = apply_filters( 'core_post_feed_items_html', '', $items, $settings );

		if ( ! empty( $html ) ) {

			echo wp_kses_post( $html );

		} else {

			if ( ! empty( $items ) ) {

				switch ( $display ) {

					case 'events-row':
						$this->the_events_row_display( $items, $settings );
						break;

					case 'cahnrs-events':
						$this->the_cahnrs_events_display( $items, $settings );
						break;

					default:
						$this->the_promo_display( $items, $settings );
						break;

				} // End switch
			} else {

				include __DIR__ . '/displays/no-items-found.php';

			} // End if
		} // End if

		echo '</div>';

	} // end get_items_html



	public function the_events_row_display( $items, $settings ) {

		foreach ( $items as $post_id => $item ) {

			$title             = ( ! empty( $item['title'] ) ) ? $item['title'] : '';
			$title_tag         = ( ! empty( $settings['title_tag'] ) ) ? $settings['title_tag'] : 'h3';
			$content           = ( ! empty( $item['content'] ) ) ? $item['content'] : '';
			$link              = ( ! empty( $item['link'] ) ) ? $item['link'] : '';
			$author            = ( ! empty( $item['author'] ) ) ? $item['author'] : '';
			$date              = ( ! empty( $item['date'] ) ) ? $item['date'] : '';
			$image_placeholder = ( ! empty( $item['show_image_placeholder'] ) ) ? $item['show_image_placeholder'] : '';
			$excerpt           = $this->get_item_excerpt( $item, $settings );
			$image             = $this->get_item_image( $item, $settings );
			$meta              = $this->get_item_meta( $item, $settings );
			$date_day          = ( ! empty( $item['start_date'] ) ) ? date( 'j', $item['start_date'] ) : '';
			$date_month        = ( ! empty( $item['start_date'] ) ) ? date( 'M', $item['start_date'] ) : '';
			$venue             = ( ! empty( $settings['show_venue'] ) ) ? $item['venue'] : '';

			include __DIR__ . '/displays/events/events-row.php';

		} // End foreach

		if ( ! empty( $settings['more_link'] ) ) {

			$link = $settings['more_link'];
			$label = ( ! empty( $settings['more_label'] ) ) ? $settings['more_label'] : 'More';

			include __DIR__ . '/displays/general/more.php';

		} // End if

	} // End the_cahnrs_events_display


	public function the_cahnrs_events_display( $items, $settings ) {

		foreach ( $items as $post_id => $item ) {

			$title             = ( ! empty( $item['title'] ) ) ? $item['title'] : '';
			$title_tag         = ( ! empty( $settings['title_tag'] ) ) ? $settings['title_tag'] : 'h3';
			$content           = ( ! empty( $item['content'] ) ) ? $item['content'] : '';
			$link              = ( ! empty( $item['link'] ) ) ? $item['link'] : '';
			$author            = ( ! empty( $item['author'] ) ) ? $item['author'] : '';
			$date              = ( ! empty( $item['date'] ) ) ? $item['date'] : '';
			$image_placeholder = ( ! empty( $item['show_image_placeholder'] ) ) ? $item['show_image_placeholder'] : '';
			$excerpt           = $this->get_item_excerpt( $item, $settings );
			$image             = $this->get_item_image( $item, $settings );
			$meta              = $this->get_item_meta( $item, $settings );
			$date_day          = ( ! empty( $item['start_date'] ) ) ? date( 'j', $item['start_date'] ) : '';
			$date_month        = ( ! empty( $item['start_date'] ) ) ? date( 'M', $item['start_date'] ) : '';
			$venue             = ( ! empty( $settings['show_venu'] ) ) ? $item['venue'] : '';

			include __DIR__ . '/displays/events/cahnrs-events.php';

		} // End foreach

	} // End the_cahnrs_events_display


	public function the_promo_display( $items, $settings ) {

		foreach ( $items as $post_id => $item ) {

			$title             = ( ! empty( $item['title'] ) ) ? $item['title'] : '';
			$title_tag         = ( ! empty( $settings['title_tag'] ) ) ? $settings['title_tag'] : 'h3';
			$content           = ( ! empty( $item['content'] ) ) ? $item['content'] : '';
			$link              = ( ! empty( $item['link'] ) ) ? $item['link'] : '';
			$author            = ( ! empty( $item['author'] ) ) ? $item['author'] : '';
			$date              = ( ! empty( $item['date'] ) ) ? $item['date'] : '';
			$image_placeholder = ( ! empty( $item['show_image_placeholder'] ) ) ? $item['show_image_placeholder'] : '';
			$excerpt           = $this->get_item_excerpt( $item, $settings );
			$image             = $this->get_item_image( $item, $settings );
			$meta              = $this->get_item_meta( $item, $settings );

			include __DIR__ . '/displays/promo.php';

		} // End foreach

	} // End get_promo_display


	public function the_search( $settings ) {

		if ( ! empty( $settings['show_search'] ) ) {

			$keyword = $settings['s'];

			include __DIR__ . '/displays/search.php';

		} // End if

	} // End the_search


	public function the_pagination( $settings, $is_end = false ) {

		if ( ! empty( $settings['show_pagination'] ) ) {

			$pages = ( ! empty( $settings['pages'] ) ) ? (int) $settings['pages'] : 0;

			$current_page = ( ! empty( $settings['page'] ) ) ? (int) $settings['page'] : 1;

			$per_page = ( ! empty( $settings['per_page'] ) ) ? (int) $settings['per_page'] : 10;

			$total_items = ( ! empty( $settings['total_items'] ) ) ? (int) $settings['total_items'] : 0;

			if ( 1 === $current_page ) {

				$start_index = 1;

			} else {

				$start_index = ( ( $current_page - 1 ) * $per_page );

			} // End if

			$end_index = ( ( $current_page ) * $per_page );

			if ( $end_index > $total_items ) {

				$end_index = $total_items;

			} // end if

			$next_page = ( $current_page + 1 );

			$previous_page = ( $current_page - 1 );

			include __DIR__ . '/displays/pagination.php';

		} // End if

	} // End the_pagination


	private function get_item_meta( $item, $settings ) {

		$meta = array();

		if ( ! empty( $item['author'] ) && ! empty( $settings['show_author'] ) ) {

			$meta[] = '<span class="item-author">Posted by ' . $item['author'] . '</span>';

		} // End if

		if ( ! empty( $item['date'] ) && ! empty( $settings['show_date'] ) ) {

			$meta[] = '<span class="item-date">' . $item['date'] . '</span>';

		} // End if

		return $meta;

	} // End get_item_meta


	private function get_item_image( $item, $settings ) {

		$image = '';

		if ( ! empty( $item['has_image'] ) && ! empty( $item['image'] ) ) {

			$size = ( ! empty( $item['image_size'] ) ) ? $item['image_size'] : 'medium';

			if ( ! empty( $item['image'][ $size ] ) ) {

				$image = $item['image'][ $size ];

			} // End if
		} // End if

		return $image;

	} // End if


	private function get_item_excerpt( $item, $settings ) {

		$excerpt = ( ! empty( $item['excerpt'] ) ) ? $item['excerpt'] : '';

		if ( ! empty( $settings['excerpt_length'] ) ) {

			$excerpt = wp_trim_words( $excerpt, $settings['excerpt_length'] );

		} // End if

		return $excerpt;

	} // End get_item_excerpt

} // End Post_Feed_Display
