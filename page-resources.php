<?php
/**
 * Template Name: Resources & Brochures
 */
defined( 'ABSPATH' ) || exit;

get_header();

$page_title    = get_the_title();
$page_subtitle = function_exists( 'get_field' ) ? get_field( 'resources_subtitle' ) : '';
$has_resources = function_exists( 'have_rows' ) && have_rows( 'resources_files' );

$resources  = [];
$categories = [];

if ( $has_resources ) {
	while ( have_rows( 'resources_files' ) ) {
		the_row();
		$name         = get_sub_field( 'name' );
		$file         = get_sub_field( 'file' );
		$external_url = get_sub_field( 'external_url' );
		$thumb        = get_sub_field( 'thumbnail' );
		$category     = get_sub_field( 'category' );
		$description  = get_sub_field( 'description' );
		$button_label = get_sub_field( 'button_label' );

		$url      = '';
		$file_ext = '';
		$file_size = '';
		$is_video = false;

		if ( $file ) {
			$url       = $file['url'];
			$file_ext  = strtoupper( pathinfo( $file['filename'], PATHINFO_EXTENSION ) );
			$file_size = size_format( $file['filesize'] ?? 0 );
		} elseif ( $external_url ) {
			$url      = $external_url;
			$is_video = (bool) preg_match( '/(youtube|youtu\.be|vimeo)/i', $external_url );
		}

		if ( ! $name ) continue;

		// Auto button label
		if ( ! $button_label ) {
			if ( $category === 'How-To Videos' || $is_video ) {
				$button_label = __( 'Watch Video', 'quest' );
			} elseif ( $category === 'Catalogs' ) {
				$button_label = __( 'Download Catalog', 'quest' );
			} else {
				$button_label = __( 'Download Resource', 'quest' );
			}
		}

		// Preview image
		$preview_url = '';
		if ( $thumb ) {
			$preview_url = $thumb['sizes']['medium'] ?? $thumb['url'];
		} elseif ( $file && ! empty( $file['ID'] ) ) {
			$pdf_thumb = wp_get_attachment_image_url( $file['ID'], 'medium' );
			if ( $pdf_thumb ) {
				$preview_url = $pdf_thumb;
			}
		}

		// Icon for video vs file
		$icon_type = $is_video ? 'play' : 'file-pdf';

		$resources[] = [
			'name'         => $name,
			'url'          => $url,
			'category'     => $category,
			'description'  => $description,
			'preview_url'  => $preview_url,
			'file_ext'     => $file_ext,
			'file_size'    => $file_size,
			'button_label' => $button_label,
			'is_video'     => $is_video,
			'has_file'     => (bool) $file,
		];

		if ( $category && ! in_array( $category, $categories, true ) ) {
			$categories[] = $category;
		}
	}
}
?>

<main id="qt-main" class="qt-main qt-main--home">

	<div class="qt-page-banner">
		<div class="qt-container">
			<h1 class="qt-page-banner__title"><?php echo esc_html( $page_title ); ?></h1>
			<?php if ( $page_subtitle ) : ?>
				<p class="qt-page-banner__subtitle"><?php echo esc_html( $page_subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="qt-section qt-resources-page">
		<div class="qt-container">

			<?php if ( ! empty( $categories ) ) : ?>
				<div class="qt-resources-filters" data-component="resource-filters">
					<button type="button" class="qt-resources-filter qt-resources-filter--active" data-filter="all">
						<?php esc_html_e( 'All', 'quest' ); ?>
						<span class="qt-resources-filter__count"><?php echo count( $resources ); ?></span>
					</button>
					<?php foreach ( $categories as $cat ) :
						$count = count( array_filter( $resources, fn( $r ) => $r['category'] === $cat ) );
					?>
						<button type="button" class="qt-resources-filter" data-filter="<?php echo esc_attr( $cat ); ?>">
							<?php echo esc_html( $cat ); ?>
							<span class="qt-resources-filter__count"><?php echo $count; ?></span>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $resources ) ) : ?>
				<div class="qt-resources-grid" id="qt-resources-grid">
					<?php foreach ( $resources as $res ) : ?>
						<div class="qt-resource-card" data-category="<?php echo esc_attr( $res['category'] ); ?>">
							<?php if ( $res['url'] ) : ?>
								<a href="<?php echo esc_url( $res['url'] ); ?>" class="qt-resource-card__preview" target="_blank" rel="noopener">
							<?php else : ?>
								<div class="qt-resource-card__preview">
							<?php endif; ?>
								<?php if ( $res['preview_url'] ) : ?>
									<img src="<?php echo esc_url( $res['preview_url'] ); ?>" alt="<?php echo esc_attr( $res['name'] ); ?>" loading="lazy">
								<?php elseif ( $res['is_video'] ) : ?>
									<div class="qt-resource-card__icon qt-resource-card__icon--video">
										<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><polygon points="5 3 19 12 5 21 5 3"/></svg>
										<span class="qt-resource-card__ext"><?php esc_html_e( 'VIDEO', 'quest' ); ?></span>
									</div>
								<?php else : ?>
									<div class="qt-resource-card__icon">
										<?php echo quest_icon( 'file-pdf', 40 ); ?>
										<?php if ( $res['file_ext'] ) : ?>
											<span class="qt-resource-card__ext"><?php echo esc_html( $res['file_ext'] ); ?></span>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							<?php echo $res['url'] ? '</a>' : '</div>'; ?>

							<div class="qt-resource-card__info">
								<h3 class="qt-resource-card__name">
									<?php if ( $res['url'] ) : ?>
										<a href="<?php echo esc_url( $res['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $res['name'] ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $res['name'] ); ?>
									<?php endif; ?>
								</h3>
								<?php if ( $res['description'] ) : ?>
									<p class="qt-resource-card__desc"><?php echo esc_html( $res['description'] ); ?></p>
								<?php endif; ?>
								<div class="qt-resource-card__meta">
									<?php if ( $res['file_ext'] ) : ?>
										<span class="qt-resource-card__type"><?php echo esc_html( $res['file_ext'] ); ?></span>
									<?php elseif ( $res['is_video'] ) : ?>
										<span class="qt-resource-card__type qt-resource-card__type--video"><?php esc_html_e( 'VIDEO', 'quest' ); ?></span>
									<?php endif; ?>
									<?php if ( $res['file_size'] && $res['file_size'] !== '0 B' ) : ?>
										<span class="qt-resource-card__size"><?php echo esc_html( $res['file_size'] ); ?></span>
									<?php endif; ?>
									<?php if ( $res['category'] ) : ?>
										<span class="qt-resource-card__cat"><?php echo esc_html( $res['category'] ); ?></span>
									<?php endif; ?>
								</div>
								<?php if ( $res['url'] ) : ?>
									<a href="<?php echo esc_url( $res['url'] ); ?>" class="qt-resource-card__download" target="_blank" rel="noopener" <?php echo $res['has_file'] ? 'download' : ''; ?>>
										<?php echo $res['is_video'] ? quest_icon( 'arrow-right', 16 ) : quest_icon( 'download', 16 ); ?>
										<?php echo esc_html( $res['button_label'] ); ?>
									</a>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="qt-no-content">
					<p><?php esc_html_e( 'No resources available at this time. Please check back soon.', 'quest' ); ?></p>
				</div>
			<?php endif; ?>

		</div>
	</div>

	<!-- Bottom CTA -->
	<section class="qt-cta">
		<div class="qt-container qt-cta__inner">
			<div class="qt-cta__content">
				<h2 class="qt-cta__headline"><?php esc_html_e( 'Need Help Finding the Right Resource?', 'quest' ); ?></h2>
				<p class="qt-cta__body"><?php esc_html_e( 'Contact Quest for product documentation, warranty support, or help selecting the right solution for your project.', 'quest' ); ?></p>
				<div class="qt-cta__buttons">
					<a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" class="qt-btn qt-btn--primary qt-btn--lg">
						<?php esc_html_e( 'Contact Quest', 'quest' ); ?>
						<?php echo quest_icon( 'arrow-right', 18 ); ?>
					</a>
					<a href="<?php echo esc_url( quest_shop_url() ); ?>" class="qt-btn qt-btn--hero-outline qt-btn--lg">
						<?php esc_html_e( 'Browse Products', 'quest' ); ?>
					</a>
				</div>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();
