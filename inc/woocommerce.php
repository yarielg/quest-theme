<?php
defined( 'ABSPATH' ) || exit;

// ---------------------------------------------------------------------------
// Catalog mode — conditionally remove add-to-cart, prices, cart
// ---------------------------------------------------------------------------
// Remove add-to-cart for guests and catalog mode
add_filter( 'woocommerce_is_purchasable', function ( bool $purchasable ): bool {
	if ( ! is_user_logged_in() || quest_is_catalog_mode() ) {
		return false;
	}
	return $purchasable;
} );

add_filter( 'woocommerce_variation_is_purchasable', function ( bool $purchasable ): bool {
	if ( ! is_user_logged_in() || quest_is_catalog_mode() ) {
		return false;
	}
	return $purchasable;
} );

add_action( 'wp', function (): void {
	if ( ! is_user_logged_in() || quest_is_catalog_mode() ) {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
		remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
		remove_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
		remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
		remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	}
} );

add_action( 'template_redirect', function (): void {
	if ( ! is_user_logged_in() || quest_is_catalog_mode() ) {
		if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
			wp_safe_redirect( quest_shop_url() );
			exit;
		}
	}
} );

// ---------------------------------------------------------------------------
// Move SKU/meta above the price on single product pages
// ---------------------------------------------------------------------------
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 9 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );

// ---------------------------------------------------------------------------
// Remove "Additional information" tab, rename "Description" to "Features"
// ---------------------------------------------------------------------------
add_filter( 'woocommerce_product_tabs', function ( array $tabs ): array {
	unset( $tabs['additional_information'] );
	if ( isset( $tabs['description'] ) ) {
		$tabs['description']['title'] = __( 'Features', 'quest' );
	}
	return $tabs;
}, 98 );

add_filter( 'woocommerce_product_description_heading', '__return_empty_string' );
add_filter( 'woocommerce_product_additional_information_heading', '__return_empty_string' );

// ---------------------------------------------------------------------------
// Support high variation count without breaking AJAX
// ---------------------------------------------------------------------------
add_filter( 'woocommerce_ajax_variation_threshold', function (): int {
	return 100;
}, 10 );

// ---------------------------------------------------------------------------
// Force classic checkout template (bypass WC Block checkout)
// ---------------------------------------------------------------------------
add_filter( 'render_block', function ( string $content, array $block ): string {
	if ( ( $block['blockName'] ?? '' ) !== 'woocommerce/checkout' ) {
		return $content;
	}
	ob_start();
	echo do_shortcode( '[woocommerce_checkout]' );
	return ob_get_clean();
}, 5, 2 );

// ---------------------------------------------------------------------------
// Hide WP page title on My Account pages (theme handles its own header)
// ---------------------------------------------------------------------------
add_filter( 'woocommerce_show_page_title', '__return_false' );

// ---------------------------------------------------------------------------
// WooCommerce wrapper overrides for our theme markup
// ---------------------------------------------------------------------------
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_filter( 'woocommerce_sale_flash', '__return_empty_string' );

add_action( 'woocommerce_before_main_content', function (): void {
	$is_archive = is_shop() || is_product_category() || is_product_tag();
	$is_account = function_exists( 'is_account_page' ) && is_account_page();
	$is_single  = is_product();

	if ( $is_archive ) {
		quest_render_archive_banner();
		echo '<div class="qt-archive"><div class="qt-container"><div class="qt-archive__layout">';
		echo '<aside class="qt-archive__sidebar">';
		quest_render_category_sidebar();
		echo '</aside>';
		echo '<div class="qt-archive__content">';
	} elseif ( $is_single ) {
		echo '<main id="qt-main" class="qt-main qt-main--product">';
		echo '<div class="qt-product-breadcrumb-bar"><div class="qt-container">';
		quest_render_breadcrumb();
		echo '</div></div>';
		echo '<div class="qt-container">';
	} elseif ( $is_account ) {
		echo '<main id="qt-main" class="qt-main qt-main--account"><div class="qt-container">';
	} else {
		echo '<main id="qt-main" class="qt-main"><div class="qt-container">';
	}
}, 10 );

add_action( 'woocommerce_after_main_content', function (): void {
	$is_archive = is_shop() || is_product_category() || is_product_tag();

	if ( $is_archive ) {
		echo '</div></div></div></div>';
	} else {
		echo '</div></main>';
	}
}, 10 );

// ---------------------------------------------------------------------------
// Remove default WooCommerce sidebar
// ---------------------------------------------------------------------------
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// ---------------------------------------------------------------------------
// Products per page
// ---------------------------------------------------------------------------
add_filter( 'loop_shop_per_page', function (): int {
	return 24;
} );

add_filter( 'woocommerce_output_related_products_args', function ( array $args ): array {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
} );

// ---------------------------------------------------------------------------
// Enhanced product loop cards
// ---------------------------------------------------------------------------

// Add image overlay wrapper
add_action( 'woocommerce_before_shop_loop_item_title', function (): void {
	echo '<div class="qt-loop-overlay"><span>' . esc_html__( 'View Product', 'quest' ) . '</span></div>';
}, 11 );

// Show SKU + category after title
add_action( 'woocommerce_after_shop_loop_item_title', function (): void {
	global $product;
	$sku  = $product->get_sku();
	$cats = wp_get_post_terms( $product->get_id(), 'product_cat', [ 'fields' => 'names' ] );
	$cat  = ! is_wp_error( $cats ) && ! empty( $cats ) ? $cats[0] : '';

	if ( $sku ) {
		echo '<span class="qt-loop-sku">SKU: ' . esc_html( $sku ) . '</span>';
	}
	if ( $cat ) {
		echo '<span class="qt-loop-cat">' . esc_html( $cat ) . '</span>';
	}
}, 6 );

// ---------------------------------------------------------------------------
// Enhanced single product page
// ---------------------------------------------------------------------------

// Category badge above the title
add_action( 'woocommerce_single_product_summary', function (): void {
	global $product;
	$cats = wp_get_post_terms( $product->get_id(), 'product_cat', [ 'fields' => 'all' ] );
	if ( ! is_wp_error( $cats ) && ! empty( $cats ) ) {
		echo '<div class="qt-single-cats">';
		foreach ( $cats as $cat ) {
			$link = get_term_link( $cat );
			if ( ! is_wp_error( $link ) ) {
				echo '<a href="' . esc_url( $link ) . '" class="qt-single-cat-badge">' . esc_html( $cat->name ) . '</a>';
			}
		}
		echo '</div>';
	}
}, 3 );

// SKU highlight right after meta
add_action( 'woocommerce_single_product_summary', function (): void {
	global $product;
	if ( ! quest_is_catalog_mode() ) return;
	echo '<div class="qt-single-contact-cta">';
	echo '<p>' . esc_html__( 'Interested in this product?', 'quest' ) . '</p>';
	echo '<a href="' . esc_url( home_url( '/contact-us/' ) ) . '" class="qt-btn qt-btn--primary">';
	echo esc_html__( 'Contact Us for Pricing', 'quest' );
	echo ' ' . quest_icon( 'arrow-right', 16 );
	echo '</a></div>';
}, 20 );

// Product actions bar (share, print)
add_action( 'woocommerce_single_product_summary', function (): void {
	global $product;
	echo '<div class="qt-single-actions">';
	echo '<button type="button" class="qt-single-action" onclick="window.print()">';
	echo quest_icon( 'catalog', 16 ) . ' ' . esc_html__( 'Print', 'quest' );
	echo '</button>';
	echo '<button type="button" class="qt-single-action" onclick="navigator.clipboard.writeText(window.location.href).then(function(){alert(\'Link copied!\')})">';
	echo quest_icon( 'mail', 16 ) . ' ' . esc_html__( 'Share', 'quest' );
	echo '</button>';
	echo '</div>';
}, 35 );


// Add "View Details" CTA at the bottom
add_action( 'woocommerce_after_shop_loop_item', function (): void {
	global $product;
	echo '<div class="qt-loop-footer">';
	echo '<a href="' . esc_url( $product->get_permalink() ) . '" class="qt-loop-cta">';
	echo esc_html__( 'View Details', 'quest' );
	echo ' ' . quest_icon( 'arrow-right', 14 );
	echo '</a></div>';
}, 10 );

// ---------------------------------------------------------------------------
// Archive banner with category image background
// ---------------------------------------------------------------------------
function quest_render_archive_banner(): void {
	$title       = '';
	$description = '';
	$bg_url      = '';

	if ( is_product_category() ) {
		$term        = get_queried_object();
		$title       = $term->name;
		$description = $term->description;
		$bg_url      = quest_get_cat_image_url( $term->term_id, 'quest-hero' );
	} elseif ( is_product_tag() ) {
		$term  = get_queried_object();
		$title = sprintf( __( 'Tag: %s', 'quest' ), $term->name );
	} elseif ( is_shop() ) {
		$title = __( 'All Products', 'quest' );
	} elseif ( is_search() ) {
		$title = sprintf( __( 'Search results for "%s"', 'quest' ), get_search_query() );
	}

	$has_bg = ! empty( $bg_url );
	?>
	<div class="qt-archive-banner<?php echo $has_bg ? ' qt-archive-banner--has-bg' : ''; ?>">
		<?php if ( $has_bg ) : ?>
			<div class="qt-archive-banner__bg" style="background-image:url('<?php echo esc_url( $bg_url ); ?>')"></div>
			<div class="qt-archive-banner__overlay"></div>
		<?php endif; ?>
		<div class="qt-container qt-archive-banner__inner">
			<?php quest_render_breadcrumb(); ?>
			<h1 class="qt-archive-banner__title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $description ) : ?>
				<p class="qt-archive-banner__desc"><?php echo wp_kses_post( $description ); ?></p>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

// ---------------------------------------------------------------------------
// Category sidebar navigation
// ---------------------------------------------------------------------------
function quest_render_category_sidebar(): void {
	$current_term = is_product_category() ? get_queried_object() : null;
	$current_id   = $current_term ? $current_term->term_id : 0;

	$parent_categories = get_terms( [
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
		'exclude'    => get_option( 'default_product_cat' ),
		'orderby'    => 'menu_order',
		'order'      => 'ASC',
	] );

	if ( is_wp_error( $parent_categories ) || empty( $parent_categories ) ) {
		return;
	}

	echo '<nav class="qt-sidebar-nav" aria-label="' . esc_attr__( 'Product categories', 'quest' ) . '">';
	echo '<h3 class="qt-sidebar-nav__title">' . esc_html__( 'Categories', 'quest' ) . '</h3>';
	echo '<ul class="qt-sidebar-nav__list">';

	foreach ( $parent_categories as $cat ) {
		$link = get_term_link( $cat );
		if ( is_wp_error( $link ) ) continue;

		$is_active   = ( $current_id === $cat->term_id );
		$is_ancestor = $current_term && term_is_ancestor_of( $cat->term_id, $current_id, 'product_cat' );
		$is_open     = $is_active || $is_ancestor;

		$children = get_terms( [
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'parent'     => $cat->term_id,
			'orderby'    => 'menu_order',
			'order'      => 'ASC',
		] );
		$has_children = ! is_wp_error( $children ) && ! empty( $children );

		$classes = 'qt-sidebar-nav__item';
		if ( $is_active ) $classes .= ' qt-sidebar-nav__item--active';
		if ( $is_open )   $classes .= ' qt-sidebar-nav__item--open';

		echo '<li class="' . esc_attr( $classes ) . '">';
		echo '<a href="' . esc_url( $link ) . '" class="qt-sidebar-nav__link">';
		echo esc_html( $cat->name );
		echo '<span class="qt-sidebar-nav__count">' . esc_html( $cat->count ) . '</span>';
		echo '</a>';

		if ( $has_children ) {
			echo '<ul class="qt-sidebar-nav__sub">';
			foreach ( $children as $child ) {
				$child_link = get_term_link( $child );
				if ( is_wp_error( $child_link ) ) continue;

				$child_active = ( $current_id === $child->term_id );
				echo '<li class="' . ( $child_active ? 'qt-sidebar-nav__item--active' : '' ) . '">';
				echo '<a href="' . esc_url( $child_link ) . '" class="qt-sidebar-nav__link">';
				echo esc_html( $child->name );
				echo '<span class="qt-sidebar-nav__count">' . esc_html( $child->count ) . '</span>';
				echo '</a></li>';
			}
			echo '</ul>';
		}

		echo '</li>';
	}

	echo '</ul></nav>';
}

// ---------------------------------------------------------------------------
// Breadcrumb
// ---------------------------------------------------------------------------
function quest_render_breadcrumb(): void {
	if ( function_exists( 'woocommerce_breadcrumb' ) ) {
		woocommerce_breadcrumb( [
			'wrap_before' => '<nav class="qt-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'quest' ) . '"><ol class="qt-breadcrumb__list">',
			'wrap_after'  => '</ol></nav>',
			'before'      => '<li class="qt-breadcrumb__item">',
			'after'       => '</li>',
			'delimiter'   => '',
		] );
	}
}
