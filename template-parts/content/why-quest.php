<?php
defined( 'ABSPATH' ) || exit;

$features = [];

if ( function_exists( 'have_rows' ) && have_rows( 'why_quest_features', 'option' ) ) {
	while ( have_rows( 'why_quest_features', 'option' ) ) {
		the_row();
		$features[] = [
			'title'       => get_sub_field( 'title' ),
			'description' => get_sub_field( 'description' ),
			'icon'        => get_sub_field( 'icon' ),
		];
	}
}

if ( empty( $features ) ) {
	$features = [
		[ 'title' => 'Industry Expertise',  'description' => '40+ years of experience in structured cabling and connectivity solutions for enterprise environments.', 'icon' => 'expertise' ],
		[ 'title' => 'Quality Certified',    'description' => 'All products meet or exceed industry standards with UL listings, ETL verifications, and compliance certifications.', 'icon' => 'certified' ],
		[ 'title' => 'Dealer Pricing',       'description' => 'Competitive tiered pricing for authorized dealers and distributors with volume discounts.', 'icon' => 'pricing' ],
		[ 'title' => 'In-Stock & Ready',     'description' => 'Extensive inventory with same-day shipping on thousands of SKUs from our distribution center.', 'icon' => 'shipping' ],
	];
}

$headline = quest_option_text( 'why_quest_headline', 'Why Choose Quest Technology', false );

$stats = [];
if ( function_exists( 'get_field' ) && get_field( 'stats_items', 'option' ) ) {
	$stats = get_field( 'stats_items', 'option' );
}
if ( empty( $stats ) ) {
	$stats = [
		[ 'stat_number' => '40+',     'stat_label' => 'Years in Business' ],
		[ 'stat_number' => '10,000+', 'stat_label' => 'Products' ],
		[ 'stat_number' => '50+',     'stat_label' => 'States Served' ],
		[ 'stat_number' => '1,000+',  'stat_label' => 'Distributors' ],
	];
}
?>

<section class="qt-why">
	<div class="qt-container">

		<!-- Stats row -->
		<div class="qt-why__stats">
			<?php foreach ( $stats as $item ) : ?>
				<div class="qt-why__stat">
					<span class="qt-why__stat-number"><?php echo esc_html( $item['stat_number'] ); ?></span>
					<span class="qt-why__stat-label"><?php echo esc_html( $item['stat_label'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Heading -->
		<div class="qt-section__header">
			<h2 class="qt-why__title"><?php echo esc_html( $headline ); ?></h2>
		</div>

		<!-- Feature cards -->
		<div class="qt-why__grid">
			<?php foreach ( $features as $i => $feature ) : ?>
				<div class="qt-why__card">
					<span class="qt-why__card-number"><?php echo esc_html( str_pad( $i + 1, 2, '0', STR_PAD_LEFT ) ); ?></span>
					<div class="qt-why__card-icon">
						<?php echo quest_why_icon( $feature['icon'] ?? 'expertise' ); ?>
					</div>
					<h3 class="qt-why__card-title"><?php echo esc_html( $feature['title'] ); ?></h3>
					<p class="qt-why__card-desc"><?php echo esc_html( $feature['description'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
</section>
