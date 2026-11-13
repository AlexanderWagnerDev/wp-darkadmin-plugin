<?php
/**
 * Dark mode for the login screen (wp-login.php).
 *
 * @package DarkAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the login screen should be dark.
 *
 * Nobody is logged in on wp-login.php, so the user access settings do not
 * apply: the option affects every visitor of the login screen.
 *
 * @return bool
 */
function darkadmin_is_login_dark_mode_enabled(): bool {
	return darkadmin_sanitize_bool( get_option( 'darkadmin_dark_mode_enabled', false ) )
		&& darkadmin_sanitize_bool( get_option( 'darkadmin_login_page', false ) );
}

/**
 * Enqueues the dark login stylesheet with the current palette.
 *
 * @return void
 */
function darkadmin_enqueue_login_styles(): void {
	if ( ! darkadmin_is_login_dark_mode_enabled() ) {
		return;
	}

	$preset = darkadmin_normalize_preset_slug( darkadmin_read_string_option( 'darkadmin_preset', DARKADMIN_DEFAULT_PRESET ) );
	$vars   = darkadmin_css_variables_block( $preset );

	wp_enqueue_style(
		'darkadmin-login',
		DARKADMIN_URL . 'assets/css/darkadmin-login.css',
		array( 'login' ),
		DARKADMIN_VERSION . '-' . substr( md5( $vars ), 0, 8 )
	);

	// Values in $vars are sanitized at the source, see darkadmin_css_variables_block().
	wp_add_inline_style( 'darkadmin-login', $vars );

	// The default logo is a dark gray SVG. Swap it for the white variant, but
	// only while the logo still links to WordPress.org: sites with a custom logo
	// almost always change the link as well, and their logo stays untouched.
	$logo = esc_url_raw( admin_url( 'images/wordpress-logo-white.svg' ) );
	wp_add_inline_style(
		'darkadmin-login',
		'body.login h1 a[href*="wordpress.org"]{background-image:none,url("' . $logo . '");opacity:.9;}'
	);
}
add_action( 'login_enqueue_scripts', 'darkadmin_enqueue_login_styles' );
