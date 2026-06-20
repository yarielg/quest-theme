<?php
defined( 'ABSPATH' ) || exit;

$items = $args['items'] ?? [];
?>

<div class="quest-quote-page">
	<h2 class="qt-account-section__title"><?php esc_html_e( 'My Quote', 'quest-asap' ); ?></h2>

	<?php if ( empty( $items ) ) : ?>
		<div class="quest-quote-empty">
			<p><?php esc_html_e( 'Your quote is empty. Browse our products to add items.', 'quest-asap' ); ?></p>
			<a href="<?php echo esc_url( quest_shop_url() ); ?>" class="qt-btn qt-btn--primary">
				<?php esc_html_e( 'Browse Products', 'quest-asap' ); ?>
			</a>
		</div>
	<?php else : ?>
		<div class="quest-quote-list">
			<div class="quest-quote-header">
				<span class="quest-quote-col quest-quote-col--product"><?php esc_html_e( 'Product', 'quest-asap' ); ?></span>
				<span class="quest-quote-col quest-quote-col--sku"><?php esc_html_e( 'SKU', 'quest-asap' ); ?></span>
				<span class="quest-quote-col quest-quote-col--qty"><?php esc_html_e( 'Qty', 'quest-asap' ); ?></span>
				<span class="quest-quote-col quest-quote-col--actions"></span>
			</div>

			<?php foreach ( $items as $item ) :
				$product = wc_get_product( $item['product_id'] );
				if ( ! $product ) continue;
				$thumb = $product->get_image_id()
					? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' )
					: wc_placeholder_img_src( 'thumbnail' );
			?>
				<div class="quest-quote-item" data-product-id="<?php echo esc_attr( $item['product_id'] ); ?>">
					<div class="quest-quote-col quest-quote-col--product">
						<img src="<?php echo esc_url( $thumb ); ?>" alt="" class="quest-quote-item__img">
						<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="quest-quote-item__name">
							<?php echo esc_html( $product->get_name() ); ?>
						</a>
					</div>
					<div class="quest-quote-col quest-quote-col--sku">
						<span class="quest-quote-item__sku"><?php echo esc_html( $product->get_sku() ); ?></span>
					</div>
					<div class="quest-quote-col quest-quote-col--qty">
						<input type="number" min="1" value="<?php echo esc_attr( $item['quantity'] ); ?>"
							class="quest-quote-qty-input" data-product-id="<?php echo esc_attr( $item['product_id'] ); ?>">
					</div>
					<div class="quest-quote-col quest-quote-col--actions">
						<button type="button" class="quest-remove-from-quote" data-product-id="<?php echo esc_attr( $item['product_id'] ); ?>" aria-label="<?php esc_attr_e( 'Remove', 'quest-asap' ); ?>">
							&times;
						</button>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="quest-quote-footer">
			<div class="quest-quote-notes">
				<label for="quest-quote-notes"><?php esc_html_e( 'Notes (optional)', 'quest-asap' ); ?></label>
				<textarea id="quest-quote-notes" rows="3" placeholder="<?php esc_attr_e( 'Special requirements, preferred quantities, delivery timeline...', 'quest-asap' ); ?>"></textarea>
			</div>
			<div class="quest-quote-submit">
				<p class="quest-quote-submit__info"><?php esc_html_e( 'Our team will review your quote and contact you with pricing within 1-2 business days.', 'quest-asap' ); ?></p>
				<button type="button" id="quest-submit-quote" class="qt-btn qt-btn--primary qt-btn--lg">
					<?php esc_html_e( 'Submit Quote Request', 'quest-asap' ); ?>
					<?php echo quest_icon( 'arrow-right', 18 ); ?>
				</button>
			</div>
		</div>
	<?php endif; ?>
</div>
