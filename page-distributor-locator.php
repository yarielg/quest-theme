<?php
/**
 * Template Name: Distributor Locator
 */
defined( 'ABSPATH' ) || exit;

get_header();

$stores = get_posts( [
	'post_type'      => 'wpsl_stores',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
] );

$store_data = [];
$states     = [];
$countries  = [];

foreach ( $stores as $store ) {
	$lat = get_post_meta( $store->ID, 'wpsl_lat', true );
	$lng = get_post_meta( $store->ID, 'wpsl_lng', true );
	if ( ! $lat || ! $lng ) continue;

	$state   = get_post_meta( $store->ID, 'wpsl_state', true );
	$country = get_post_meta( $store->ID, 'wpsl_country', true );

	if ( $state && ! in_array( $state, $states, true ) ) $states[] = $state;
	if ( $country && ! in_array( $country, $countries, true ) ) $countries[] = $country;

	$store_data[] = [
		'id'      => $store->ID,
		'name'    => $store->post_title,
		'address' => get_post_meta( $store->ID, 'wpsl_address', true ),
		'city'    => get_post_meta( $store->ID, 'wpsl_city', true ),
		'state'   => $state,
		'country' => $country,
		'zip'     => get_post_meta( $store->ID, 'wpsl_zip', true ),
		'phone'   => get_post_meta( $store->ID, 'wpsl_phone', true ),
		'email'   => get_post_meta( $store->ID, 'wpsl_email', true ),
		'url'     => get_post_meta( $store->ID, 'wpsl_url', true ),
		'lat'     => floatval( $lat ),
		'lng'     => floatval( $lng ),
	];
}

sort( $states );
sort( $countries );
?>

<main id="qt-main" class="qt-main qt-main--home">

	<div class="qt-page-banner">
		<div class="qt-container">
			<h1 class="qt-page-banner__title"><?php esc_html_e( 'Find a Distributor', 'quest' ); ?></h1>
			<p class="qt-page-banner__subtitle"><?php esc_html_e( 'Locate Quest Technology authorized distributors and dealers near you.', 'quest' ); ?></p>
		</div>
	</div>

	<div class="qt-section">
		<div class="qt-container">
			<div class="qt-locator" id="qt-locator">

				<!-- Search -->
				<div class="qt-locator__search">
					<div class="qt-locator__search-row">
						<div class="qt-locator__input-wrap">
							<?php echo quest_icon( 'search', 18 ); ?>
							<input type="text" id="qt-loc-input" placeholder="<?php esc_attr_e( 'Search by city, state, zip code, or country...', 'quest' ); ?>" autocomplete="off">
						</div>
						<select id="qt-loc-radius" class="qt-locator__radius">
							<option value="25">25 mi</option>
							<option value="50" selected>50 mi</option>
							<option value="100">100 mi</option>
							<option value="250">250 mi</option>
							<option value="500">500 mi</option>
							<option value="0"><?php esc_html_e( 'Show All', 'quest' ); ?></option>
						</select>
						<button type="button" id="qt-loc-search" class="qt-btn qt-btn--primary">
							<?php esc_html_e( 'Search', 'quest' ); ?>
						</button>
					</div>
					<div class="qt-locator__status" id="qt-loc-status"></div>
				</div>

				<!-- Mobile toggle -->
				<div class="qt-locator__toggle" id="qt-loc-toggle">
					<button type="button" class="qt-locator__toggle-btn qt-locator__toggle-btn--active" data-view="map"><?php esc_html_e( 'Map', 'quest' ); ?></button>
					<button type="button" class="qt-locator__toggle-btn" data-view="list"><?php esc_html_e( 'List', 'quest' ); ?></button>
				</div>

				<!-- Map + List split -->
				<div class="qt-locator__body">
					<div class="qt-locator__map-wrap">
						<div class="qt-locator__map" id="qt-loc-map"></div>
					</div>
					<div class="qt-locator__list" id="qt-loc-list">
						<div class="qt-locator__list-inner" id="qt-loc-list-inner"></div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<?php get_template_part( 'template-parts/content/cta-section' ); ?>

</main>

<script type="application/json" id="qt-locator-config"><?php echo wp_json_encode( [
	'stores'  => $store_data,
	'center'  => [ 'lat' => 25.840653, 'lng' => -80.32644 ],
	'zoom'    => 4,
	'total'   => count( $store_data ),
] ); ?></script>

<?php
get_footer();
