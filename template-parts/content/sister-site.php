<?php
defined( 'ABSPATH' ) || exit;

$sister_url  = 'https://questmanufacturing.net';
$sister_logo = function_exists( 'get_field' ) ? get_field( 'sister_site_logo', 'option' ) : '';
?>

<section class="qt-sister">
	<div class="qt-container">
		<div class="qt-sister__inner">
			<div class="qt-sister__content">
				<span class="qt-sister__label"><?php esc_html_e( 'Quest Family of Brands', 'quest' ); ?></span>
				<h3 class="qt-sister__heading"><?php esc_html_e( 'Need Racks, Enclosures & Cable Management?', 'quest' ); ?></h3>
				<p class="qt-sister__text"><?php esc_html_e( 'Visit Quest Manufacturing for server racks, wall-mount enclosures, brackets, cable trays, and infrastructure solutions.', 'quest' ); ?></p>
			</div>
			<div class="qt-sister__action">
				<?php if ( $sister_logo ) : ?>
					<img src="<?php echo esc_url( $sister_logo['url'] ); ?>" alt="Quest Manufacturing" class="qt-sister__logo">
				<?php endif; ?>
				<a href="<?php echo esc_url( $sister_url ); ?>" class="qt-btn qt-btn--lg qt-sister__btn" target="_blank" rel="noopener">
					<?php esc_html_e( 'Visit QuestManufacturing.net', 'quest' ); ?>
					<?php echo quest_icon( 'arrow-right', 18 ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
