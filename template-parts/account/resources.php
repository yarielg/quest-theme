<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="qt-account-section">
	<h2 class="qt-account-section__title"><?php esc_html_e( 'Resources', 'quest' ); ?></h2>

	<div class="qt-resources__grid">
		<a href="<?php echo esc_url( home_url( '/marketing-resources/' ) ); ?>" class="qt-dashboard__card">
			<div class="qt-dashboard__card-icon"><?php echo quest_icon( 'catalog', 28 ); ?></div>
			<h3><?php esc_html_e( 'Marketing Resources', 'quest' ); ?></h3>
			<p><?php esc_html_e( 'Download brochures, product sheets, and marketing collateral.', 'quest' ); ?></p>
		</a>

		<a href="<?php echo esc_url( quest_shop_url() ); ?>" class="qt-dashboard__card">
			<div class="qt-dashboard__card-icon"><?php echo quest_icon( 'grid', 28 ); ?></div>
			<h3><?php esc_html_e( 'Product Catalog', 'quest' ); ?></h3>
			<p><?php esc_html_e( 'Browse our complete product catalog with specifications.', 'quest' ); ?></p>
		</a>

		<a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" class="qt-dashboard__card">
			<div class="qt-dashboard__card-icon"><?php echo quest_icon( 'phone', 28 ); ?></div>
			<h3><?php esc_html_e( 'Contact Support', 'quest' ); ?></h3>
			<p><?php esc_html_e( 'Reach out to our support team for product or account assistance.', 'quest' ); ?></p>
		</a>
	</div>
</div>
