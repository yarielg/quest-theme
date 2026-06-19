<?php
defined( 'ABSPATH' ) || exit;

$is_registered = isset( $_GET['quest-registered'] );
$show_register = isset( $_GET['action'] ) && $_GET['action'] === 'register';
$has_errors     = wc_notice_count( 'error' ) > 0;
$reg_attempted  = isset( $_POST['quest_register_nonce'] );

$active_tab = ( $show_register || $reg_attempted ) ? 'register' : 'login';
?>

<div class="qt-auth">

	<?php if ( $is_registered ) : ?>
		<div class="qt-auth__success">
			<div class="qt-auth__success-icon"><?php echo quest_icon( 'certified', 48 ); ?></div>
			<h2><?php esc_html_e( 'Application Submitted!', 'quest' ); ?></h2>
			<p><?php esc_html_e( 'Thank you for your interest in becoming a Quest Technology dealer. Our team will review your application and contact you within 1-2 business days.', 'quest' ); ?></p>
		</div>
	<?php else : ?>

		<div class="qt-auth__card" data-component="auth-tabs">

			<div class="qt-auth__tabs">
				<button type="button"
					class="qt-auth__tab<?php echo $active_tab === 'login' ? ' qt-auth__tab--active' : ''; ?>"
					data-tab="login"
				><?php esc_html_e( 'Sign In', 'quest' ); ?></button>
				<button type="button"
					class="qt-auth__tab<?php echo $active_tab === 'register' ? ' qt-auth__tab--active' : ''; ?>"
					data-tab="register"
				><?php esc_html_e( 'Become a Dealer', 'quest' ); ?></button>
			</div>

			<?php wc_print_notices(); ?>

			<!-- Login Panel -->
			<div class="qt-auth__panel<?php echo $active_tab === 'login' ? ' qt-auth__panel--active' : ''; ?>" data-panel="login">
				<div class="qt-auth__panel-header">
					<h2 class="qt-auth__title"><?php esc_html_e( 'Welcome Back', 'quest' ); ?></h2>
					<p class="qt-auth__subtitle"><?php esc_html_e( 'Sign in to access your dealer account', 'quest' ); ?></p>
				</div>

				<form method="post" class="qt-auth__form">
					<?php do_action( 'woocommerce_login_form_start' ); ?>

					<div class="qt-auth__field">
						<label for="username"><?php esc_html_e( 'Email Address', 'quest' ); ?></label>
						<input type="text" id="username" name="username" autocomplete="username" required
							value="<?php echo isset( $_POST['username'] ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>">
					</div>

					<div class="qt-auth__field">
						<label for="password"><?php esc_html_e( 'Password', 'quest' ); ?></label>
						<input type="password" id="password" name="password" autocomplete="current-password" required>
					</div>

					<div class="qt-auth__row">
						<label class="qt-auth__checkbox">
							<input type="checkbox" name="rememberme" value="forever">
							<span><?php esc_html_e( 'Remember me', 'quest' ); ?></span>
						</label>
						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="qt-auth__forgot">
							<?php esc_html_e( 'Forgot password?', 'quest' ); ?>
						</a>
					</div>

					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" name="login" value="1" class="qt-btn qt-btn--primary qt-btn--block qt-btn--lg">
						<?php esc_html_e( 'Sign In', 'quest' ); ?>
					</button>

					<?php do_action( 'woocommerce_login_form_end' ); ?>
				</form>

				<div class="qt-auth__switch">
					<p><?php esc_html_e( "Don't have an account?", 'quest' ); ?>
						<button type="button" class="qt-auth__switch-btn" data-tab="register"><?php esc_html_e( 'Apply to become a dealer', 'quest' ); ?></button>
					</p>
				</div>
			</div>

			<!-- Register Panel -->
			<div class="qt-auth__panel<?php echo $active_tab === 'register' ? ' qt-auth__panel--active' : ''; ?>" data-panel="register">
				<div class="qt-auth__panel-header">
					<h2 class="qt-auth__title"><?php esc_html_e( 'Become a Dealer', 'quest' ); ?></h2>
					<p class="qt-auth__subtitle"><?php esc_html_e( 'Apply for a dealer account to access pricing and resources', 'quest' ); ?></p>
				</div>

				<form method="post" class="qt-auth__form" id="qt-register-form">

					<div class="qt-auth__field-row">
						<div class="qt-auth__field">
							<label for="reg_first_name"><?php esc_html_e( 'First Name', 'quest' ); ?> <span class="qt-required">*</span></label>
							<input type="text" id="reg_first_name" name="reg_first_name" required
								value="<?php echo isset( $_POST['reg_first_name'] ) ? esc_attr( $_POST['reg_first_name'] ) : ''; ?>">
						</div>
						<div class="qt-auth__field">
							<label for="reg_last_name"><?php esc_html_e( 'Last Name', 'quest' ); ?> <span class="qt-required">*</span></label>
							<input type="text" id="reg_last_name" name="reg_last_name" required
								value="<?php echo isset( $_POST['reg_last_name'] ) ? esc_attr( $_POST['reg_last_name'] ) : ''; ?>">
						</div>
					</div>

					<div class="qt-auth__field">
						<label for="reg_company"><?php esc_html_e( 'Company Name', 'quest' ); ?> <span class="qt-required">*</span></label>
						<input type="text" id="reg_company" name="reg_company" required
							value="<?php echo isset( $_POST['reg_company'] ) ? esc_attr( $_POST['reg_company'] ) : ''; ?>">
					</div>

					<div class="qt-auth__field-row">
						<div class="qt-auth__field">
							<label for="reg_email"><?php esc_html_e( 'Business Email', 'quest' ); ?> <span class="qt-required">*</span></label>
							<input type="email" id="reg_email" name="reg_email" autocomplete="email" required
								value="<?php echo isset( $_POST['reg_email'] ) ? esc_attr( $_POST['reg_email'] ) : ''; ?>">
						</div>
						<div class="qt-auth__field">
							<label for="reg_phone"><?php esc_html_e( 'Phone Number', 'quest' ); ?> <span class="qt-required">*</span></label>
							<input type="tel" id="reg_phone" name="reg_phone" required
								value="<?php echo isset( $_POST['reg_phone'] ) ? esc_attr( $_POST['reg_phone'] ) : ''; ?>">
						</div>
					</div>

					<div class="qt-auth__field">
						<label for="reg_address"><?php esc_html_e( 'Street Address', 'quest' ); ?> <span class="qt-required">*</span></label>
						<input type="text" id="reg_address" name="reg_address" required
							value="<?php echo isset( $_POST['reg_address'] ) ? esc_attr( $_POST['reg_address'] ) : ''; ?>">
					</div>

					<div class="qt-auth__field-row qt-auth__field-row--3">
						<div class="qt-auth__field">
							<label for="reg_city"><?php esc_html_e( 'City', 'quest' ); ?></label>
							<input type="text" id="reg_city" name="reg_city"
								value="<?php echo isset( $_POST['reg_city'] ) ? esc_attr( $_POST['reg_city'] ) : ''; ?>">
						</div>
						<div class="qt-auth__field">
							<label for="reg_state"><?php esc_html_e( 'State', 'quest' ); ?></label>
							<input type="text" id="reg_state" name="reg_state"
								value="<?php echo isset( $_POST['reg_state'] ) ? esc_attr( $_POST['reg_state'] ) : ''; ?>">
						</div>
						<div class="qt-auth__field">
							<label for="reg_zip"><?php esc_html_e( 'ZIP Code', 'quest' ); ?></label>
							<input type="text" id="reg_zip" name="reg_zip"
								value="<?php echo isset( $_POST['reg_zip'] ) ? esc_attr( $_POST['reg_zip'] ) : ''; ?>">
						</div>
					</div>

					<div class="qt-auth__field-row">
						<div class="qt-auth__field">
							<label for="reg_password"><?php esc_html_e( 'Password', 'quest' ); ?> <span class="qt-required">*</span></label>
							<input type="password" id="reg_password" name="reg_password" autocomplete="new-password" required minlength="8">
						</div>
						<div class="qt-auth__field">
							<label for="reg_password2"><?php esc_html_e( 'Confirm Password', 'quest' ); ?> <span class="qt-required">*</span></label>
							<input type="password" id="reg_password2" name="reg_password2" autocomplete="new-password" required minlength="8">
						</div>
					</div>

					<?php wp_nonce_field( 'quest_register', 'quest_register_nonce' ); ?>
					<button type="submit" class="qt-btn qt-btn--primary qt-btn--block qt-btn--lg">
						<?php esc_html_e( 'Submit Application', 'quest' ); ?>
						<?php echo quest_icon( 'arrow-right', 18 ); ?>
					</button>

					<p class="qt-auth__note"><?php esc_html_e( 'Applications are reviewed within 1-2 business days. You will receive an email once approved.', 'quest' ); ?></p>
				</form>

				<div class="qt-auth__switch">
					<p><?php esc_html_e( 'Already have an account?', 'quest' ); ?>
						<button type="button" class="qt-auth__switch-btn" data-tab="login"><?php esc_html_e( 'Sign in', 'quest' ); ?></button>
					</p>
				</div>
			</div>

		</div>

	<?php endif; ?>
</div>
