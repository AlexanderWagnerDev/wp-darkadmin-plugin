<?php
/**
 * Plugin Name: WP Admin Dark Mode
 * Plugin URI: https://github.com/AlexanderWagnerDev/wp-admin-dark-mode-plugin
 * Description: Simple, lightweight Dark Mode Plugin for the WordPress Admin Dashboard.
 * Version: 0.0.3
 * Requires at least: 6.0
 * Tested up to: 6.9.4
 * Requires PHP: 7.4
 * Author: AlexanderWagnerDev
 * Author URI: https://alexanderwagnerdev.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-admin-dark-mode
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ADM_VERSION', '0.0.3' );
define( 'ADM_URL', plugin_dir_url( __FILE__ ) );

/**
 * Default color palette.
 * Keys map 1:1 to CSS custom properties in wp-admin-dark.css (:root).
 */
function adm_default_colors(): array {
	return [
		'bg'         => '#1d2327',
		'surface1'   => '#2c3338',
		'surface2'   => '#32393f',
		'surface3'   => '#3c434a',
		'border'     => '#3c434a',
		'text'       => '#dcdcde',
		'text_muted' => '#a7aaad',
		'text_soft'  => '#787c82',
		'link'       => '#72aee6',
		'primary'    => '#2271b1',
		'success'    => '#00a32a',
		'warning'    => '#dba617',
		'danger'     => '#d63638',
	];
}

/**
 * Load translations.
 */
add_action( 'plugins_loaded', function () {
	load_plugin_textdomain(
		'wp-admin-dark-mode',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
} );

/**
 * Enqueue global dark mode CSS + inject user colour overrides as CSS vars.
 */
add_action( 'admin_enqueue_scripts', function () {
	if ( ! get_option( 'adm_dark_mode_enabled', false ) ) {
		return;
	}

	$c = wp_parse_args(
		(array) get_option( 'adm_colors', [] ),
		adm_default_colors()
	);

	$sc = static fn( string $k, string $fb ) => sanitize_hex_color( $c[ $k ] ?? '' ) ?: $fb;

	$vars = ':root{'                                    .
		"--adm-bg:{$sc('bg','#1d2327')};"            .
		"--adm-surface-1:{$sc('surface1','#2c3338')};" .
		"--adm-surface-2:{$sc('surface2','#32393f')};" .
		"--adm-surface-3:{$sc('surface3','#3c434a')};" .
		"--adm-border:{$sc('border','#3c434a')};"     .
		"--adm-text:{$sc('text','#dcdcde')};"         .
		"--adm-text-muted:{$sc('text_muted','#a7aaad')};" .
		"--adm-text-soft:{$sc('text_soft','#787c82')};"   .
		"--adm-link:{$sc('link','#72aee6')};"         .
		"--adm-primary:{$sc('primary','#2271b1')};"   .
		"--adm-success:{$sc('success','#00a32a')};"   .
		"--adm-warning:{$sc('warning','#dba617')};"   .
		"--adm-danger:{$sc('danger','#d63638')};"     .
		'}';

	wp_enqueue_style(
		'adm-darkmode',
		ADM_URL . 'assets/css/wp-admin-dark.css',
		[],
		ADM_VERSION
	);
	wp_add_inline_style( 'adm-darkmode', $vars );

	$custom = get_option( 'adm_custom_css', '' );
	if ( ! empty( $custom ) ) {
		wp_add_inline_style( 'adm-darkmode', wp_strip_all_tags( $custom ) );
	}
} );

/**
 * Settings menu entry.
 */
add_action( 'admin_menu', function () {
	add_options_page(
		__( 'WP Admin Dark Mode', 'wp-admin-dark-mode' ),
		__( 'WP Admin Dark Mode', 'wp-admin-dark-mode' ),
		'manage_options',
		'wp-admin-dark-mode',
		'adm_settings_page'
	);
} );

/**
 * Enqueue settings page assets (color picker + external CSS).
 */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( $hook !== 'settings_page_wp-admin-dark-mode' ) {
		return;
	}
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style(
		'adm-settings-css',
		ADM_URL . 'assets/css/settings.css',
		[ 'wp-color-picker' ],
		ADM_VERSION
	);
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_script(
		'adm-settings-js',
		ADM_URL . 'assets/js/settings.js',
		[ 'wp-color-picker' ],
		ADM_VERSION,
		true
	);
} );

/**
 * Register settings.
 */
add_action( 'admin_init', function () {
	register_setting( 'adm_settings', 'adm_dark_mode_enabled', [
		'type'              => 'boolean',
		'sanitize_callback' => fn( $v ) => (bool) $v,
		'default'           => false,
	] );
	register_setting( 'adm_settings', 'adm_colors', [
		'type'              => 'array',
		'sanitize_callback' => 'adm_sanitize_colors',
		'default'           => adm_default_colors(),
	] );
	register_setting( 'adm_settings', 'adm_custom_css', [
		'type'              => 'string',
		'sanitize_callback' => fn( $v ) => wp_strip_all_tags( $v ),
		'default'           => '',
	] );
} );

function adm_sanitize_colors( $input ): array {
	$defaults = adm_default_colors();
	$output   = [];
	foreach ( $defaults as $key => $default ) {
		$raw            = $input[ $key ] ?? $default;
		$output[ $key ] = sanitize_hex_color( $raw ) ?: $default;
	}
	return $output;
}

/**
 * Settings page HTML.
 * All styles live in assets/css/settings.css — zero inline <style> here.
 */
function adm_settings_page() {
	$enabled = (bool) get_option( 'adm_dark_mode_enabled', false );
	$colors  = wp_parse_args( (array) get_option( 'adm_colors', [] ), adm_default_colors() );
	$custom  = get_option( 'adm_custom_css', '' );

	if ( $enabled ) {
		echo '<script>document.body.classList.add("adm-dark-active");</script>';
	}

	$color_labels = [
		'bg'         => __( 'Background (Base)', 'wp-admin-dark-mode' ),
		'surface1'   => __( 'Surface 1 (Cards / Tables)', 'wp-admin-dark-mode' ),
		'surface2'   => __( 'Surface 2 (Inputs)', 'wp-admin-dark-mode' ),
		'surface3'   => __( 'Surface 3 (Hover)', 'wp-admin-dark-mode' ),
		'border'     => __( 'Border Color', 'wp-admin-dark-mode' ),
		'text'       => __( 'Primary Text', 'wp-admin-dark-mode' ),
		'text_muted' => __( 'Muted Text', 'wp-admin-dark-mode' ),
		'text_soft'  => __( 'Soft Text (Row Actions)', 'wp-admin-dark-mode' ),
		'link'       => __( 'Link Color', 'wp-admin-dark-mode' ),
		'primary'    => __( 'Primary / Buttons', 'wp-admin-dark-mode' ),
		'success'    => __( 'Success', 'wp-admin-dark-mode' ),
		'warning'    => __( 'Warning', 'wp-admin-dark-mode' ),
		'danger'     => __( 'Danger / Error', 'wp-admin-dark-mode' ),
	];
	?>
	<div class="wrap adm-settings-wrap">

		<!-- Page header -->
		<div class="adm-page-header">
			<div class="adm-page-header-inner">
				<span class="adm-header-icon dashicons dashicons-visibility"></span>
				<div>
					<h1 class="adm-page-title"><?php esc_html_e( 'WP Admin Dark Mode', 'wp-admin-dark-mode' ); ?></h1>
					<p class="adm-page-subtitle">
						<?php esc_html_e( 'Dark theme for the WordPress backend', 'wp-admin-dark-mode' ); ?>
						&mdash; v<?php echo esc_html( ADM_VERSION ); ?>
					</p>
				</div>
			</div>
			<div class="adm-status-badge <?php echo $enabled ? 'adm-status-active' : 'adm-status-inactive'; ?>">
				<span class="adm-status-dot"></span>
				<?php echo $enabled
					? esc_html__( 'Active', 'wp-admin-dark-mode' )
					: esc_html__( 'Inactive', 'wp-admin-dark-mode' ); ?>
			</div>
		</div>

		<form method="post" action="options.php">
			<?php settings_fields( 'adm_settings' ); ?>

			<!-- General -->
			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-admin-settings"></span>
					<h2><?php esc_html_e( 'General', 'wp-admin-dark-mode' ); ?></h2>
				</div>
				<div class="adm-card-body">
					<div class="adm-field-row">
						<div class="adm-field-info">
							<label for="adm_dark_mode_enabled" class="adm-field-title">
								<?php esc_html_e( 'Enable Dark Mode', 'wp-admin-dark-mode' ); ?>
							</label>
							<span class="adm-field-desc">
								<?php esc_html_e( 'Enables the dark theme for all admin pages.', 'wp-admin-dark-mode' ); ?>
							</span>
						</div>
						<label class="adm-toggle">
							<input type="checkbox" id="adm_dark_mode_enabled" name="adm_dark_mode_enabled" value="1"
								<?php checked( true, $enabled ); ?> />
							<span class="adm-slider" aria-hidden="true"></span>
						</label>
					</div>
				</div>
			</div>

			<!-- Colors -->
			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-color-picker"></span>
					<h2><?php esc_html_e( 'Color Customization', 'wp-admin-dark-mode' ); ?></h2>
				</div>
				<div class="adm-card-body">
					<p class="adm-card-description">
						<?php esc_html_e(
							'Customize every color token individually. Changes are applied globally via CSS variables.',
							'wp-admin-dark-mode'
						); ?>
					</p>
					<div class="adm-color-grid">
						<?php foreach ( $color_labels as $key => $label ) : ?>
							<div class="adm-color-item">
								<label class="adm-color-label" for="adm_color_<?php echo esc_attr( $key ); ?>">
									<?php echo esc_html( $label ); ?>
								</label>
								<input
									type="text"
									id="adm_color_<?php echo esc_attr( $key ); ?>"
									name="adm_colors[<?php echo esc_attr( $key ); ?>]"
									value="<?php echo esc_attr( $colors[ $key ] ?? adm_default_colors()[ $key ] ); ?>"
									class="adm-color-picker"
									data-default-color="<?php echo esc_attr( adm_default_colors()[ $key ] ); ?>"
								/>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="adm-color-reset-row">
						<button type="button" id="adm-reset-colors" class="button">
							<span class="dashicons dashicons-image-rotate"></span>
							<?php esc_html_e( 'Restore Default Colors', 'wp-admin-dark-mode' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Custom CSS -->
			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-editor-code"></span>
					<h2><?php esc_html_e( 'Custom CSS', 'wp-admin-dark-mode' ); ?></h2>
				</div>
				<div class="adm-card-body">
					<p class="adm-card-description">
						<?php esc_html_e(
							'Add your own CSS here, loaded after the dark mode stylesheet. All CSS variables are available.',
							'wp-admin-dark-mode'
						); ?>
					</p>
					<div class="adm-css-editor-wrap">
						<textarea
							id="adm_custom_css"
							name="adm_custom_css"
							class="adm-css-editor"
							rows="12"
							spellcheck="false"
							placeholder="/* Your custom CSS here */
/* Variables: --adm-bg, --adm-surface-1, --adm-surface-2, --adm-surface-3,
   --adm-border, --adm-text, --adm-text-muted, --adm-text-soft,
   --adm-link, --adm-primary, --adm-success, --adm-warning, --adm-danger */"
						><?php echo esc_textarea( $custom ); ?></textarea>
					</div>
				</div>
			</div>

			<div class="adm-submit-row">
				<?php submit_button( __( 'Save Settings', 'wp-admin-dark-mode' ), 'primary', 'submit', false ); ?>
			</div>
		</form>

		<div class="adm-footer">
			<p>
				<?php esc_html_e( 'WP Admin Dark Mode', 'wp-admin-dark-mode' ); ?> &ndash;
				<a href="https://alexanderwagnerdev.com" target="_blank" rel="noopener">AlexanderWagnerDev</a>
				&mdash;
				<a href="https://github.com/AlexanderWagnerDev/wp-admin-dark-mode-plugin" target="_blank" rel="noopener">GitHub</a>
			</p>
		</div>

	</div>
	<?php
}

/**
 * Settings link in Plugins list.
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $actions ) {
	$url = admin_url( 'options-general.php?page=wp-admin-dark-mode' );
	$actions['settings'] = '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'wp-admin-dark-mode' ) . '</a>';
	return $actions;
} );

/**
 * Admin notice after saving.
 */
add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'manage_options' ) ) return;
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	if ( $page !== 'wp-admin-dark-mode' ) return;
	if ( empty( $_GET['settings-updated'] ) ) return;

	$enabled = (bool) get_option( 'adm_dark_mode_enabled', false );
	$msg = $enabled
		? __( '✓ Dark Mode is active. Settings have been saved.', 'wp-admin-dark-mode' )
		: __( '✓ Settings saved. Dark Mode is disabled.', 'wp-admin-dark-mode' );
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $msg ) . '</p></div>';
} );
