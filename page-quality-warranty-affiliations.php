<?php
/**
 * Template Name: Quality, Warranty & Affiliations
 */
defined( 'ABSPATH' ) || exit;

get_header();

$page_title    = get_the_title();
$page_subtitle = function_exists( 'get_field' ) ? get_field( 'qwa_subtitle' ) : '';

// Certifications
$cert_heading = function_exists( 'get_field' ) ? ( get_field( 'qwa_cert_heading' ) ?: 'Certifications & Compliance' ) : 'Certifications & Compliance';
$cert_body    = function_exists( 'get_field' ) ? get_field( 'qwa_cert_body' ) : '';
$cert_logos   = function_exists( 'get_field' ) ? get_field( 'qwa_cert_logos' ) : [];

if ( ! $cert_body ) {
	$cert_body = 'Most Quest Technology International connectivity products meet the RoHS standard and have been tested and/or verified to be certified that hazardous substances are in compliance with EU Directive 2011/65/EU. Our products carry UL & ETL verification for networking solutions.';
}

// Warranty categories
$warranty_heading = function_exists( 'get_field' ) ? ( get_field( 'qwa_warranty_heading' ) ?: 'Warranty Coverage' ) : 'Warranty Coverage';
$warranty_intro   = function_exists( 'get_field' ) ? get_field( 'qwa_warranty_intro' ) : '';
$warranty_cats    = [];

if ( function_exists( 'have_rows' ) && have_rows( 'qwa_warranty_categories' ) ) {
	while ( have_rows( 'qwa_warranty_categories' ) ) {
		the_row();
		$warranty_cats[] = [
			'title'       => get_sub_field( 'title' ),
			'coverage'    => get_sub_field( 'coverage' ),
			'description' => get_sub_field( 'description' ),
			'icon'        => get_sub_field( 'icon' ),
		];
	}
}

if ( empty( $warranty_cats ) ) {
	$warranty_cats = [
		[ 'title' => 'Professional Termination Tools', 'coverage' => 'Lifetime', 'description' => 'Covers manufacturing and material defects. Excludes wearable parts such as blades and crimp dies.', 'icon' => 'expertise' ],
		[ 'title' => 'Structured Cabling Products', 'coverage' => 'Lifetime', 'description' => 'Full coverage for Cat5e, Cat6, and Cat6A components including patch panels, keystone jacks, and patch cables.', 'icon' => 'certified' ],
		[ 'title' => 'Active Components', 'coverage' => '1 Year', 'description' => 'Coverage for network switches, HDMI devices, testers, and active electronics.', 'icon' => 'shipping' ],
		[ 'title' => 'General Cabling & Connectivity', 'coverage' => '1 Year', 'description' => 'Standard coverage for cables, connectors, adapters, and general connectivity products.', 'icon' => 'pricing' ],
	];
}

// Affiliations
$affil_heading = function_exists( 'get_field' ) ? ( get_field( 'qwa_affil_heading' ) ?: 'Industry Affiliations' ) : 'Industry Affiliations';
$affiliations  = [];

if ( function_exists( 'have_rows' ) && have_rows( 'qwa_affiliations' ) ) {
	while ( have_rows( 'qwa_affiliations' ) ) {
		the_row();
		$affiliations[] = [
			'name'        => get_sub_field( 'name' ),
			'logo'        => get_sub_field( 'logo' ),
			'description' => get_sub_field( 'description' ),
			'url'         => get_sub_field( 'url' ),
		];
	}
}

if ( empty( $affiliations ) ) {
	$affiliations = [
		[ 'name' => 'BICSI', 'logo' => '', 'description' => 'Professional association serving the information technology systems industry with over 23,000 members spanning voice, data, safety, security, and audio-video technologies.', 'url' => '' ],
		[ 'name' => 'Genie Group', 'logo' => '', 'description' => 'Electronic distributors\' cooperative with 93 members across 300+ branch locations, representing collective buying power over $1 billion.', 'url' => '' ],
		[ 'name' => 'Edge Group', 'logo' => '', 'description' => 'Distributor-owned buying and marketing organization connecting more than 140 independent distributors in the datacom, security, and low-voltage industries.', 'url' => '' ],
		[ 'name' => 'SIA', 'logo' => '', 'description' => 'Security Industry Association with more than 750 member companies in electronic and physical security, advocating for pro-industry policies and open standards.', 'url' => '' ],
	];
}
?>

<main id="qt-main" class="qt-main qt-main--home">

	<!-- Banner -->
	<div class="qt-page-banner">
		<div class="qt-container">
			<h1 class="qt-page-banner__title"><?php echo esc_html( $page_title ); ?></h1>
			<?php if ( $page_subtitle ) : ?>
				<p class="qt-page-banner__subtitle"><?php echo esc_html( $page_subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<!-- Certifications -->
	<section class="qt-section qt-qwa-certs">
		<div class="qt-container">
			<div class="qt-qwa-certs__layout">
				<div class="qt-qwa-certs__content">
					<h2 class="qt-section__title qt-section__title--left"><?php echo esc_html( $cert_heading ); ?></h2>
					<div class="qt-qwa-certs__body"><?php echo wp_kses_post( $cert_body ); ?></div>
				</div>
				<?php if ( ! empty( $cert_logos ) ) : ?>
					<div class="qt-qwa-certs__logos">
						<?php foreach ( $cert_logos as $logo ) : ?>
							<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ?? '' ); ?>" loading="lazy">
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<!-- Warranty -->
	<section class="qt-section qt-qwa-warranty">
		<div class="qt-container">
			<div class="qt-section__header">
				<h2 class="qt-section__title"><?php echo esc_html( $warranty_heading ); ?></h2>
				<?php if ( $warranty_intro ) : ?>
					<p class="qt-section__subtitle"><?php echo esc_html( $warranty_intro ); ?></p>
				<?php endif; ?>
			</div>
			<div class="qt-qwa-warranty__grid">
				<?php foreach ( $warranty_cats as $cat ) : ?>
					<div class="qt-warranty-card">
						<div class="qt-warranty-card__icon">
							<?php echo quest_why_icon( $cat['icon'] ?? 'certified' ); ?>
						</div>
						<div class="qt-warranty-card__badge"><?php echo esc_html( $cat['coverage'] ); ?></div>
						<h3 class="qt-warranty-card__title"><?php echo esc_html( $cat['title'] ); ?></h3>
						<p class="qt-warranty-card__desc"><?php echo esc_html( $cat['description'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- Warranty Disclaimer -->
	<section class="qt-qwa-disclaimer">
		<div class="qt-container">
			<div class="qt-qwa-disclaimer__inner">
				<h4><?php esc_html_e( 'Warranty Terms', 'quest' ); ?></h4>
				<p><?php esc_html_e( 'Warranty excludes coverage for misuse, negligence, accident, improper installation, or use in violation of instructions, including but not limited to electrical irregularities, lightning, or power-line related damage. Quest Technology limits liability to repair and/or replacement of products and assumes no responsibility for consequential damages.', 'quest' ); ?></p>
			</div>
		</div>
	</section>

	<!-- Affiliations -->
	<section class="qt-section qt-qwa-affiliations">
		<div class="qt-container">
			<div class="qt-section__header">
				<h2 class="qt-section__title"><?php echo esc_html( $affil_heading ); ?></h2>
				<p class="qt-section__subtitle"><?php esc_html_e( 'Trusted partnerships with leading industry organizations', 'quest' ); ?></p>
			</div>
			<div class="qt-qwa-affil__grid">
				<?php foreach ( $affiliations as $affil ) :
					$tag   = ! empty( $affil['url'] ) ? 'a' : 'div';
					$attrs = ! empty( $affil['url'] ) ? ' href="' . esc_url( $affil['url'] ) . '" target="_blank" rel="noopener"' : '';
				?>
					<<?php echo $tag; ?> class="qt-affil-card"<?php echo $attrs; ?>>
						<?php if ( ! empty( $affil['logo'] ) ) : ?>
							<div class="qt-affil-card__logo">
								<img src="<?php echo esc_url( $affil['logo']['url'] ); ?>" alt="<?php echo esc_attr( $affil['name'] ); ?>" loading="lazy">
							</div>
						<?php else : ?>
							<div class="qt-affil-card__logo qt-affil-card__logo--text">
								<span><?php echo esc_html( $affil['name'] ); ?></span>
							</div>
						<?php endif; ?>
						<div class="qt-affil-card__info">
							<h3 class="qt-affil-card__name"><?php echo esc_html( $affil['name'] ); ?></h3>
							<p class="qt-affil-card__desc"><?php echo esc_html( $affil['description'] ); ?></p>
						</div>
					</<?php echo $tag; ?>>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- CTA -->
	<?php get_template_part( 'template-parts/content/cta-section' ); ?>

</main>

<?php
get_footer();
