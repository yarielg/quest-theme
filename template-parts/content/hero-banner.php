<?php
defined( 'ABSPATH' ) || exit;

$slides = [];

if ( function_exists( 'have_rows' ) && have_rows( 'hero_slides', 'option' ) ) {
	while ( have_rows( 'hero_slides', 'option' ) ) {
		the_row();
		$slides[] = [
			'label'           => get_sub_field( 'label' ),
			'headline'        => get_sub_field( 'headline' ),
			'body'            => get_sub_field( 'body' ),
			'cta_text'        => get_sub_field( 'cta_text' ),
			'cta_url'         => get_sub_field( 'cta_url' ),
			'cta_style'       => get_sub_field( 'cta_style' ) ?: 'primary',
			'bg_image'        => get_sub_field( 'bg_image' ),
			'bg_color'        => get_sub_field( 'bg_color' ),
			'overlay_opacity' => get_sub_field( 'overlay_opacity' ),
			'bg_position'     => get_sub_field( 'bg_position' ),
		];
	}
}

if ( empty( $slides ) ) {
	$slides[] = [
		'label'           => 'HIGH PERFORMANCE',
		'headline'        => 'Built to Connect.',
		'body'            => 'Enterprise-grade cabling, connectivity, and infrastructure solutions engineered for professionals who demand reliability.',
		'cta_text'        => 'Browse Products',
		'cta_url'         => quest_shop_url(),
		'cta_style'       => 'primary',
		'bg_image'        => '',
		'bg_color'        => '',
		'overlay_opacity' => 60,
		'bg_position'     => 'center center',
	];
}

$count = count( $slides );
?>

<section class="qt-hero" data-slides="<?php echo esc_attr( $count ); ?>">

	<?php foreach ( $slides as $i => $slide ) :
		$bg_url     = ! empty( $slide['bg_image'] ) ? $slide['bg_image']['url'] : '';
		$bg_color   = ! empty( $slide['bg_color'] ) ? $slide['bg_color'] : '#000000';
		$opacity    = isset( $slide['overlay_opacity'] ) ? intval( $slide['overlay_opacity'] ) / 100 : 0.6;
		$bg_pos     = ! empty( $slide['bg_position'] ) ? $slide['bg_position'] : 'center center';
		$btn_class  = $slide['cta_style'] === 'outline' ? 'qt-btn--hero-outline' : 'qt-btn--primary';
	?>
		<div class="qt-hero__slide<?php echo $i === 0 ? ' qt-hero__slide--active' : ''; ?>" aria-hidden="<?php echo $i === 0 ? 'false' : 'true'; ?>">
			<?php if ( $bg_url ) : ?>
				<div class="qt-hero__bg">
					<img src="<?php echo esc_url( $bg_url ); ?>" alt="" style="object-position:<?php echo esc_attr( $bg_pos ); ?>"<?php echo $i === 0 ? ' fetchpriority="high"' : ' loading="lazy"'; ?>>
				</div>
			<?php endif; ?>
			<div class="qt-hero__overlay" style="background:<?php echo esc_attr( $bg_color ); ?>;opacity:<?php echo esc_attr( $opacity ); ?>"></div>

			<div class="qt-container qt-hero__inner">
				<div class="qt-hero__content">
					<?php if ( ! empty( $slide['label'] ) ) : ?>
						<span class="qt-hero__eyebrow"><?php echo esc_html( $slide['label'] ); ?></span>
					<?php endif; ?>

					<h1 class="qt-hero__headline"><?php echo wp_kses_post( $slide['headline'] ); ?></h1>

					<?php if ( ! empty( $slide['body'] ) ) : ?>
						<p class="qt-hero__body"><?php echo wp_kses_post( $slide['body'] ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $slide['cta_text'] ) && ! empty( $slide['cta_url'] ) ) : ?>
						<div class="qt-hero__cta">
							<a href="<?php echo esc_url( $slide['cta_url'] ); ?>" class="qt-btn qt-btn--lg <?php echo esc_attr( $btn_class ); ?>">
								<?php echo esc_html( $slide['cta_text'] ); ?>
								<?php echo quest_icon( 'arrow-right', 18 ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>

	<?php if ( $count > 1 ) : ?>
		<div class="qt-hero__controls">
			<div class="qt-container qt-hero__controls-inner">
				<div class="qt-hero__nav-arrows">
					<button type="button" class="qt-hero__arrow qt-hero__arrow--prev" aria-label="<?php esc_attr_e( 'Previous slide', 'quest' ); ?>" data-dir="prev">
						<?php echo quest_icon( 'chevron-right', 20 ); ?>
					</button>
					<button type="button" class="qt-hero__arrow qt-hero__arrow--next" aria-label="<?php esc_attr_e( 'Next slide', 'quest' ); ?>" data-dir="next">
						<?php echo quest_icon( 'chevron-right', 20 ); ?>
					</button>
				</div>

				<div class="qt-hero__progress">
					<?php for ( $i = 0; $i < $count; $i++ ) : ?>
						<button
							type="button"
							class="qt-hero__progress-dot<?php echo $i === 0 ? ' qt-hero__progress-dot--active' : ''; ?>"
							aria-label="<?php printf( esc_attr__( 'Slide %d of %d', 'quest' ), $i + 1, $count ); ?>"
							data-slide="<?php echo esc_attr( $i ); ?>"
						>
							<span class="qt-hero__progress-bar"></span>
						</button>
					<?php endfor; ?>
				</div>

				<div class="qt-hero__counter">
					<span class="qt-hero__counter-current">01</span>
					<span class="qt-hero__counter-sep">/</span>
					<span class="qt-hero__counter-total"><?php echo esc_html( str_pad( $count, 2, '0', STR_PAD_LEFT ) ); ?></span>
				</div>
			</div>
		</div>
	<?php endif; ?>

</section>
