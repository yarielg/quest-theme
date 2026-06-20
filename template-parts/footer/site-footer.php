<?php
defined( 'ABSPATH' ) || exit;

$phone   = quest_option_text( 'company_phone', '(800) 555-0199', false );
$email   = quest_option_text( 'company_email', 'info@questtechnologyintl.com', false );
$address = quest_option( 'company_address', '', false );

$col1_heading = quest_option_text( 'footer_col1_heading', 'Products', false );
$col2_heading = quest_option_text( 'footer_col2_heading', 'Company', false );
$col3_heading = quest_option_text( 'footer_col3_heading', 'Support', false );

$tagline   = quest_option_text( 'footer_tagline', '', false );
$copyright = quest_option_text( 'footer_copyright', '', false );

$social = function_exists( 'get_field' ) ? get_field( 'social_links', 'option' ) : [];
?>

<footer id="qt-footer" class="qt-footer" role="contentinfo">

	<div class="qt-footer__main">
		<div class="qt-container qt-footer__grid">

			<div class="qt-footer__brand">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<span class="qt-footer__site-name"><?php bloginfo( 'name' ); ?></span>
				<?php endif; ?>
				<p class="qt-footer__tagline"><?php echo $tagline ?: get_bloginfo( 'description' ); ?></p>
				<?php if ( ! empty( $social ) && is_array( $social ) ) : ?>
					<div class="qt-footer__social">
						<?php if ( ! empty( $social['facebook'] ) ) : ?>
							<a href="<?php echo esc_url( $social['facebook'] ); ?>" class="qt-footer__social-link" aria-label="Facebook" target="_blank" rel="noopener"><?php echo quest_icon( 'facebook', 18 ); ?></a>
						<?php endif; ?>
						<?php if ( ! empty( $social['linkedin'] ) ) : ?>
							<a href="<?php echo esc_url( $social['linkedin'] ); ?>" class="qt-footer__social-link" aria-label="LinkedIn" target="_blank" rel="noopener"><?php echo quest_icon( 'linkedin', 18 ); ?></a>
						<?php endif; ?>
						<?php if ( ! empty( $social['youtube'] ) ) : ?>
							<a href="<?php echo esc_url( $social['youtube'] ); ?>" class="qt-footer__social-link" aria-label="YouTube" target="_blank" rel="noopener"><?php echo quest_icon( 'youtube', 18 ); ?></a>
						<?php endif; ?>
						<?php if ( ! empty( $social['instagram'] ) ) : ?>
							<a href="<?php echo esc_url( $social['instagram'] ); ?>" class="qt-footer__social-link" aria-label="Instagram" target="_blank" rel="noopener"><?php echo quest_icon( 'instagram', 18 ); ?></a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( has_nav_menu( 'footer_1' ) ) : ?>
				<div class="qt-footer__col">
					<h4 class="qt-footer__heading"><?php echo esc_html( $col1_heading ); ?></h4>
					<?php wp_nav_menu( [
						'theme_location' => 'footer_1',
						'menu_class'     => 'qt-footer__links',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					] ); ?>
				</div>
			<?php endif; ?>

			<div class="qt-footer__col">
				<?php if ( has_nav_menu( 'footer_2' ) ) : ?>
					<h4 class="qt-footer__heading"><?php echo esc_html( $col2_heading ); ?></h4>
					<?php wp_nav_menu( [
						'theme_location' => 'footer_2',
						'menu_class'     => 'qt-footer__links',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					] ); ?>
				<?php endif; ?>

				<?php if ( has_nav_menu( 'footer_3' ) ) : ?>
					<h4 class="qt-footer__heading qt-footer__heading--contact"><?php echo esc_html( $col3_heading ); ?></h4>
					<?php wp_nav_menu( [
						'theme_location' => 'footer_3',
						'menu_class'     => 'qt-footer__links',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					] ); ?>
				<?php endif; ?>
			</div>

			<div class="qt-footer__col">
				<h4 class="qt-footer__heading"><?php esc_html_e( 'Contact', 'quest' ); ?></h4>
				<ul class="qt-footer__contact-list">
					<?php if ( $address ) : ?>
						<li>
							<span class="qt-footer__contact-icon"><?php echo quest_icon( 'home', 16 ); ?></span>
							<span><?php echo nl2br( esc_html( $address ) ); ?></span>
						</li>
					<?php endif; ?>
					<?php if ( $phone ) : ?>
						<li>
							<span class="qt-footer__contact-icon"><?php echo quest_icon( 'phone', 16 ); ?></span>
							<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
						</li>
					<?php endif; ?>
					<?php if ( $email ) : ?>
						<li>
							<span class="qt-footer__contact-icon"><?php echo quest_icon( 'mail', 16 ); ?></span>
							<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
						</li>
					<?php endif; ?>
				</ul>
			</div>

		</div>
	</div>

	<div class="qt-footer__bottom">
		<div class="qt-container qt-footer__bottom-inner">
			<?php if ( $copyright ) : ?>
				<p><?php echo esc_html( str_replace( [ '{year}', '{name}' ], [ date( 'Y' ), get_bloginfo( 'name' ) ], $copyright ) ); ?></p>
			<?php else : ?>
				<p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'quest' ); ?></p>
			<?php endif; ?>
			<p class="qt-footer__credit"><?php esc_html_e( 'Designed by', 'quest' ); ?> <a href="https://webreadynow.com" target="_blank" rel="noopener">WebReadyNow</a></p>
		</div>
	</div>

</footer>
