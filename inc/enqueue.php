<?php
defined( 'ABSPATH' ) || exit;

// ---------------------------------------------------------------------------
// Frontend scripts and styles
// ---------------------------------------------------------------------------
function quest_scripts(): void {
	$ver = QUEST_VERSION;

	// Google Fonts: Barlow (industrial) + Barlow Condensed (headings)
	wp_enqueue_style(
		'quest-fonts',
		'https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&family=Barlow+Condensed:wght@600;700;800;900&display=swap',
		[],
		null
	);

	wp_enqueue_style( 'quest-base', QUEST_URL . '/assets/css/base.css', [], $ver );
	wp_enqueue_style( 'quest-header', QUEST_URL . '/assets/css/header.css', [ 'quest-base' ], $ver );

	if ( is_front_page() ) {
		wp_enqueue_style( 'quest-home', QUEST_URL . '/assets/css/home.css', [ 'quest-base' ], $ver );
	}

	wp_enqueue_style( 'quest-components', QUEST_URL . '/assets/css/components.css', [ 'quest-base' ], $ver );
	wp_enqueue_style( 'quest-footer', QUEST_URL . '/assets/css/footer.css', [ 'quest-components' ], $ver );

	if ( quest_is_woo_page() ) {
		wp_enqueue_style( 'quest-woocommerce', QUEST_URL . '/assets/css/woocommerce.css', [ 'quest-components' ], $ver );
	}

	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		wp_enqueue_style( 'quest-account', QUEST_URL . '/assets/css/account.css', [ 'quest-components' ], $ver );
	}

	if ( is_page_template( 'page-resources.php' ) || is_page() || is_singular() ) {
		wp_enqueue_style( 'quest-pages', QUEST_URL . '/assets/css/pages.css', [ 'quest-components' ], $ver );
	}

	if ( is_page_template( 'page-distributor-locator.php' ) ) {
		wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', [], '1.9.4' );
		wp_enqueue_style( 'leaflet-cluster', 'https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css', [ 'leaflet' ], '1.5.3' );
		wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], '1.9.4', true );
		wp_enqueue_script( 'leaflet-cluster', 'https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js', [ 'leaflet' ], '1.5.3', true );
		wp_enqueue_script(
			'quest-distributor-locator',
			QUEST_URL . '/assets/js/distributor-locator.js',
			[ 'leaflet', 'leaflet-cluster' ],
			$ver,
			[ 'strategy' => 'defer', 'in_footer' => true ]
		);
	}

	if ( is_page_template( 'page-resources.php' ) ) {
		wp_enqueue_script(
			'quest-pdf-preview',
			QUEST_URL . '/assets/js/pdf-preview.js',
			[],
			$ver,
			[ 'strategy' => 'defer', 'in_footer' => true ]
		);
	}

	wp_enqueue_script(
		'quest-navigation',
		QUEST_URL . '/assets/js/navigation.js',
		[],
		$ver,
		[ 'strategy' => 'defer', 'in_footer' => true ]
	);

	wp_enqueue_script(
		'quest-main',
		QUEST_URL . '/assets/js/main.js',
		[],
		$ver,
		[ 'strategy' => 'defer', 'in_footer' => true ]
	);
}
add_action( 'wp_enqueue_scripts', 'quest_scripts' );

// ---------------------------------------------------------------------------
// Disable wc-cart-fragments on non-WooCommerce pages
// ---------------------------------------------------------------------------
function quest_disable_cart_fragments(): void {
	if ( quest_is_woo_page() ) {
		return;
	}
	wp_dequeue_script( 'wc-cart-fragments' );
}
add_action( 'wp_enqueue_scripts', 'quest_disable_cart_fragments', 99 );

// ---------------------------------------------------------------------------
// Dequeue jQuery on frontend when not needed
// ---------------------------------------------------------------------------
function quest_dequeue_jquery(): void {
	if ( is_admin() ) return;
	if ( is_page_template( 'page-distributor-locator.php' ) ) return;
	if ( function_exists( 'is_account_page' ) && is_account_page() ) return;
	if ( is_cart() || is_checkout() ) return;

	wp_dequeue_script( 'jquery' );
	wp_deregister_script( 'jquery' );
	wp_dequeue_script( 'jquery-migrate' );
	wp_deregister_script( 'jquery-migrate' );
}
add_action( 'wp_enqueue_scripts', 'quest_dequeue_jquery', 99 );

// ---------------------------------------------------------------------------
// Preconnect hints + defer non-critical WooCommerce CSS
// ---------------------------------------------------------------------------
function quest_resource_hints(): void {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action( 'wp_head', 'quest_resource_hints', 1 );

function quest_defer_non_critical_styles( string $tag, string $handle ): string {
	$defer_handles = [
		'quest-fonts',
		'quest-footer',
		'quest-components',
		'quest-pages',
		'quest-woocommerce',
		'woocommerce-layout',
		'woocommerce-general',
		'wc-blocks-style',
	];

	if ( in_array( $handle, $defer_handles, true ) ) {
		$tag = str_replace( "rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $tag );
		$tag .= '<noscript>' . str_replace( "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", "rel='stylesheet'", $tag ) . '</noscript>';
	}

	return $tag;
}
add_filter( 'style_loader_tag', 'quest_defer_non_critical_styles', 10, 2 );

// ---------------------------------------------------------------------------
// Dynamic CSS variables from ACF Options
// ---------------------------------------------------------------------------
function quest_output_dynamic_css(): void {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	$overrides = [];

	$map = [
		'brand_color_primary'   => '--qt-color-primary',
		'brand_color_secondary' => '--qt-color-secondary',
		'brand_color_dark'      => '--qt-color-dark',
	];

	foreach ( $map as $acf_key => $css_var ) {
		$val = get_field( $acf_key, 'option' );
		if ( $val ) {
			$overrides[] = esc_attr( $css_var ) . ':' . esc_attr( $val );

			if ( $acf_key === 'brand_color_primary' ) {
				$r = hexdec( substr( $val, 1, 2 ) );
				$g = hexdec( substr( $val, 3, 2 ) );
				$b = hexdec( substr( $val, 5, 2 ) );
				$dark = sprintf( '#%02x%02x%02x', max( 0, $r - 40 ), max( 0, $g - 40 ), max( 0, $b - 40 ) );
				$overrides[] = '--qt-color-primary-dark:' . esc_attr( $dark );
			}
		}
	}

	if ( empty( $overrides ) ) {
		return;
	}

	wp_add_inline_style( 'quest-base', ':root{' . implode( ';', $overrides ) . '}' );
}
add_action( 'wp_enqueue_scripts', 'quest_output_dynamic_css', 20 );
