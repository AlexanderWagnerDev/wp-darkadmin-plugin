<?php
/**
 * User-specific dark mode access logic for the DarkAdmin plugin.
 *
 * @package DarkAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determines whether dark mode should be active for the current user.
 *
 * Logic:
 *  1. Global dark mode must be enabled (darkadmin_dark_mode_enabled).
 *  2. Administrators always get dark mode when it's globally enabled.
 *  3. For non-admins the darkadmin_user_access_mode option controls behaviour:
 *     - 'all'     : dark mode applies to everyone (default).
 *     - 'include' : dark mode only for users listed in darkadmin_allowed_users.
 *     - 'exclude' : dark mode for everyone EXCEPT users listed in darkadmin_allowed_users.
 *
 * @return bool
 */
function darkadmin_is_dark_mode_active(): bool {
	if ( ! get_option( 'darkadmin_dark_mode_enabled', false ) ) {
		return false;
	}

	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return false;
	}

	// Administrators always get dark mode when globally enabled.
	if ( current_user_can( 'manage_options' ) ) {
		return true;
	}

	$mode        = darkadmin_sanitize_user_access_mode( get_option( 'darkadmin_user_access_mode', 'all' ) );
	$listed      = array_map( 'strval', array_filter( (array) get_option( 'darkadmin_allowed_users', array() ), 'is_scalar' ) );
	$user_id_str = (string) $user_id;

	switch ( $mode ) {
		case 'include':
			return in_array( $user_id_str, $listed, true );
		case 'exclude':
			return ! in_array( $user_id_str, $listed, true );
		case 'all':
		default:
			return true;
	}
}

/**
 * Returns selectable users for the user selection UI.
 *
 * Users who can manage options always receive dark mode and are therefore
 * excluded; the include/exclude setting cannot affect them.
 *
 * @return WP_User[]
 */
function darkadmin_get_selectable_users(): array {
	$users = get_users(
		array(
			'orderby'      => 'display_name',
			'order'        => 'ASC',
			'role__not_in' => array( 'administrator' ),
		)
	);

	// Anyone with manage_options always receives dark mode (see
	// darkadmin_is_dark_mode_active()), so listing them as selectable would
	// offer an include/exclude control that cannot take effect. This also
	// covers custom roles, individual capabilities and multisite super admins.
	return array_values(
		array_filter(
			$users,
			static function ( $user ): bool {
				return $user instanceof WP_User && ! user_can( $user, 'manage_options' );
			}
		)
	);
}

/**
 * Whether the current user may edit the site-wide custom CSS.
 *
 * WordPress maps edit_css to unfiltered_html. On multisite this is normally
 * limited to super administrators; single-site administrators and editors may
 * have unfiltered_html unless DISALLOW_UNFILTERED_HTML is set.
 *
 * @return bool
 */
function darkadmin_can_edit_custom_css(): bool {
	return current_user_can( 'edit_css' );
}

/**
 * Whether the current request is an options.php save request.
 *
 * Core routes Settings API saves through options.php and reads the routing
 * fields from $_REQUEST (WordPress 7.1+) or POST/GET fallbacks (WordPress 6.3).
 * The legacy "All Settings" screen uses page_options instead of option_page.
 * Checking only the POST settings-page shape leaves the custom-CSS guard
 * bypassable by moving action to the query string or by using that legacy
 * screen, so detect every core options.php save route.
 *
 * @return bool
 */
function darkadmin_is_options_save_request(): bool {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing -- Core options.php verifies the settings nonce before update_option() reaches this filter. This helper only detects the core save route.
	return isset( $_REQUEST['option_page'] )
		|| isset( $_POST['option_page'] )
		|| isset( $_GET['option_page'] )
		|| isset( $_POST['page_options'] );
	// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing
}

/**
 * Preserve the stored custom CSS when an unauthorized user saves options.
 *
 * The Settings API updates every option in the settings group and passes null for
 * fields that are not posted. Without this guard, hiding the field from a user
 * without edit_css would erase the existing CSS when that user saves another
 * setting, and the hidden field could still be submitted directly through
 * options.php.
 *
 * @param mixed $value     Sanitized new value.
 * @param mixed $old_value Stored value.
 * @return mixed
 */
function darkadmin_preserve_custom_css_on_unauthorized_save( $value, $old_value ) {
	if ( darkadmin_can_edit_custom_css() ) {
		return $value;
	}

	if ( ! darkadmin_is_options_save_request() ) {
		return $value;
	}

	return $old_value;
}
add_filter( 'pre_update_option_darkadmin_custom_css', 'darkadmin_preserve_custom_css_on_unauthorized_save', 10, 2 );
