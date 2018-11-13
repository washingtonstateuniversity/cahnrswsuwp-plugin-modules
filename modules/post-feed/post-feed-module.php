<?php namespace WSUWP\CAHNRSWSUWP_Plugin_Core;

if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly

} // End if


/**
 * Uses theme filters to add sub layouts to the page with sidebars.
 *
 * @version 0.0.1
 * @author CAHNRS Communications, Danial Bleile
 */
class Post_Feed_Module extends Core_Module {

	public $slug = 'post_feed';

	public $register_args = array(
		'label'          => 'Post Feed',
		'helper_text'    => 'Shortcode for displaying content.',
	);

	public $default_atts = array(
		'id'                     => 'core-post-feed',
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'protocol'               => 'http',
		'host'                   => '',
		'rest_path'              => '/wp-json/wp/v2/',
		'count'                  => 10,
		'tags'                   => '',
		'categories'             => '',
		'order_by'               => 'date',
		'order'                  => 'DESC',
		'offset'                 => 0,
		'page'                   => 1,
		'show_pagination'        => '',
		'show_search'            => '',
		'display'                => 'promo',
		'excerpt_length'         => 25,
		'show_author'            => '',
		'show_date'              => '',
		'tax_query_relation'     => 'AND',
		'image_size'             => 'medium',
		's'                      => '',
		'taxonomies'             => '',
		'title_tag'              => 'h3',
		'css_hook'               => '',
		'show_image_placeholder' => '',
		'year'                   => '',
		'author'                 => '',
		'exclude_author'         => '',
		'filters'                => '',
		'start_date'             => '',
		'show_venue'             => '1',
		'more_link'              => '',
		'more_label'             => 'More',
	);


	/**
	 * Init the module here
	 */
	public function init() {

		require_once __DIR__ . '/class-post-feed-display.php';

		add_shortcode( 'post_feed', array( $this, 'render_shortcode' ) );

	} // End init


	public function render_shortcode( $atts, $content, $tag ) {

		$atts = $this->parse_shortcode_atts( $atts );

		$atts = $this->get_request_atts( $atts );

		$atts = shortcode_atts( $this->default_atts, $atts, $tag );

		$query_items = $this->get_query_items( $atts );

		$atts['pages'] = ( ! empty( $query_items['pages'] ) ) ? (int) $query_items['pages'] : 0;

		$atts['per_page'] = ( ! empty( $query_items['per_page'] ) ) ? (int) $query_items['per_page'] : 10;

		$atts['total_items'] = ( ! empty( $query_items['total_items'] ) ) ? (int) $query_items['total_items'] : 0;

		$atts['filter_array'] = ( ! empty( $atts['filters'] ) ) ? $this->get_filters_array( $atts ) : array();

		$items = ( ! empty( $query_items['items'] ) ) ? $query_items['items'] : array();

		$post_feed_display = new Post_Feed_Display();

		ob_start();

		$post_feed_display->the_display( $items, $atts );

		$html = ob_get_clean();

		return $html;

	} // End render_shortcode


	private function parse_shortcode_atts( $atts ) {

		if ( ! empty( $atts['taxonomies'] ) ) {

			$atts['taxonomies'] = $this->parse_shortcode_taxonomy_atts( $atts );

		} // end if

		return $atts;

	} // End parse_shortcode_atts


	private function parse_shortcode_taxonomy_atts( $atts ) {

		$taxonomies = array();

		$shortcode_taxonomies = $atts['taxonomies'];

		$taxonomy_sets = explode( '},{', $shortcode_taxonomies );

		foreach ( $taxonomy_sets as $taxonomy_set ) {

			$taxonomy_set = str_replace( array( '{', '}' ), '', $taxonomy_set );

			$taxonomy_group = explode( '|', $taxonomy_set );

			if ( ! empty( $taxonomy_group ) ) {

				if ( ! empty( $taxonomy_group[0] ) ) {

					$taxonomy = array(
						'terms'    => ( ! empty( $taxonomy_group[1] ) ) ? $taxonomy_group[1] : '',
						'relation' => ( ! empty( $taxonomy_group[2] ) ) ? $taxonomy_group[2] : 'OR',
					);

					$taxonomies[ $taxonomy_group[0] ] = $taxonomy;

				} // End if
			} // end if
		} // End foreach

		return $taxonomies;

	} // End parse_shortcode_taxonomy_atts


	private function get_request_atts( $atts ) {

		if ( ! empty( $_REQUEST['pf_search'] ) ) {

			$atts['s'] = sanitize_text_field( $_REQUEST['pf_search'] );

		} // End if

		if ( ! empty( $_REQUEST['pf_year'] ) ) {

			$atts['year'] = sanitize_text_field( $_REQUEST['pf_year'] );

		} // End if

		if ( ! empty( $_REQUEST['pf_page'] ) ) {

			$atts['page'] = sanitize_text_field( $_REQUEST['pf_page'] );

		} // End if

		if ( ! empty( $_REQUEST['pf_author'] ) ) {

			$atts['author'] = sanitize_text_field( $_REQUEST['pf_author'] );

		} // End if

		if ( ! empty( $_REQUEST['taxonomies'] ) ) {

			if ( empty( $atts['taxonomies'] ) || ! is_array( $atts['taxonomies'] ) ) {

				$atts['taxonomies'] = array();

			} // End if

			foreach ( $_REQUEST['taxonomies'] as $taxonomy => $slug ) {

				$clean_tax = sanitize_text_field( $taxonomy );

				$clean_slug = sanitize_text_field( $slug );

				$atts['taxonomies'][ $clean_tax ] = $clean_slug;

			} // End foreach
		} // End if

		return $atts;

	}


	private function get_query_items( $atts ) {

		$default_query = array(
			'per_page'      => 0,
			'total_items'   => 0,
			'page'          => 1,
			'items'         => array(),
			'pages'         => 1,
		);

		if ( ! empty( $atts['host'] ) ) {

			// This is a REST request

		} else {

			// This is a local query
			$query = $this->get_local_query_items( $atts );

		} // end if

		$query_items = array_merge( $default_query, $query );

		return $query_items;

	} // End get_query_items


	private function get_local_query_items( $atts ) {

		$query_items = array(
			'items'       => array(),
			'page'        => ( ! empty( $atts['page'] ) ) ? $atts['page'] : 1,
		);

		$query_args = $this->get_local_query_args( $atts );

		$the_query = new \WP_Query( $query_args );

		$query_items['total_items'] = $the_query->found_posts;
		$query_items['pages']       = $the_query->max_num_pages;
		$query_items['paged']       = ( ! empty( $query_args['paged'] ) ) ? $query_args['paged'] : 1;
		$query_items['per_page']    = ( ! empty( $query_args['posts_per_page'] ) ) ? $query_args['posts_per_page'] : 10;

		// The Loop
		if ( $the_query->have_posts() ) {

			while ( $the_query->have_posts() ) {

				$the_query->the_post();

				$post_id = get_the_ID();

				$item = array(
					'is_local'  => true,
					'post_id'   => $post_id,
					'post_type' => get_post_type(),
					'title'     => get_the_title(),
					'author'    => get_the_author_meta( 'display_name' ),
					'date'      => get_the_date(),
					'has_image' => false,
					'image'     => array(),
					'link'      => get_the_permalink(),
				);

				if ( has_post_thumbnail() ) {

					$item['has_image'] = true;

					$item['image'] = array(
						'thumbnail' => get_the_post_thumbnail_url( $post_id ),
						'medium'    => get_the_post_thumbnail_url( $post_id, 'medium' ),
						'large'     => get_the_post_thumbnail_url( $post_id, 'large' ),
						'full'      => get_the_post_thumbnail_url( $post_id, 'full' ),
					);
				} // End if

				ob_start();

				the_content();

				$item['content'] = ob_get_clean();

				ob_start();

				the_excerpt();

				$item['excerpt'] = ob_get_clean();

				switch ( $item['post_type'] ) {

					case 'tribe_events':
						$item = $this->get_tribe_events_item( $item, $post_id, $atts );
						break;

				} // End switch

				$item = apply_filters( 'core_post_feed_local_item_array', $item, $post_id, $atts );

				$query_items['items'][ $post_id ] = $item;

			} // End while

			wp_reset_postdata();

		} // End if

		return $query_items;

	}


	private function get_tribe_events_item( $item, $post_id, $atts ) {

		$item['start_date'] = ( function_exists( 'tribe_get_start_date' ) ) ? strtotime( tribe_get_start_date( $post_id, false, 'Y-m-d H:i:s' ) ) : '';

		$item['link'] = ( function_exists( 'tribe_get_event_link' ) ) ? tribe_get_event_link( $post_id ) : '';

		$item['venue'] = ( function_exists( 'tribe_get_venue' ) ) ? tribe_get_venue( $post_id ) : '';

		return $item;

	} // End get_tribe_events_item


	private function get_remote_query_items( $atts ) {

	}


	private function get_filters_array( $atts ) {

		$filters = array();

		if ( ! empty( $atts['filters'] ) ) {

			$atts_filters = explode( '},{', $atts['filters'] );

			foreach ( $atts_filters as $atts_filter ) {

				$filter = array();

				$atts_filter = str_replace( array( '{', '}' ), '', $atts_filter );

				$filter_settings = $this->parse_filter_atts( $atts_filter );

				switch ( $filter_settings['type'] ) {

					case 'built-in':
						$filter = $this->get_filter_built_in( $filter_settings, $atts );
						break;

					case 'taxonomy':
						$filter = $this->get_filter_taxonomy( $filter_settings, $atts );
						break;

				} // End Switch

				if ( ! empty( $filter ) ) {

					$filters[] = $filter;

				} // End if
			} // End foreach
		} // End if

		return $filters;

	} // End get_filters_html


	private function get_filter_taxonomy( $filter_settings, $atts ) {

		$taxonomy = ( ! empty( $filter_settings['name'] ) ) ? $filter_settings['name'] : 'category';

		$include_terms = ( ! empty( $filter_settings['terms'] ) ) ? $filter_settings['terms'] : array();

		$filter_array = array(
			'type'           => 'taxonomy',
			'name'           => 'taxonomies[' . $taxonomy . ']',
			'taxonomy'       => $taxonomy,
			'class'          => $taxonomy,
			'label'          => ( ! empty( $filter_settings['label'] ) ) ? $filter_settings['label'] : 'Filter By:',
			'terms'          => $include_terms,
			'current_value'  => '',
			'options'   => $this->get_filter_term_options( $taxonomy, $include_terms ),
		);

		if ( isset( $_REQUEST['taxonomies'][ $taxonomy ] ) && ! empty( $_REQUEST['taxonomies'][ $taxonomy ] ) ) {

			$filter_array['current_value'] = sanitize_text_field( $_REQUEST['taxonomies'][ $taxonomy ] );

		} // End if

		return $filter_array;

	} // End get_filter_taxonomy


	private function get_filter_built_in( $filter_settings, $atts ) {

		$filter = array();

		if ( ! empty( $filter_settings['name'] ) ) {

			switch ( $filter_settings['name'] ) {

				case 'author':
					$filter_array = $this->get_filter_built_in_author( $filter_settings, $atts );
					break;

				case 'year':
					$filter_array = $this->get_filter_built_in_year( $filter_settings, $atts );
					break;

			} // End switch

			if ( ! empty( $filter_array ) ) {

				$filter = $filter_array;

			} // End if
		} // End if

		return $filter;

	} // End get_filter_built_in


	private function get_filter_built_in_author( $filter_settings, $atts ) {

		// Taken from https://core.trac.wordpress.org/browser/tags/4.9.8/src/wp-includes/author-template.php#L421

		global $wpdb;

		$query_args = array(
			'fields' => 'ids',
		);

		$post_type = ( ! empty( $atts['post_type'] ) ) ? $atts['post_type'] : 'post';

		$authors = get_users( $query_args );

		$author_count = array();

		$author_options = array();

		$exclude = ( ! empty( $filter_settings['exclude'] ) && is_array( $filter_settings['exclude'] ) ) ? $filter_settings['exclude'] : array();

		//$sql_query = $wpdb->prepare( 'SELECT DISTINCT post_author, COUNT(ID) AS count FROM %s WHERE post_type=%s GROUP BY post_author', array( $wpdb->posts, $post_type ) );

		$sql_query = $wpdb->prepare( 'SELECT DISTINCT post_author, COUNT(ID) AS count FROM %5s WHERE post_type LIKE %s GROUP BY post_author', array( $wpdb->posts, $post_type ) );

		$post_query = $wpdb->get_results( $sql_query );

		if ( is_array( $post_query ) ) {

			foreach ( $post_query as $row ) {

				$author_count[ $row->post_author ] = $row->count;

			} // End foreach
		} // End if

		//uasort( $authors, array( $this, 'sort_authors' ) );

		foreach ( $authors as $author_id ) {

			$author = get_userdata( $author_id );

			if ( ! in_array( $author->display_name, $exclude, true ) ) {

				$posts = isset( $author_count[ $author->ID ] ) ? $author_count[ $author->ID ] : 0;

				if ( ! $posts ) {

					continue;

				} // End if

				$author_options[ $author->ID ] = $author->display_name;

			} // End if
		} // End foreach

		uasort( $author_options, array( $this, 'sort_authors' ) );

		$filter = array(
			'type'          => 'built-in',
			'name'          => 'pf_author',
			'label'         => 'Author',
			'options'       => $author_options,
			'current_value' => ( ! empty( $atts['author'] ) ) ? $atts['author'] : '',
			'class'         => 'pf_author',
		);

		return $filter;

	} // End get_author_filter


	public function sort_authors( $a, $b ) {

		$a_name = array_pop( explode( ' ', $a ) );

		$b_name = array_pop( explode( ' ', $b ) );

		return ( $a_name < $b_name ) ? -1 : 1;

	} // End sort_authors


	private function parse_filter_atts( $filter_atts ) {

		$filter_array = explode( '|', $filter_atts );

		$filter = array(
			'type'     => ( ! empty( $filter_array[0] ) ) ? $filter_array[0] : 'taxonomy',
			'name'     => ( ! empty( $filter_array[1] ) ) ? $filter_array[1] : 'category',
			'label'    => ( ! empty( $filter_array[2] ) ) ? $filter_array[2] : 'Categories',
			'terms'    => ( ! empty( $filter_array[3] ) ) ? explode( ',', $filter_array[3] ) : array(),
			'exclude'  => ( ! empty( $filter_array[4] ) ) ? explode( ',', $filter_array[4] ) : array(),
			'class'    => ( ! empty( $filter_array[5] ) ) ? $filter_array[5] : '',
		);

		return $filter;

	}


	private function get_filter_built_in_year( $filter_settings, $atts ) {

		$years = array();

		$current_year = date( 'Y' );

		for ( $y = 0; $y < 20; $y++ ) {

			$year = $current_year - $y;

			$years[ $year ] = $year;

		} // End for

		$filter = array(
			'type'          => 'built-in',
			'name'          => 'pf_year',
			'label'         => 'Year',
			'options'       => $years,
			'current_value' => ( ! empty( $atts['year'] ) ) ? $atts['year'] : '',
			'class'         => 'pf_year',
		);

		return $filter;

	} // End get_year_filter


	private function get_filter_term_options( $taxonomy, $term_ids ) {

		$term_options = array();

		if ( ! empty( $term_ids ) ) {

			foreach ( $term_ids as $term_id ) {

				$term = get_term_by( 'id', $term_id, $taxonomy );

				$term_options[ $term->slug ] = $term->name;

			} // End foreach
		} else {

			$args = array();

			$terms = get_terms( $taxonomy, $args );

			if ( is_array( $terms ) ) {

				foreach ( $terms as $index => $term ) {

					$term_options[ $term->slug ] = $term->name;

				} // End foreach
			} // End if
		} // End if

		return $term_options;

	} // End get_filter_term_options


	private function get_local_query_args( $atts ) {

		$query_args = array(
			'post_type'      => $atts['post_type'],
			'post_status'    => $atts['post_status'],
			'posts_per_page' => $atts['count'],
			'order_by'       => $atts['order_by'],
			'order'          => $atts['order'],
			'paged'          => $atts['page'],
		);

		switch ( $atts['post_type'] ) {

			case 'tribe_events':
				$query_args['order']      = 'ASC';
				$query_args['start_date'] = ( ! empty( $atts['start_date'] ) ) ? $atts['start_date'] : date( 'Y-m-d H:i:s' );
				break;

		} // End switch

		if ( ! empty( $atts['s'] ) ) {

			$query_args['s'] = $atts['s'];

		} // End if

		if ( ! empty( $atts['year'] ) ) {

			$query_args['year'] = $atts['year'];

		} // End if

		if ( ! empty( $atts['author'] ) ) {

			$query_args['author'] = $atts['author'];

		} // End if

		if ( ! empty( $atts['offset'] ) && ( 2 > $query_args['paged'] ) ) {

			$query_args['offset'] = $atts['offset'];

		} // End if

		$taxonomy_query = $this->get_local_taxonomy_query( $atts );

		if ( ! empty( $taxonomy_query ) ) {

			$query_args['tax_query'] = $taxonomy_query;

			$query_args['tax_query']['relation'] = $atts['tax_query_relation'];

		} // End if

		//var_dump( $query_args );

		return $query_args;

	} // End get_local_query_args


	private function get_local_taxonomy_query( $atts ) {

		$taxonomy_query = array();

		if ( ! empty( $atts['tags'] ) ) {

			$tag_terms = explode( ',', $atts['tags'] );

			$tag_query = array(
				'taxonomy' => 'post_tag',
				'field'    => 'term_id',
				'terms'    => $tag_terms,
			);

			$taxonomy_query[] = $tag_query;

		} // End if

		if ( ! empty( $atts['categories'] ) ) {

			$category_terms = explode( ',', $atts['categories'] );

			$category_query = array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => $category_terms,
			);

			$taxonomy_query[] = $category_query;

		} // End if

		if ( ! empty( $atts['taxonomies'] ) && is_array( $atts['taxonomies'] ) ) {

			foreach ( $atts['taxonomies'] as $taxonomy => $tax ) {

				if ( ! empty( $tax ) ) {

					if ( is_array( $tax ) && ! empty( $tax['terms'] ) ) {

						$terms = sanitize_text_field( $tax['terms'] );

						$terms = explode( ',', $terms );

						$tax_query = array(
							'taxonomy' => sanitize_text_field( $taxonomy ),
							'field'    => 'term_id',
							'terms'    => $terms,
						);

						$taxonomy_query[] = $tax_query;

					} else {

						$tax_query = array(
							'taxonomy' => sanitize_text_field( $taxonomy ),
							'field'    => 'slug',
							'terms'    => sanitize_text_field( $tax ),
						);

						$taxonomy_query[] = $tax_query;

					}// End if
				} // End if
			} // End foreach
		} // End if

		return $taxonomy_query;

	} // get_local_taxonomy_query


} // End Sub_Layouts

$ccore_post_feed_module = new Post_Feed_Module();
