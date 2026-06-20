<?php
defined( 'ABSPATH' ) || exit;

function quest_email_wrap( string $body, string $subject = '' ): string {
	$logo_id  = get_theme_mod( 'custom_logo' );
	$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
	$site_name = get_bloginfo( 'name' );
	$site_url  = home_url( '/' );
	$year      = date( 'Y' );

	$phone   = function_exists( 'get_field' ) ? ( get_field( 'company_phone', 'option' ) ?: '' ) : '';
	$email   = function_exists( 'get_field' ) ? ( get_field( 'company_email', 'option' ) ?: '' ) : '';
	$address = function_exists( 'get_field' ) ? ( get_field( 'company_address', 'option' ) ?: '' ) : '';

	$body_html = nl2br( esc_html( $body ) );
	$body_html = preg_replace( '/https?:\/\/[^\s<]+/', '<a href="$0" style="color:#CC0000;">$0</a>', $body_html );

	ob_start();
	?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo esc_html( $subject ?: $site_name ); ?></title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,Helvetica,sans-serif;-webkit-font-smoothing:antialiased;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;">
<tr><td align="center" style="padding:24px 16px;">

<!-- Container -->
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

<!-- Header -->
<tr>
<td style="background-color:#111111;padding:24px 32px;text-align:center;border-radius:8px 8px 0 0;">
	<?php if ( $logo_url ) : ?>
		<a href="<?php echo esc_url( $site_url ); ?>">
			<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" height="40" style="height:40px;width:auto;display:inline-block;">
		</a>
	<?php else : ?>
		<a href="<?php echo esc_url( $site_url ); ?>" style="color:#CC0000;font-size:24px;font-weight:bold;text-decoration:none;font-family:Arial,sans-serif;">
			<?php echo esc_html( $site_name ); ?>
		</a>
	<?php endif; ?>
</td>
</tr>

<!-- Red accent line -->
<tr>
<td style="background-color:#CC0000;height:4px;font-size:0;line-height:0;">&nbsp;</td>
</tr>

<!-- Body -->
<tr>
<td style="background-color:#ffffff;padding:40px 32px;">
	<?php if ( $subject ) : ?>
		<h1 style="margin:0 0 24px;font-size:22px;font-weight:bold;color:#111111;font-family:Arial,sans-serif;"><?php echo esc_html( $subject ); ?></h1>
	<?php endif; ?>
	<div style="font-size:15px;line-height:1.7;color:#333333;font-family:Arial,sans-serif;">
		<?php echo $body_html; ?>
	</div>
</td>
</tr>

<!-- Footer -->
<tr>
<td style="background-color:#111111;padding:32px;border-radius:0 0 8px 8px;">
	<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
	<tr>
	<td style="font-size:13px;line-height:1.8;font-family:Arial,sans-serif;">
		<strong style="color:#ffffff;font-size:15px;"><?php echo esc_html( $site_name ); ?></strong><br>
		<?php if ( $address ) : ?>
			<span style="color:#aaaaaa;"><?php echo esc_html( $address ); ?></span><br>
		<?php endif; ?>
		<?php if ( $phone ) : ?>
			<span style="color:#aaaaaa;">Phone: </span><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>" style="color:#ffffff;text-decoration:none;"><?php echo esc_html( $phone ); ?></a><br>
		<?php endif; ?>
		<?php if ( $email ) : ?>
			<span style="color:#aaaaaa;">Email: </span><a href="mailto:<?php echo esc_attr( $email ); ?>" style="color:#CC0000;text-decoration:none;"><?php echo esc_html( $email ); ?></a><br>
		<?php endif; ?>
		<br>
		<a href="<?php echo esc_url( $site_url ); ?>" style="color:#CC0000;text-decoration:none;font-weight:bold;"><?php echo esc_html( str_replace( [ 'https://', 'http://' ], '', $site_url ) ); ?></a>
	</td>
	</tr>
	<tr>
	<td style="padding-top:20px;border-top:1px solid rgba(255,255,255,0.1);margin-top:20px;">
		<p style="color:rgba(255,255,255,0.4);font-size:11px;margin:0;font-family:Arial,sans-serif;">
			&copy; <?php echo esc_html( $year ); ?> <?php echo esc_html( $site_name ); ?>. All rights reserved.
		</p>
	</td>
	</tr>
	</table>
</td>
</tr>

</table>
<!-- /Container -->

</td></tr>
</table>
</body>
</html>
	<?php
	return ob_get_clean();
}

// Override all WordPress emails to use HTML template
add_filter( 'wp_mail', function ( array $args ): array {
	if ( strpos( $args['message'], '<html' ) !== false ) {
		return $args;
	}

	$is_quest_email = false;
	$site_name = get_bloginfo( 'name' );

	if ( is_string( $args['subject'] ) && strpos( $args['subject'], "[$site_name]" ) !== false ) {
		$is_quest_email = true;
	}

	if ( ! $is_quest_email ) {
		return $args;
	}

	$subject_clean = $args['subject'];
	$subject_clean = preg_replace( '/^\[.*?\]\s*/', '', $subject_clean );

	$args['message'] = quest_email_wrap( $args['message'], $subject_clean );

	if ( ! isset( $args['headers'] ) || ! is_array( $args['headers'] ) ) {
		$args['headers'] = is_string( $args['headers'] ?? '' ) ? [ $args['headers'] ] : [];
	}

	$has_content_type = false;
	foreach ( $args['headers'] as $h ) {
		if ( stripos( $h, 'Content-Type' ) !== false ) {
			$has_content_type = true;
			break;
		}
	}

	if ( ! $has_content_type ) {
		$args['headers'][] = 'Content-Type: text/html; charset=UTF-8';
	}

	return $args;
} );
