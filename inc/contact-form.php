<?php
defined( 'ABSPATH' ) || exit;

function quest_handle_contact_form(): void {
	if ( ! isset( $_POST['quest_contact_nonce'] ) ) {
		return;
	}

	check_ajax_referer( 'quest_contact_form', 'quest_contact_nonce' );

	// Honeypot
	if ( ! empty( $_POST['cf_website_url'] ) ) {
		wp_send_json_error( [ 'message' => 'Spam detected.' ] );
	}

	// Turnstile verification
	$secret = function_exists( 'get_field' ) ? get_field( 'turnstile_secret_key', 'option' ) : '';
	if ( $secret ) {
		$token    = sanitize_text_field( $_POST['cf-turnstile-response'] ?? '' );
		$response = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', [
			'body' => [
				'secret'   => $secret,
				'response' => $token,
				'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
			],
		] );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( [ 'message' => 'Security verification failed. Please try again.' ] );
		}

		$result = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( empty( $result['success'] ) ) {
			wp_send_json_error( [ 'message' => 'Security verification failed. Please complete the challenge.' ] );
		}
	}

	$name    = sanitize_text_field( $_POST['cf_name'] ?? '' );
	$email   = sanitize_email( $_POST['cf_email'] ?? '' );
	$phone   = sanitize_text_field( $_POST['cf_phone'] ?? '' );
	$company = sanitize_text_field( $_POST['cf_company'] ?? '' );
	$subject = sanitize_text_field( $_POST['cf_subject'] ?? '' );
	$msg     = sanitize_textarea_field( $_POST['cf_message'] ?? '' );

	if ( ! $name || ! $email || ! is_email( $email ) || ! $subject || ! $msg ) {
		wp_send_json_error( [ 'message' => 'Please fill in all required fields.' ] );
	}

	$admin_email = function_exists( 'quest_get_notification_email' )
		? quest_get_notification_email()
		: get_option( 'admin_email' );

	$site_name   = get_bloginfo( 'name' );
	$mail_subject = sprintf( '[%s] Contact: %s — %s', $site_name, $subject, $name );

	$body  = "New contact form submission:\n\n";
	$body .= "Name: {$name}\n";
	$body .= "Email: {$email}\n";
	if ( $phone )   $body .= "Phone: {$phone}\n";
	if ( $company ) $body .= "Company: {$company}\n";
	$body .= "Subject: {$subject}\n\n";
	$body .= "Message:\n{$msg}\n";

	$headers = [ 'Reply-To: ' . $name . ' <' . $email . '>' ];
	$sent    = wp_mail( $admin_email, $mail_subject, $body, $headers );

	if ( $sent ) {
		wp_send_json_success( [ 'message' => 'Thank you! Your message has been sent. We will get back to you shortly.' ] );
	}

	wp_send_json_error( [ 'message' => 'Failed to send message. Please try again or contact us directly.' ] );
}
add_action( 'wp_ajax_quest_contact_form', 'quest_handle_contact_form' );
add_action( 'wp_ajax_nopriv_quest_contact_form', 'quest_handle_contact_form' );

function quest_enqueue_turnstile(): void {
	if ( ! is_page_template( 'page-contact.php' ) ) {
		return;
	}

	$site_key = function_exists( 'get_field' ) ? get_field( 'turnstile_site_key', 'option' ) : '';
	if ( $site_key ) {
		wp_enqueue_script( 'cf-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js', [], null, true );
	}

	wp_enqueue_script(
		'quest-contact-form',
		QUEST_URL . '/assets/js/contact-form.js',
		[],
		QUEST_VERSION,
		[ 'strategy' => 'defer', 'in_footer' => true ]
	);

	wp_localize_script( 'quest-contact-form', 'questContact', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'quest_contact_form' ),
	] );
}
add_action( 'wp_enqueue_scripts', 'quest_enqueue_turnstile' );
