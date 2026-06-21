<?php
defined( 'ABSPATH' ) || exit;

// ---------------------------------------------------------------------------
// Custom user roles
// ---------------------------------------------------------------------------
function quest_register_roles(): void {
	$customer_caps = get_role( 'customer' ) ? get_role( 'customer' )->capabilities : [ 'read' => true ];

	add_role( 'quest_pending', __( 'Pending Dealer', 'quest' ), [
		'read' => true,
	] );

	add_role( 'quest_dealer', __( 'Quest Dealer', 'quest' ), array_merge( $customer_caps, [
		'read' => true,
	] ) );
}
add_action( 'after_setup_theme', 'quest_register_roles' );

// ---------------------------------------------------------------------------
// Registration form handler
// ---------------------------------------------------------------------------
function quest_handle_registration(): void {
	if ( ! isset( $_POST['quest_register_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['quest_register_nonce'], 'quest_register' ) ) {
		wc_add_notice( __( 'Security check failed. Please try again.', 'quest' ), 'error' );
		return;
	}

	$turnstile = quest_verify_turnstile();
	if ( is_wp_error( $turnstile ) ) {
		wc_add_notice( $turnstile->get_error_message(), 'error' );
		return;
	}

	$fields = [
		'first_name'   => sanitize_text_field( $_POST['reg_first_name'] ?? '' ),
		'last_name'    => sanitize_text_field( $_POST['reg_last_name'] ?? '' ),
		'company'      => sanitize_text_field( $_POST['reg_company'] ?? '' ),
		'email'        => sanitize_email( $_POST['reg_email'] ?? '' ),
		'phone'        => sanitize_text_field( $_POST['reg_phone'] ?? '' ),
		'address'      => sanitize_text_field( $_POST['reg_address'] ?? '' ),
		'city'         => sanitize_text_field( $_POST['reg_city'] ?? '' ),
		'state'        => sanitize_text_field( $_POST['reg_state'] ?? '' ),
		'zip'          => sanitize_text_field( $_POST['reg_zip'] ?? '' ),
		'password'     => $_POST['reg_password'] ?? '',
		'password2'    => $_POST['reg_password2'] ?? '',
	];

	// Validation
	$errors = [];

	if ( empty( $fields['first_name'] ) )  $errors[] = __( 'First name is required.', 'quest' );
	if ( empty( $fields['last_name'] ) )   $errors[] = __( 'Last name is required.', 'quest' );
	if ( empty( $fields['company'] ) )     $errors[] = __( 'Company name is required.', 'quest' );
	if ( empty( $fields['email'] ) || ! is_email( $fields['email'] ) ) {
		$errors[] = __( 'A valid email address is required.', 'quest' );
	}
	if ( empty( $fields['phone'] ) )       $errors[] = __( 'Phone number is required.', 'quest' );
	if ( empty( $fields['address'] ) )     $errors[] = __( 'Address is required.', 'quest' );
	if ( strlen( $fields['password'] ) < 8 ) {
		$errors[] = __( 'Password must be at least 8 characters.', 'quest' );
	}
	if ( $fields['password'] !== $fields['password2'] ) {
		$errors[] = __( 'Passwords do not match.', 'quest' );
	}
	if ( email_exists( $fields['email'] ) ) {
		$errors[] = __( 'An account with this email already exists.', 'quest' );
	}

	if ( ! empty( $errors ) ) {
		foreach ( $errors as $err ) {
			wc_add_notice( $err, 'error' );
		}
		return;
	}

	// Create user
	$username = sanitize_user( strtolower( $fields['first_name'] . '.' . $fields['last_name'] ) );
	$username = quest_unique_username( $username );

	$user_id = wp_insert_user( [
		'user_login'   => $username,
		'user_email'   => $fields['email'],
		'user_pass'    => $fields['password'],
		'first_name'   => $fields['first_name'],
		'last_name'    => $fields['last_name'],
		'display_name' => $fields['first_name'] . ' ' . $fields['last_name'],
		'role'         => 'quest_pending',
	] );

	if ( is_wp_error( $user_id ) ) {
		wc_add_notice( $user_id->get_error_message(), 'error' );
		return;
	}

	// Save B2B meta
	update_user_meta( $user_id, 'quest_company',        $fields['company'] );
	update_user_meta( $user_id, 'quest_phone',           $fields['phone'] );
	update_user_meta( $user_id, 'quest_address',         $fields['address'] );
	update_user_meta( $user_id, 'quest_city',            $fields['city'] );
	update_user_meta( $user_id, 'quest_state',           $fields['state'] );
	update_user_meta( $user_id, 'quest_zip',             $fields['zip'] );
	update_user_meta( $user_id, 'quest_account_status',  'pending' );
	update_user_meta( $user_id, 'quest_registered_date', current_time( 'mysql' ) );

	// WooCommerce billing fields
	update_user_meta( $user_id, 'billing_first_name', $fields['first_name'] );
	update_user_meta( $user_id, 'billing_last_name',  $fields['last_name'] );
	update_user_meta( $user_id, 'billing_company',    $fields['company'] );
	update_user_meta( $user_id, 'billing_email',      $fields['email'] );
	update_user_meta( $user_id, 'billing_phone',      $fields['phone'] );
	update_user_meta( $user_id, 'billing_address_1',  $fields['address'] );
	update_user_meta( $user_id, 'billing_city',       $fields['city'] );
	update_user_meta( $user_id, 'billing_state',      $fields['state'] );
	update_user_meta( $user_id, 'billing_postcode',   $fields['zip'] );

	// Send admin notification
	quest_send_admin_new_application( $user_id, $fields );

	// Send user confirmation
	quest_send_user_pending_email( $user_id, $fields );

	// Redirect to pending page
	wc_add_notice( __( 'Your application has been submitted! We will review it and get back to you shortly.', 'quest' ), 'success' );

	wp_safe_redirect( add_query_arg( 'quest-registered', '1', quest_account_url() ) );
	exit;
}
add_action( 'template_redirect', 'quest_handle_registration' );

// ---------------------------------------------------------------------------
// Unique username generator
// ---------------------------------------------------------------------------
function quest_unique_username( string $base ): string {
	$username = $base;
	$i = 1;
	while ( username_exists( $username ) ) {
		$username = $base . $i;
		$i++;
	}
	return $username;
}

// ---------------------------------------------------------------------------
// Admin notification email
// ---------------------------------------------------------------------------
function quest_send_admin_new_application( int $user_id, array $fields ): void {
	$admin_email = quest_get_notification_email();
	$site_name   = get_bloginfo( 'name' );

	$subject = sprintf( '[%s] New Dealer Application: %s', $site_name, $fields['company'] );

	$message  = "A new dealer application has been submitted.\n\n";
	$message .= "Company: {$fields['company']}\n";
	$message .= "Contact: {$fields['first_name']} {$fields['last_name']}\n";
	$message .= "Email: {$fields['email']}\n";
	$message .= "Phone: {$fields['phone']}\n";
	$message .= "Address: {$fields['address']}, {$fields['city']}, {$fields['state']} {$fields['zip']}\n\n";
	$message .= "Review this application:\n";
	$message .= admin_url( 'user-edit.php?user_id=' . $user_id ) . "\n\n";
	$message .= "Or view all pending applications:\n";
	$message .= admin_url( 'users.php?role=quest_pending' ) . "\n";

	wp_mail( $admin_email, $subject, $message );
}

// ---------------------------------------------------------------------------
// User pending confirmation email
// ---------------------------------------------------------------------------
function quest_send_user_pending_email( int $user_id, array $fields ): void {
	$site_name = get_bloginfo( 'name' );

	$subject = sprintf( '[%s] Application Received', $site_name );

	$message  = "Hi {$fields['first_name']},\n\n";
	$message .= "Thank you for applying for a dealer account with {$site_name}.\n\n";
	$message .= "We have received your application and our team will review it shortly. ";
	$message .= "You will receive an email once your account has been approved.\n\n";
	$message .= "If you have any questions, please don't hesitate to contact us.\n\n";
	$message .= "Best regards,\n";
	$message .= "The {$site_name} Team\n";

	wp_mail( $fields['email'], $subject, $message );
}

// ---------------------------------------------------------------------------
// Get notification email (from ACF or fallback to admin email)
// ---------------------------------------------------------------------------
function quest_get_notification_email(): string {
	if ( function_exists( 'get_field' ) ) {
		$email = get_field( 'notification_email', 'option' );
		if ( $email && is_email( $email ) ) {
			return $email;
		}
	}
	return get_option( 'admin_email' );
}

// ---------------------------------------------------------------------------
// Prevent pending users from accessing certain pages
// ---------------------------------------------------------------------------
function quest_restrict_pending_users(): void {
	if ( ! is_user_logged_in() ) return;

	$user = wp_get_current_user();
	if ( ! in_array( 'quest_pending', (array) $user->roles, true ) ) return;

	if ( is_cart() || is_checkout() ) {
		wp_safe_redirect( quest_account_url() );
		exit;
	}
}
add_action( 'template_redirect', 'quest_restrict_pending_users' );

// ---------------------------------------------------------------------------
// Turnstile verification on WooCommerce login
// ---------------------------------------------------------------------------
function quest_verify_login_turnstile( $validation_error ): \WP_Error {
	$turnstile = quest_verify_turnstile();
	if ( is_wp_error( $turnstile ) ) {
		$validation_error->add( 'turnstile_fail', $turnstile->get_error_message() );
	}
	return $validation_error;
}
add_filter( 'woocommerce_process_login_errors', 'quest_verify_login_turnstile' );

// ---------------------------------------------------------------------------
// Custom My Account endpoints
// Skip when quest-asap plugin handles these (avoids duplicate registration)
// ---------------------------------------------------------------------------
function quest_account_endpoints(): void {
	if ( class_exists( 'QuestAsap\\Dealer\\Endpoints' ) ) return;
	add_rewrite_endpoint( 'company-details', EP_ROOT | EP_PAGES );
	add_rewrite_endpoint( 'resources', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'quest_account_endpoints' );

function quest_account_menu_items( array $items ): array {
	if ( class_exists( 'QuestAsap\\Dealer\\Endpoints' ) ) return $items;

	$user = wp_get_current_user();
	$is_pending = in_array( 'quest_pending', (array) $user->roles, true );

	$new_items = [];

	$new_items['dashboard'] = __( 'Dashboard', 'quest' );

	if ( ! $is_pending ) {
		$new_items['company-details'] = __( 'Company Details', 'quest' );
		if ( isset( $items['orders'] ) && ! quest_is_catalog_mode() ) {
			$new_items['orders'] = $items['orders'];
		}
		$new_items['resources'] = __( 'Resources', 'quest' );
	}

	$new_items['edit-account'] = __( 'Account Settings', 'quest' );
	$new_items['customer-logout'] = $items['customer-logout'] ?? __( 'Logout', 'quest' );

	return $new_items;
}
add_filter( 'woocommerce_account_menu_items', 'quest_account_menu_items' );

function quest_account_company_details_content(): void {
	if ( class_exists( 'QuestAsap\\Dealer\\Endpoints' ) ) return;
	get_template_part( 'template-parts/account/company-details' );
}
add_action( 'woocommerce_account_company-details_endpoint', 'quest_account_company_details_content' );

function quest_account_resources_content(): void {
	if ( class_exists( 'QuestAsap\\Dealer\\Endpoints' ) ) return;
	get_template_part( 'template-parts/account/resources' );
}
add_action( 'woocommerce_account_resources_endpoint', 'quest_account_resources_content' );
