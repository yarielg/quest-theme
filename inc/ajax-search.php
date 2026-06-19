<?php
defined( 'ABSPATH' ) || exit;

function quest_ajax_search(): void {
	$query = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

	if ( strlen( $query ) < 2 ) {
		wp_send_json( [ 'results' => [], 'total' => 0 ] );
	}

	$args = [
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 8,
		's'              => $query,
	];

	// Also search by SKU via meta query
	$meta_args = [
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 8,
		'meta_query'     => [ [
			'key'     => '_sku',
			'value'   => $query,
			'compare' => 'LIKE',
		] ],
	];

	$search_query = new WP_Query( $args );
	$sku_query    = new WP_Query( $meta_args );

	$product_ids = [];
	$results     = [];

	// Merge results, SKU matches first
	foreach ( [ $sku_query->posts, $search_query->posts ] as $posts ) {
		foreach ( $posts as $post ) {
			if ( isset( $product_ids[ $post->ID ] ) ) continue;
			$product_ids[ $post->ID ] = true;

			$product = wc_get_product( $post->ID );
			if ( ! $product ) continue;

			$thumb_id = $product->get_image_id();
			$img_url  = $thumb_id
				? wp_get_attachment_image_url( $thumb_id, 'thumbnail' )
				: wc_placeholder_img_src( 'thumbnail' );

			$cats     = wp_get_post_terms( $post->ID, 'product_cat', [ 'fields' => 'names' ] );
			$cat_name = ! is_wp_error( $cats ) && ! empty( $cats ) ? $cats[0] : '';

			$results[] = [
				'id'        => $post->ID,
				'name'      => $product->get_name(),
				'sku'       => $product->get_sku(),
				'url'       => $product->get_permalink(),
				'image'     => $img_url,
				'category'  => $cat_name,
			];

			if ( count( $results ) >= 8 ) break;
		}
		if ( count( $results ) >= 8 ) break;
	}

	$total = $search_query->found_posts;

	wp_send_json( [
		'results' => $results,
		'total'   => $total,
		'query'   => $query,
	] );
}
add_action( 'wp_ajax_quest_search', 'quest_ajax_search' );
add_action( 'wp_ajax_nopriv_quest_search', 'quest_ajax_search' );

function quest_search_localize(): void {
	wp_localize_script( 'quest-main', 'questSearch', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'shopUrl' => quest_shop_url(),
	] );
}
add_action( 'wp_enqueue_scripts', 'quest_search_localize', 30 );
