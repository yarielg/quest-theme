<?php
/**
 * Template Name: Contact Us
 */
defined( 'ABSPATH' ) || exit;

get_header();

$page_title = get_the_title();

$address    = quest_option_text( 'company_address', '', false );
$phone      = quest_option_text( 'company_phone', '(800) 555-0199', false );
$email      = quest_option_text( 'company_email', 'info@questtechnologyintl.com', false );
$fax        = function_exists( 'get_field' ) ? get_field( 'contact_fax' ) : '';
$toll_free  = function_exists( 'get_field' ) ? get_field( 'contact_toll_free' ) : '';
$hours      = function_exists( 'get_field' ) ? get_field( 'contact_hours' ) : '';
$map_embed  = function_exists( 'get_field' ) ? get_field( 'contact_map_embed' ) : '';

$departments = [];
if ( function_exists( 'have_rows' ) && have_rows( 'contact_departments' ) ) {
	while ( have_rows( 'contact_departments' ) ) {
		the_row();
		$departments[] = [
			'department' => get_sub_field( 'department' ),
			'name'       => get_sub_field( 'name' ),
			'email'      => get_sub_field( 'email' ),
			'extension'  => get_sub_field( 'extension' ),
		];
	}
}

if ( empty( $departments ) ) {
	$departments = [
		[ 'department' => 'Domestic Sales',       'name' => 'Nick Pacella',      'email' => 'nick@qtinet.com',    'extension' => '1226' ],
		[ 'department' => 'Accounting',            'name' => 'Viviane Encio',     'email' => 'vencio@qtinet.com',  'extension' => '1204' ],
		[ 'department' => 'International Sales',   'name' => 'Marcela Ujueta',    'email' => 'mujueta@qtinet.com', 'extension' => '1224' ],
		[ 'department' => 'Invoicing',             'name' => 'Johana Nieto',      'email' => 'johana@qtinet.com',  'extension' => '1218' ],
		[ 'department' => 'Purchasing',            'name' => 'Don Lacertosa',     'email' => 'don@qtinet.com',     'extension' => '1220' ],
		[ 'department' => 'Accounts Receivable',   'name' => 'Johana Nieto',      'email' => 'johana@qtinet.com',  'extension' => '1218' ],
		[ 'department' => 'Marketing & Design',    'name' => 'Gregory Johnson',   'email' => 'gregory@qtinet.com', 'extension' => '1217' ],
		[ 'department' => 'Warehouse',             'name' => 'Reinaldo Vazquez',  'email' => 'rey@qtinet.com',     'extension' => '1206' ],
	];
}
?>

<main id="qt-main" class="qt-main">

	<div class="qt-page-banner">
		<div class="qt-container">
			<h1 class="qt-page-banner__title"><?php echo esc_html( $page_title ); ?></h1>
			<p class="qt-page-banner__subtitle"><?php esc_html_e( 'We\'re here to help. Reach out to the right department directly.', 'quest' ); ?></p>
		</div>
	</div>

	<!-- Department Contacts -->
	<section class="qt-section qt-contact-depts">
		<div class="qt-container">
			<div class="qt-section__header">
				<h2 class="qt-section__title"><?php esc_html_e( 'Department Contacts', 'quest' ); ?></h2>
			</div>
			<div class="qt-contact-depts__grid">
				<?php foreach ( $departments as $dept ) : ?>
					<div class="qt-dept-card">
						<h3 class="qt-dept-card__dept"><?php echo esc_html( $dept['department'] ); ?></h3>
						<p class="qt-dept-card__name"><?php echo esc_html( $dept['name'] ); ?></p>
						<a href="mailto:<?php echo esc_attr( $dept['email'] ); ?>" class="qt-dept-card__email"><?php echo esc_html( $dept['email'] ); ?></a>
						<?php if ( $dept['extension'] ) : ?>
							<span class="qt-dept-card__ext"><?php printf( esc_html__( 'Ext: %s', 'quest' ), esc_html( $dept['extension'] ) ); ?></span>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- Contact Form + Company Info -->
	<section class="qt-section qt-contact-main">
		<div class="qt-container">
			<div class="qt-contact-main__grid">

				<!-- Form -->
				<div class="qt-contact-form-wrap">
					<h2 class="qt-contact-form__title"><?php esc_html_e( 'Send Us a Message', 'quest' ); ?></h2>
					<form method="post" class="qt-contact-form" id="qt-contact-form">
						<div class="qt-contact-form__row">
							<div class="qt-contact-form__field">
								<label for="cf_name"><?php esc_html_e( 'Full Name', 'quest' ); ?> <span class="qt-required">*</span></label>
								<input type="text" id="cf_name" name="cf_name" required>
							</div>
							<div class="qt-contact-form__field">
								<label for="cf_email"><?php esc_html_e( 'Email Address', 'quest' ); ?> <span class="qt-required">*</span></label>
								<input type="email" id="cf_email" name="cf_email" required>
							</div>
						</div>
						<div class="qt-contact-form__row">
							<div class="qt-contact-form__field">
								<label for="cf_phone"><?php esc_html_e( 'Phone Number', 'quest' ); ?></label>
								<input type="tel" id="cf_phone" name="cf_phone">
							</div>
							<div class="qt-contact-form__field">
								<label for="cf_company"><?php esc_html_e( 'Company', 'quest' ); ?></label>
								<input type="text" id="cf_company" name="cf_company">
							</div>
						</div>
						<div class="qt-contact-form__field">
							<label for="cf_subject"><?php esc_html_e( 'Subject', 'quest' ); ?> <span class="qt-required">*</span></label>
							<select id="cf_subject" name="cf_subject" required>
								<option value=""><?php esc_html_e( 'Select a topic...', 'quest' ); ?></option>
								<option value="Sales Inquiry"><?php esc_html_e( 'Sales Inquiry', 'quest' ); ?></option>
								<option value="Product Information"><?php esc_html_e( 'Product Information', 'quest' ); ?></option>
								<option value="Dealer Account"><?php esc_html_e( 'Dealer Account', 'quest' ); ?></option>
								<option value="Order Status"><?php esc_html_e( 'Order Status', 'quest' ); ?></option>
								<option value="Technical Support"><?php esc_html_e( 'Technical Support', 'quest' ); ?></option>
								<option value="Billing / Invoicing"><?php esc_html_e( 'Billing / Invoicing', 'quest' ); ?></option>
								<option value="Other"><?php esc_html_e( 'Other', 'quest' ); ?></option>
							</select>
						</div>
						<div class="qt-contact-form__field">
							<label for="cf_message"><?php esc_html_e( 'Message', 'quest' ); ?> <span class="qt-required">*</span></label>
							<textarea id="cf_message" name="cf_message" rows="5" required></textarea>
						</div>

						<!-- Honeypot -->
						<div class="qt-contact-form__hp" aria-hidden="true">
							<input type="text" name="cf_website_url" tabindex="-1" autocomplete="off">
						</div>

						<!-- Turnstile placeholder -->
						<div class="qt-contact-form__turnstile" id="qt-turnstile-container">
							<?php
							$turnstile_key = function_exists( 'get_field' ) ? get_field( 'turnstile_site_key', 'option' ) : '';
							if ( $turnstile_key ) :
							?>
								<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( $turnstile_key ); ?>"></div>
							<?php endif; ?>
						</div>

						<?php wp_nonce_field( 'quest_contact_form', 'quest_contact_nonce' ); ?>
						<button type="submit" class="qt-btn qt-btn--primary qt-btn--lg qt-btn--block" id="qt-contact-submit">
							<?php esc_html_e( 'Send Message', 'quest' ); ?>
							<?php echo quest_icon( 'arrow-right', 18 ); ?>
						</button>
					</form>
					<div class="qt-contact-form__result" id="qt-contact-result" hidden></div>
				</div>

				<!-- Company Info -->
				<div class="qt-contact-info">
					<h2 class="qt-contact-info__title"><?php esc_html_e( 'Company Information', 'quest' ); ?></h2>

					<div class="qt-contact-info__cards">
						<?php if ( $address ) : ?>
							<div class="qt-info-card">
								<div class="qt-info-card__icon"><?php echo quest_icon( 'home', 22 ); ?></div>
								<div class="qt-info-card__content">
									<h4><?php esc_html_e( 'Address', 'quest' ); ?></h4>
									<p><?php echo nl2br( esc_html( $address ) ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $toll_free ) : ?>
							<div class="qt-info-card">
								<div class="qt-info-card__icon"><?php echo quest_icon( 'phone', 22 ); ?></div>
								<div class="qt-info-card__content">
									<h4><?php esc_html_e( 'Toll Free', 'quest' ); ?></h4>
									<p><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $toll_free ) ); ?>"><?php echo esc_html( $toll_free ); ?></a></p>
								</div>
							</div>
						<?php elseif ( $phone ) : ?>
							<div class="qt-info-card">
								<div class="qt-info-card__icon"><?php echo quest_icon( 'phone', 22 ); ?></div>
								<div class="qt-info-card__content">
									<h4><?php esc_html_e( 'Phone', 'quest' ); ?></h4>
									<p><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $fax ) : ?>
							<div class="qt-info-card">
								<div class="qt-info-card__icon"><?php echo quest_icon( 'phone', 22 ); ?></div>
								<div class="qt-info-card__content">
									<h4><?php esc_html_e( 'Fax', 'quest' ); ?></h4>
									<p><?php echo esc_html( $fax ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $email ) : ?>
							<div class="qt-info-card">
								<div class="qt-info-card__icon"><?php echo quest_icon( 'mail', 22 ); ?></div>
								<div class="qt-info-card__content">
									<h4><?php esc_html_e( 'Email', 'quest' ); ?></h4>
									<p><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $hours ) : ?>
							<div class="qt-info-card">
								<div class="qt-info-card__icon"><?php echo quest_icon( 'grid', 22 ); ?></div>
								<div class="qt-info-card__content">
									<h4><?php esc_html_e( 'Hours of Operation', 'quest' ); ?></h4>
									<p><?php echo nl2br( esc_html( $hours ) ); ?></p>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>

			</div>
		</div>
	</section>

	<!-- Map -->
	<?php if ( $map_embed ) : ?>
		<section class="qt-contact-map">
			<div class="qt-contact-map__embed">
				<?php echo $map_embed; ?>
			</div>
		</section>
	<?php endif; ?>

	<!-- Distributor CTA -->
	<?php get_template_part( 'template-parts/content/cta-section' ); ?>

</main>

<?php
get_footer();
