<?php
defined( 'ABSPATH' ) || exit;

$logos = function_exists( 'get_field' ) ? get_field( 'partner_logos', 'option' ) : [];

if ( empty( $logos ) ) {
	return;
}
?>

<section class="qt-partners">
	<div class="qt-container">
		<p class="qt-partners__label"><?php esc_html_e( 'Trusted by Industry Leaders', 'quest' ); ?></p>
		<div class="qt-partners__track">
			<?php foreach ( $logos as $logo ) : ?>
				<div class="qt-partners__logo">
					<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ?? '' ); ?>" loading="lazy">
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
