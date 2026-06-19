<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_products' ) ) {
	return;
}

$section_title = quest_option_text( 'featured_title', 'Featured Products', false );

$products = wc_get_products( [
	'featured' => true,
	'status'   => 'publish',
	'limit'    => 8,
	'orderby'  => 'date',
	'order'    => 'DESC',
] );

if ( empty( $products ) ) {
	$products = wc_get_products( [
		'status'  => 'publish',
		'limit'   => 8,
		'orderby' => 'date',
		'order'   => 'DESC',
	] );
}

if ( empty( $products ) ) {
	return;
}
?>

<section class="qt-section qt-featured">
	<div class="qt-container">
		<div class="qt-section__header">
			<h2 class="qt-section__title"><?php echo esc_html( $section_title ); ?></h2>
			<p class="qt-section__subtitle"><?php esc_html_e( 'Top-selling products trusted by professionals', 'quest' ); ?></p>
		</div>
		<div class="qt-featured__grid">
			<?php foreach ( $products as $product ) :
				$thumb   = $product->get_image_id();
				$img_url = $thumb ? wp_get_attachment_image_url( $thumb, 'quest-product-card' ) : wc_placeholder_img_src( 'quest-product-card' );
				$cats    = wc_get_product_category_list( $product->get_id(), ', ' );
			?>
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="qt-product-card">
					<div class="qt-product-card__img-wrap">
						<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy">
						<?php if ( $product->is_featured() ) : ?>
							<span class="qt-product-card__badge"><?php esc_html_e( 'Featured', 'quest' ); ?></span>
						<?php endif; ?>
					</div>
					<div class="qt-product-card__info">
						<?php if ( $product->get_sku() ) : ?>
							<span class="qt-product-card__sku"><?php echo esc_html( $product->get_sku() ); ?></span>
						<?php endif; ?>
						<h3 class="qt-product-card__name"><?php echo esc_html( $product->get_name() ); ?></h3>
						<span class="qt-product-card__view">
							<?php esc_html_e( 'View Details', 'quest' ); ?>
							<?php echo quest_icon( 'arrow-right', 12 ); ?>
						</span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
		<div class="qt-section__footer">
			<a href="<?php echo esc_url( quest_shop_url() ); ?>" class="qt-btn qt-btn--outline">
				<?php esc_html_e( 'View All Products', 'quest' ); ?>
				<?php echo quest_icon( 'arrow-right', 16 ); ?>
			</a>
		</div>
	</div>
</section>
