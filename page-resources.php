<?php
/**
 * Template Name: Resources & Brochures
 */
defined( 'ABSPATH' ) || exit;

get_header();

$page_title    = get_the_title();
$page_subtitle = function_exists( 'get_field' ) ? get_field( 'resources_subtitle' ) : '';
$resources     = function_exists( 'have_rows' ) && have_rows( 'resources_files' ) ? true : false;
?>

<main id="qt-main" class="qt-main">

	<div class="qt-page-banner">
		<div class="qt-container">
			<h1 class="qt-page-banner__title"><?php echo esc_html( $page_title ); ?></h1>
			<?php if ( $page_subtitle ) : ?>
				<p class="qt-page-banner__subtitle"><?php echo esc_html( $page_subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="qt-container qt-resources-page">

		<?php if ( $resources ) : ?>
			<div class="qt-resources-grid">
				<?php while ( have_rows( 'resources_files' ) ) : the_row();
					$name     = get_sub_field( 'name' );
					$file     = get_sub_field( 'file' );
					$thumb    = get_sub_field( 'thumbnail' );
					$category = get_sub_field( 'category' );

					if ( ! $file ) continue;

					$file_url  = $file['url'];
					$file_size = size_format( $file['filesize'] ?? 0 );
					$file_ext  = strtoupper( pathinfo( $file['filename'], PATHINFO_EXTENSION ) );

					$preview_url = '';
					if ( $thumb ) {
						$preview_url = $thumb['sizes']['medium'] ?? $thumb['url'];
					} elseif ( ! empty( $file['ID'] ) ) {
						$pdf_thumb = wp_get_attachment_image_url( $file['ID'], 'medium' );
						if ( $pdf_thumb ) {
							$preview_url = $pdf_thumb;
						}
					}
				?>
					<div class="qt-resource-card" <?php echo $category ? 'data-category="' . esc_attr( $category ) . '"' : ''; ?>>
						<a href="<?php echo esc_url( $file_url ); ?>" class="qt-resource-card__preview" target="_blank" rel="noopener">
							<?php if ( $preview_url ) : ?>
								<img src="<?php echo esc_url( $preview_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy">
							<?php else : ?>
								<div class="qt-resource-card__icon">
									<?php echo quest_icon( 'file-pdf', 40 ); ?>
									<span class="qt-resource-card__ext"><?php echo esc_html( $file_ext ); ?></span>
								</div>
							<?php endif; ?>
						</a>
						<div class="qt-resource-card__info">
							<h3 class="qt-resource-card__name">
								<a href="<?php echo esc_url( $file_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $name ); ?></a>
							</h3>
							<div class="qt-resource-card__meta">
								<span class="qt-resource-card__type"><?php echo esc_html( $file_ext ); ?></span>
								<?php if ( $file_size && $file_size !== '0 B' ) : ?>
									<span class="qt-resource-card__size"><?php echo esc_html( $file_size ); ?></span>
								<?php endif; ?>
								<?php if ( $category ) : ?>
									<span class="qt-resource-card__cat"><?php echo esc_html( $category ); ?></span>
								<?php endif; ?>
							</div>
							<a href="<?php echo esc_url( $file_url ); ?>" class="qt-resource-card__download" download target="_blank" rel="noopener">
								<?php echo quest_icon( 'download', 16 ); ?>
								<?php esc_html_e( 'Download', 'quest' ); ?>
							</a>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<div class="qt-no-content">
				<p><?php esc_html_e( 'No resources available at this time. Please check back soon.', 'quest' ); ?></p>
			</div>
		<?php endif; ?>

	</div>

</main>

<?php
get_footer();
