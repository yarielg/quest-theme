<?php
defined( 'ABSPATH' ) || exit;

$headline    = quest_option_text( 'newsletter_headline', 'Stay Connected', false );
$description = quest_option( 'newsletter_description', 'Get the latest product updates, promotions, and industry news delivered to your inbox.', false );
$turnstile_key = function_exists( 'get_field' ) ? get_field( 'turnstile_site_key', 'option' ) : '';
?>

<section class="qt-newsletter">
	<div class="qt-container qt-newsletter__inner">
		<div class="qt-newsletter__content">
			<h2 class="qt-newsletter__headline"><?php echo esc_html( $headline ); ?></h2>
			<p class="qt-newsletter__description"><?php echo $description; ?></p>
		</div>
		<div class="qt-newsletter__form">
			<?php
			$shortcode = quest_option( 'newsletter_shortcode', '', false );
			if ( $shortcode ) {
				echo do_shortcode( $shortcode );
			} else {
				?>
				<form class="qt-newsletter__default-form" id="qt-newsletter-form" method="post">
					<div class="qt-newsletter__input-group">
						<input type="email" name="newsletter_email" placeholder="<?php esc_attr_e( 'Enter your email', 'quest' ); ?>" required>
						<button type="submit" class="qt-btn qt-btn--primary"><?php esc_html_e( 'Subscribe', 'quest' ); ?></button>
					</div>
					<?php if ( $turnstile_key ) : ?>
						<div class="qt-newsletter__turnstile">
							<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( $turnstile_key ); ?>" data-theme="dark" data-size="normal"></div>
						</div>
					<?php endif; ?>
					<!-- Honeypot -->
					<div style="position:absolute;left:-9999px;" aria-hidden="true">
						<input type="text" name="newsletter_hp" tabindex="-1" autocomplete="off">
					</div>
					<?php wp_nonce_field( 'quest_newsletter', 'quest_newsletter_nonce' ); ?>
				</form>
				<div class="qt-newsletter__result" id="qt-newsletter-result" hidden></div>
				<?php
			}
			?>
		</div>
	</div>
</section>
