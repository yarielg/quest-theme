<?php
defined( 'ABSPATH' ) || exit;

$has_acf     = function_exists( 'get_field' );
$sister_url  = $has_acf ? get_field( 'sister_site_url', 'option' ) : '';
$sister_logo = $has_acf ? get_field( 'sister_site_logo', 'option' ) : '';
$sister_img  = $has_acf ? get_field( 'sister_site_image', 'option' ) : '';
$label       = $has_acf ? get_field( 'sister_site_label', 'option' ) : '';
$heading     = $has_acf ? get_field( 'sister_site_heading', 'option' ) : '';
$text        = $has_acf ? get_field( 'sister_site_text', 'option' ) : '';
$btn_text    = $has_acf ? get_field( 'sister_site_btn_text', 'option' ) : '';
$bg_color    = $has_acf ? get_field( 'sister_site_bg_color', 'option' ) : '';

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

<section class="qt-sister"<?php if ( $bg_color ) : ?> style="background-color: <?php echo esc_attr( $bg_color ); ?>"<?php endif; ?>>
	<div class="qt-sister__inner">
		<div class="qt-sister__content">
			<div class="qt-container">
				<span class="qt-sister__label"><?php echo esc_html( $label ); ?></span>
				<h3 class="qt-sister__heading"><?php echo esc_html( $heading ); ?></h3>
				<p class="qt-sister__text"><?php echo esc_html( $text ); ?></p>
				<div class="qt-sister__cta-row">
					<a href="<?php echo esc_url( $sister_url ); ?>" class="qt-btn qt-btn--lg qt-sister__btn" target="_blank" rel="noopener">
						<?php echo esc_html( $btn_text ); ?>
						<?php echo quest_icon( 'arrow-right', 18 ); ?>
					</a>
					<?php if ( $sister_logo ) : ?>
						<img src="<?php echo esc_url( $sister_logo['url'] ); ?>" alt="<?php echo esc_attr( $heading ); ?>" class="qt-sister__logo">
					<?php endif; ?>
				</div>
			</div>
		</div>
		<a href="<?php echo esc_url( $sister_url ); ?>" class="qt-sister__media" target="_blank" rel="noopener">
			<?php if ( $sister_img ) : ?>
				<img src="<?php echo esc_url( $sister_img['url'] ); ?>" alt="<?php echo esc_attr( $heading ); ?>" class="qt-sister__bg-img">
			<?php endif; ?>
			<div class="qt-sister__media-overlay"></div>
		</a>
	</div>
</section>
