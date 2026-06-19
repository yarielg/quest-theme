<?php
defined( 'ABSPATH' ) || exit;

// ---------------------------------------------------------------------------
// Theme setup
// ---------------------------------------------------------------------------
function quest_setup(): void {
	load_theme_textdomain( 'quest', QUEST_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', [
		'height'      => 80,
		'width'       => 280,
		'flex-height' => true,
		'flex-width'  => true,
	] );
	add_theme_support( 'html5', [
		'search-form', 'comment-form', 'comment-list',
		'gallery', 'caption', 'style', 'script',
	] );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );

	add_image_size( 'quest-product-card', 600, 600, true );
	add_image_size( 'quest-product-single', 900, 900, false );
	add_image_size( 'quest-category', 480, 360, true );
	add_image_size( 'quest-hero', 1920, 900, true );

	// WooCommerce
	add_theme_support( 'woocommerce', [
		'thumbnail_image_width' => 600,
		'single_image_width'    => 900,
		'product_grid'          => [
			'default_rows'    => 4,
			'min_rows'        => 2,
			'max_rows'        => 8,
			'default_columns' => 4,
			'min_columns'     => 2,
			'max_columns'     => 5,
		],
	] );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( [
		'primary'  => __( 'Primary Navigation', 'quest' ),
		'mobile'   => __( 'Mobile Navigation', 'quest' ),
		'footer_1' => __( 'Footer: Products', 'quest' ),
		'footer_2' => __( 'Footer: Company', 'quest' ),
		'footer_3' => __( 'Footer: Support', 'quest' ),
	] );
}
add_action( 'after_setup_theme', 'quest_setup' );

// ---------------------------------------------------------------------------
// Content width
// ---------------------------------------------------------------------------
function quest_content_width(): void {
	$GLOBALS['content_width'] = 1280;
}
add_action( 'after_setup_theme', 'quest_content_width', 0 );

// ---------------------------------------------------------------------------
// Widget areas
// ---------------------------------------------------------------------------
function quest_widgets_init(): void {
	register_sidebar( [
		'name'          => __( 'Footer Column 1', 'quest' ),
		'id'            => 'footer-1',
		'before_widget' => '<div id="%1$s" class="qt-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="qt-widget__title">',
		'after_title'   => '</h4>',
	] );

	register_sidebar( [
		'name'          => __( 'Footer Column 2', 'quest' ),
		'id'            => 'footer-2',
		'before_widget' => '<div id="%1$s" class="qt-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="qt-widget__title">',
		'after_title'   => '</h4>',
	] );

	register_sidebar( [
		'name'          => __( 'Footer Column 3', 'quest' ),
		'id'            => 'footer-3',
		'before_widget' => '<div id="%1$s" class="qt-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="qt-widget__title">',
		'after_title'   => '</h4>',
	] );

	register_sidebar( [
		'name'          => __( 'Shop Sidebar', 'quest' ),
		'id'            => 'shop-sidebar',
		'description'   => __( 'Filters and widgets for product archive pages.', 'quest' ),
		'before_widget' => '<div id="%1$s" class="qt-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="qt-widget__title">',
		'after_title'   => '</h4>',
	] );
}
add_action( 'widgets_init', 'quest_widgets_init' );

// ---------------------------------------------------------------------------
// Body classes
// ---------------------------------------------------------------------------
function quest_body_classes( array $classes ): array {
	if ( quest_is_woo_page() ) {
		$classes[] = 'qt-woo-page';
	}
	if ( is_front_page() ) {
		$classes[] = 'qt-home';
	}
	return $classes;
}
add_filter( 'body_class', 'quest_body_classes' );

// ---------------------------------------------------------------------------
// Add .js class early so CSS can target JS-available state without FOUC
// ---------------------------------------------------------------------------
add_action( 'wp_head', function (): void {
	echo '<script>document.documentElement.classList.add("js")</script>' . "\n";
}, 0 );

// ---------------------------------------------------------------------------
// Preconnect for Google Fonts
// ---------------------------------------------------------------------------
function quest_preconnect(): void {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	echo '<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&family=Barlow+Condensed:wght@600;700;800;900&display=swap">' . "\n";
}
add_action( 'wp_head', 'quest_preconnect', 1 );

// ---------------------------------------------------------------------------
// Declare WooCommerce HPOS compatibility
// ---------------------------------------------------------------------------
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_order_tables',
			__FILE__,
			true
		);
	}
} );

// ---------------------------------------------------------------------------
// Flush rewrite rules on theme switch
// ---------------------------------------------------------------------------
add_action( 'after_switch_theme', 'flush_rewrite_rules' );
