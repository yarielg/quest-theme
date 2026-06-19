<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_products' ) ) {
	return;
}

$section_title    = quest_option_text( 'product_tabs_title', 'Shop by Category', false );
$section_subtitle = quest_option_text( 'product_tabs_subtitle', 'Browse our most popular product lines', false );

$selected_ids = function_exists( 'get_field' ) ? get_field( 'product_tabs_categories', 'option' ) : null;

if ( ! empty( $selected_ids ) && is_array( $selected_ids ) ) {
	$tab_categories = get_terms( [
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'include'    => $selected_ids,
		'orderby'    => 'include',
	] );
} else {
	$tab_categories = get_terms( [
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
		'exclude'    => get_option( 'default_product_cat' ),
		'number'     => 6,
		'orderby'    => 'count',
		'order'      => 'DESC',
	] );
}

if ( is_wp_error( $tab_categories ) || empty( $tab_categories ) ) {
	return;
}

$tabs_data = [];
foreach ( $tab_categories as $cat ) {
	$products = wc_get_products( [
		'category' => [ $cat->slug ],
		'status'   => 'publish',
		'limit'    => 6,
		'orderby'  => 'date',
		'order'    => 'DESC',
	] );
	if ( ! empty( $products ) ) {
		$tabs_data[] = [
			'term'     => $cat,
			'products' => $products,
		];
	}
}

if ( empty( $tabs_data ) ) {
	return;
}
?>

<section class="qt-section qt-product-tabs">
	<div class="qt-container">
		<div class="qt-section__header">
			<h2 class="qt-section__title"><?php echo esc_html( $section_title ); ?></h2>
			<?php if ( $section_subtitle ) : ?>
				<p class="qt-section__subtitle"><?php echo esc_html( $section_subtitle ); ?></p>
			<?php endif; ?>
		</div>

		<div class="qt-tabs" data-component="tabs">
			<div class="qt-tabs__nav" role="tablist">
				<?php foreach ( $tabs_data as $i => $tab ) : ?>
					<button
						type="button"
						role="tab"
						class="qt-tabs__btn<?php echo $i === 0 ? ' qt-tabs__btn--active' : ''; ?>"
						aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
						aria-controls="qt-tab-panel-<?php echo esc_attr( $tab['term']->term_id ); ?>"
						data-tab="<?php echo esc_attr( $tab['term']->term_id ); ?>"
					>
						<?php echo esc_html( $tab['term']->name ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<?php foreach ( $tabs_data as $i => $tab ) :
				$cat_link = get_term_link( $tab['term'] );
			?>
				<div
					id="qt-tab-panel-<?php echo esc_attr( $tab['term']->term_id ); ?>"
					role="tabpanel"
					class="qt-tabs__panel<?php echo $i === 0 ? ' qt-tabs__panel--active' : ''; ?>"
					data-tab-panel="<?php echo esc_attr( $tab['term']->term_id ); ?>"
					<?php echo $i !== 0 ? 'hidden' : ''; ?>
				>
					<div class="qt-tabs__carousel" data-component="carousel">
						<div class="qt-tabs__carousel-track">
							<?php foreach ( $tab['products'] as $product ) :
								$thumb   = $product->get_image_id();
								$img_url = $thumb ? wp_get_attachment_image_url( $thumb, 'quest-product-card' ) : wc_placeholder_img_src( 'quest-product-card' );
							?>
								<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="qt-product-card qt-tabs__card">
									<div class="qt-product-card__img-wrap">
										<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy">
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

						<?php if ( count( $tab['products'] ) > 3 ) : ?>
							<button type="button" class="qt-tabs__carousel-btn qt-tabs__carousel-btn--prev" aria-label="<?php esc_attr_e( 'Previous', 'quest' ); ?>" data-dir="prev">
								<?php echo quest_icon( 'chevron-right', 20 ); ?>
							</button>
							<button type="button" class="qt-tabs__carousel-btn qt-tabs__carousel-btn--next" aria-label="<?php esc_attr_e( 'Next', 'quest' ); ?>" data-dir="next">
								<?php echo quest_icon( 'chevron-right', 20 ); ?>
							</button>
						<?php endif; ?>
					</div>

					<?php if ( ! is_wp_error( $cat_link ) ) : ?>
						<div class="qt-tabs__panel-footer">
							<a href="<?php echo esc_url( $cat_link ); ?>" class="qt-btn qt-btn--outline qt-btn--sm">
								<?php printf( esc_html__( 'View All %s', 'quest' ), esc_html( $tab['term']->name ) ); ?>
								<?php echo quest_icon( 'arrow-right', 14 ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
