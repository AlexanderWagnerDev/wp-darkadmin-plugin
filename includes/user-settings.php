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
 * Returns all non-admin users for the user selection UI.
 *
 * @return WP_User[]
 */
function darkadmin_get_selectable_users(): array {
	return get_users(
		array(
			'orderby'      => 'display_name',
			'order'        => 'ASC',
			'role__not_in' => array( 'administrator' ),
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
 * Preserve the stored custom CSS when an unauthorized user saves the settings form.
 *
 * The Settings API updates every option in the settings group and passes null for
 * fields that are not posted. Without this guard, hiding the field from a user
 * without edit_css would erase the existing CSS when that user saves another
 * setting.
 *
 * @param mixed $value     Sanitized new value.
 * @param mixed $old_value Stored value.
 * @return mixed
 */
function darkadmin_preserve_custom_css_on_unauthorized_save( $value, $old_value ) {
	if ( darkadmin_can_edit_custom_css() ) {
		return $value;
	}

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- This filter only decides whether to keep the stored value; core options.php verifies the settings nonce before a save reaches this point.
	$option_page = isset( $_POST['option_page'] ) && is_string( $_POST['option_page'] ) ? sanitize_key( wp_unslash( $_POST['option_page'] ) ) : '';
	$action      = isset( $_POST['action'] ) && is_string( $_POST['action'] ) ? sanitize_key( wp_unslash( $_POST['action'] ) ) : '';
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	if ( 'darkadmin_settings' !== $option_page || 'update' !== $action ) {
		return $value;
	}

	return $old_value;
}
add_filter( 'pre_update_option_darkadmin_custom_css', 'darkadmin_preserve_custom_css_on_unauthorized_save', 10, 2 );
