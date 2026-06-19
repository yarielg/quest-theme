<?php
defined( 'ABSPATH' ) || exit;

$headline    = quest_option_text( 'newsletter_headline', 'Stay Connected', false );
$description = quest_option( 'newsletter_description', 'Get the latest product updates, promotions, and industry news delivered to your inbox.', false );
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
				<form class="qt-newsletter__default-form" action="#" method="post">
					<div class="qt-newsletter__input-group">
						<input type="email" name="email" placeholder="<?php esc_attr_e( 'Enter your email', 'quest' ); ?>" required>
						<button type="submit" class="qt-btn qt-btn--primary"><?php esc_html_e( 'Subscribe', 'quest' ); ?></button>
					</div>
				</form>
				<?php
			}
			?>
		</div>
	</div>
</section>
