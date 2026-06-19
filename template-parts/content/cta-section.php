<?php
defined( 'ABSPATH' ) || exit;

$headline = quest_option( 'cta_headline', 'Become a Quest Distributor', false );
$body     = quest_option( 'cta_body', 'Get access to competitive pricing, dedicated support, and our full product catalog. Join our growing network of distributors and dealers.', false );
$btn_text = quest_option_text( 'cta_button_text', 'Request Access', false );
$btn_url  = function_exists( 'get_field' ) ? get_field( 'cta_button_url', 'option' ) : '';
$image    = function_exists( 'get_field' ) ? get_field( 'cta_image', 'option' ) : '';

if ( ! $btn_url ) {
	$btn_url = quest_account_url();
}
?>

<section class="qt-cta">
	<div class="qt-container qt-cta__inner">
		<div class="qt-cta__content">
			<h2 class="qt-cta__headline"><?php echo $headline; ?></h2>
			<p class="qt-cta__body"><?php echo $body; ?></p>
			<a href="<?php echo esc_url( $btn_url ); ?>" class="qt-btn qt-btn--primary qt-btn--lg">
				<?php echo esc_html( $btn_text ); ?>
				<?php echo quest_icon( 'arrow-right', 18 ); ?>
			</a>
		</div>
		<div class="qt-cta__media">
			<?php if ( $image ) : ?>
				<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>" loading="lazy">
			<?php endif; ?>
		</div>
	</div>
</section>
