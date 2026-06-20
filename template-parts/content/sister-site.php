<?php
defined( 'ABSPATH' ) || exit;

$has_acf     = function_exists( 'get_field' );
$sister_url  = $has_acf ? get_field( 'sister_site_url', 'option' ) : '';
$sister_logo = $has_acf ? get_field( 'sister_site_logo', 'option' ) : '';
$label       = $has_acf ? get_field( 'sister_site_label', 'option' ) : '';
$heading     = $has_acf ? get_field( 'sister_site_heading', 'option' ) : '';
$text        = $has_acf ? get_field( 'sister_site_text', 'option' ) : '';
$btn_text    = $has_acf ? get_field( 'sister_site_btn_text', 'option' ) : '';

if ( ! $sister_url ) {
	$sister_url = 'https://questmanufacturing.net';
}
if ( ! $label ) {
	$label = 'Quest Family of Brands';
}
if ( ! $heading ) {
	$heading = 'Need Racks, Enclosures & Cable Management?';
}
if ( ! $text ) {
	$text = 'Visit Quest Manufacturing for server racks, wall-mount enclosures, brackets, cable trays, and infrastructure solutions.';
}
if ( ! $btn_text ) {
	$btn_text = 'Visit Site';
}
?>

<section class="qt-sister">
	<div class="qt-container">
		<div class="qt-sister__inner">
			<div class="qt-sister__content">
				<span class="qt-sister__label"><?php echo esc_html( $label ); ?></span>
				<h3 class="qt-sister__heading"><?php echo esc_html( $heading ); ?></h3>
				<p class="qt-sister__text"><?php echo esc_html( $text ); ?></p>
				<a href="<?php echo esc_url( $sister_url ); ?>" class="qt-btn qt-btn--lg qt-sister__btn" target="_blank" rel="noopener">
					<?php echo esc_html( $btn_text ); ?>
					<?php echo quest_icon( 'arrow-right', 18 ); ?>
				</a>
			</div>
			<div class="qt-sister__action">
				<?php if ( $sister_logo ) : ?>
					<img src="<?php echo esc_url( $sister_logo['url'] ); ?>" alt="<?php echo esc_attr( $heading ); ?>" class="qt-sister__logo">
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
