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

	// The default logo is a dark gray SVG. darkadmin_mark_default_login_logo()
	// adds the class below only when the rendered logo is that default image,
	// so custom logos stay untouched, whatever their link points to.
	$logo = esc_url_raw( admin_url( 'images/wordpress-logo-white.svg' ) );
	wp_add_inline_style(
		'darkadmin-login',
		'body.login h1 a.darkadmin-default-logo{background-image:none,url("' . $logo . '");opacity:.9;}'
	);
}

add_action( 'login_enqueue_scripts', 'darkadmin_enqueue_login_styles' );

/**
 * Marks the login logo when it shows the default WordPress logo.
 *
 * Custom logos are usually set in CSS on the same element and cannot be told
 * apart from the default one in a stylesheet, so the rendered background
 * image decides. Without JavaScript the default logo simply keeps its gray color.
 *
 * @return void
 */
function darkadmin_mark_default_login_logo(): void {
	if ( ! darkadmin_is_login_dark_mode_enabled() ) {
		return;
	}

	// Exact URLs of the core logo files, compared without their query string.
	$core_logos = array(
		admin_url( 'images/wordpress-logo-gray.svg' ),
		admin_url( 'images/wordpress-logo.svg' ),
		admin_url( 'images/w-logo-gray.png' ),
	);

	wp_print_inline_script_tag(
		'(function(){var a=document.querySelector(".login h1 a");if(!a){return;}'
		. 'var core=' . wp_json_encode( array_map( 'esc_url_raw', $core_logos ) ) . ';'
		. 'var re=/url\\(["\']?([^"\')]+)["\']?\\)/g,m,urls=[];'
		. 'while((m=re.exec(getComputedStyle(a).backgroundImage))){urls.push(m[1].split("?")[0]);}'
		. 'if(urls.length&&urls.every(function(u){return core.indexOf(u)!==-1;})){a.classList.add("darkadmin-default-logo");}})();'
	);
}
add_action( 'login_footer', 'darkadmin_mark_default_login_logo' );
