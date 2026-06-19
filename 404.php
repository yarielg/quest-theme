<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="qt-main" class="qt-main">
	<div class="qt-container">
		<div class="qt-error-page">
			<h1 class="qt-error-page__code">404</h1>
			<h2 class="qt-error-page__title"><?php esc_html_e( 'Page Not Found', 'quest' ); ?></h2>
			<p class="qt-error-page__message"><?php esc_html_e( 'The page you\'re looking for doesn\'t exist or has been moved.', 'quest' ); ?></p>
			<div class="qt-error-page__actions">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="qt-btn qt-btn--primary">
					<?php esc_html_e( 'Back to Home', 'quest' ); ?>
				</a>
				<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="qt-btn qt-btn--outline">
						<?php esc_html_e( 'Browse Products', 'quest' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
