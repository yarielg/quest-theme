<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_products' ) ) {
	return;
}

$products = wc_get_products( [
	'status'  => 'publish',
	'limit'   => 4,
	'orderby' => 'date',
	'order'   => 'DESC',
] );

if ( empty( $products ) ) {
	return;
}
?>

<section class="qt-section qt-new-products">
	<div class="qt-container">
		<div class="qt-section__header">
			<h2 class="qt-section__title"><?php esc_html_e( 'Latest Products', 'quest' ); ?></h2>
			<p class="qt-section__subtitle"><?php esc_html_e( 'Recently added to our catalog', 'quest' ); ?></p>
		</div>
		<div class="qt-new-products__grid">
			<?php foreach ( $products as $product ) :
				$thumb   = $product->get_image_id();
				$img_url = $thumb ? wp_get_attachment_image_url( $thumb, 'quest-product-card' ) : wc_placeholder_img_src( 'quest-product-card' );
				$cats    = wp_get_post_terms( $product->get_id(), 'product_cat', [ 'fields' => 'names' ] );
				$cat     = ! is_wp_error( $cats ) && ! empty( $cats ) ? $cats[0] : '';
			?>
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="qt-new-product-card">
					<div class="qt-new-product-card__badge"><?php esc_html_e( 'New', 'quest' ); ?></div>
					<div class="qt-new-product-card__img">
						<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy">
					</div>
					<div class="qt-new-product-card__info">
						<?php if ( $cat ) : ?>
							<span class="qt-new-product-card__cat"><?php echo esc_html( $cat ); ?></span>
						<?php endif; ?>
						<h3 class="qt-new-product-card__name"><?php echo esc_html( $product->get_name() ); ?></h3>
						<?php if ( $product->get_sku() ) : ?>
							<span class="qt-new-product-card__sku">SKU: <?php echo esc_html( $product->get_sku() ); ?></span>
						<?php endif; ?>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
