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
		[
			'title'       => 'Industry Expertise',
			'description' => '40+ years of experience in structured cabling and connectivity solutions for enterprise environments.',
			'icon'        => 'expertise',
		],
		[
			'title'       => 'Quality Certified',
			'description' => 'All products meet or exceed industry standards with UL listings, ETL verifications, and compliance certifications.',
			'icon'        => 'certified',
		],
		[
			'title'       => 'Dealer Pricing',
			'description' => 'Competitive tiered pricing for authorized dealers and distributors with volume discounts.',
			'icon'        => 'pricing',
		],
		[
			'title'       => 'In-Stock & Ready',
			'description' => 'Extensive inventory with same-day shipping on thousands of SKUs from our distribution center.',
			'icon'        => 'shipping',
		],
	];
}

$headline = quest_option_text( 'why_quest_headline', 'Why Choose Quest Technology', false );
?>

<section class="qt-section qt-why-quest">
	<div class="qt-container">
		<div class="qt-section__header">
			<h2 class="qt-section__title"><?php echo esc_html( $headline ); ?></h2>
		</div>
		<div class="qt-why-quest__grid">
			<?php foreach ( $features as $feature ) : ?>
				<div class="qt-why-quest__card">
					<div class="qt-why-quest__icon">
						<?php echo quest_why_icon( $feature['icon'] ?? 'expertise' ); ?>
					</div>
					<h3 class="qt-why-quest__title"><?php echo esc_html( $feature['title'] ); ?></h3>
					<p class="qt-why-quest__desc"><?php echo esc_html( $feature['description'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
