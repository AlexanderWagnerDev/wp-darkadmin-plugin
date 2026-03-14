<?php
/**
 * Plugin Name: DarkAdmin - Dark Mode for Adminpanel
 * Plugin URI: https://github.com/AlexanderWagnerDev/wp-admin-dark-mode-plugin
 * Description: Simple, lightweight Dark Mode Plugin for the WordPress Admin Dashboard.
 * Version: 0.0.5
 * Requires at least: 6.0
 * Tested up to: 6.9.4
 * Requires PHP: 7.4
 * Author: AlexanderWagnerDev
 * Author URI: https://alexanderwagnerdev.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: darkadmin
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ADM_VERSION', '0.0.5' );
define( 'ADM_URL', plugin_dir_url( __FILE__ ) );

/**
 * Default color palette.
 * Keys map 1:1 to CSS custom properties in darkadmin-dark.css (:root).
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
		'darkadmin',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
} );

/**
 * Enqueue dark mode CSS and inject color token overrides as inline CSS variables.
 * Also enqueues auto-darken JS when both dark mode and auto-darken are enabled.
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

	$vars = ':root{'                                       .
		"--adm-bg:{$sc('bg','#1d2327')};"               .
		"--adm-surface-1:{$sc('surface1','#2c3338')};"  .
		"--adm-surface-2:{$sc('surface2','#32393f')};"  .
		"--adm-surface-3:{$sc('surface3','#3c434a')};"  .
		"--adm-border:{$sc('border','#3c434a')};"        .
		"--adm-text:{$sc('text','#dcdcde')};"            .
		"--adm-text-muted:{$sc('text_muted','#a7aaad')};" .
		"--adm-text-soft:{$sc('text_soft','#787c82')};"  .
		"--adm-link:{$sc('link','#72aee6')};"            .
		"--adm-primary:{$sc('primary','#2271b1')};"      .
		"--adm-success:{$sc('success','#00a32a')};"      .
		"--adm-warning:{$sc('warning','#dba617')};"      .
		"--adm-danger:{$sc('danger','#d63638')};"        .
		'}';

	wp_enqueue_style(
		'adm-darkmode',
		ADM_URL . 'assets/css/darkadmin-dark.css',
		[],
		ADM_VERSION
	);
	wp_add_inline_style( 'adm-darkmode', $vars );

	$custom = get_option( 'adm_custom_css', '' );
	if ( ! empty( $custom ) ) {
		wp_add_inline_style( 'adm-darkmode', wp_strip_all_tags( $custom ) );
	}

	if ( get_option( 'adm_auto_darken', false ) ) {
		wp_enqueue_script(
			'adm-auto-darken',
			ADM_URL . 'assets/js/auto-darken.js',
			[],
			ADM_VERSION,
			false
		);
	}
} );

/**
 * Register the settings menu page.
 */
add_action( 'admin_menu', function () {
	add_options_page(
		__( 'DarkAdmin', 'darkadmin' ),
		__( 'DarkAdmin', 'darkadmin' ),
		'manage_options',
		'darkadmin',
		'adm_settings_page'
	);
} );

/**
 * Enqueue settings page assets (color picker + settings CSS/JS).
 */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( $hook !== 'settings_page_darkadmin' ) {
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
 * Register plugin settings.
 */
add_action( 'admin_init', function () {
	register_setting( 'adm_settings', 'adm_dark_mode_enabled', [
		'type'              => 'boolean',
		'sanitize_callback' => fn( $v ) => (bool) $v,
		'default'           => false,
	] );
	register_setting( 'adm_settings', 'adm_auto_darken', [
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

/**
 * Sanitize and validate the color palette input.
 */
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
 * Render the settings page.
 * All styles are in assets/css/settings.css.
 */
function adm_settings_page() {
	$enabled     = (bool) get_option( 'adm_dark_mode_enabled', false );
	$auto_darken = (bool) get_option( 'adm_auto_darken', false );
	$colors      = wp_parse_args( (array) get_option( 'adm_colors', [] ), adm_default_colors() );
	$custom      = get_option( 'adm_custom_css', '' );

	if ( $enabled ) {
		echo '<script>document.body.classList.add("adm-dark-active");</script>';
	}

	$color_labels = [
		'bg'         => __( 'Background (Base)', 'darkadmin' ),
		'surface1'   => __( 'Surface 1 (Cards / Tables)', 'darkadmin' ),
		'surface2'   => __( 'Surface 2 (Inputs)', 'darkadmin' ),
		'surface3'   => __( 'Surface 3 (Hover)', 'darkadmin' ),
		'border'     => __( 'Border Color', 'darkadmin' ),
		'text'       => __( 'Primary Text', 'darkadmin' ),
		'text_muted' => __( 'Muted Text', 'darkadmin' ),
		'text_soft'  => __( 'Soft Text (Row Actions)', 'darkadmin' ),
		'link'       => __( 'Link Color', 'darkadmin' ),
		'primary'    => __( 'Primary / Buttons', 'darkadmin' ),
		'success'    => __( 'Success', 'darkadmin' ),
		'warning'    => __( 'Warning', 'darkadmin' ),
		'danger'     => __( 'Danger / Error', 'darkadmin' ),
	];
	?>
	<div class="wrap adm-settings-wrap">

		<div class="adm-page-header">
			<div class="adm-page-header-inner">
				<span class="adm-header-icon dashicons dashicons-visibility"></span>
				<div>
					<h1 class="adm-page-title"><?php esc_html_e( 'DarkAdmin', 'darkadmin' ); ?></h1>
					<p class="adm-page-subtitle">
						<?php esc_html_e( 'Dark theme for the WordPress backend', 'darkadmin' ); ?>
						&mdash; v<?php echo esc_html( ADM_VERSION ); ?>
					</p>
				</div>
			</div>
			<div class="adm-status-badge <?php echo $enabled ? 'adm-status-active' : 'adm-status-inactive'; ?>">
				<span class="adm-status-dot"></span>
				<?php echo $enabled
					? esc_html__( 'Active', 'darkadmin' )
					: esc_html__( 'Inactive', 'darkadmin' ); ?>
			</div>
		</div>

		<form method="post" action="options.php">
			<?php settings_fields( 'adm_settings' ); ?>

			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-admin-settings"></span>
					<h2><?php esc_html_e( 'General', 'darkadmin' ); ?></h2>
				</div>
				<div class="adm-card-body">

					<div class="adm-field-row">
						<div class="adm-field-info">
							<label for="adm_dark_mode_enabled" class="adm-field-title">
								<?php esc_html_e( 'Enable Dark Mode', 'darkadmin' ); ?>
							</label>
							<span class="adm-field-desc">
								<?php esc_html_e( 'Enables the dark theme for all admin pages.', 'darkadmin' ); ?>
							</span>
						</div>
						<label class="adm-toggle">
							<input type="checkbox" id="adm_dark_mode_enabled" name="adm_dark_mode_enabled" value="1"
								<?php checked( true, $enabled ); ?> />
							<span class="adm-slider" aria-hidden="true"></span>
						</label>
					</div>

					<hr class="adm-field-divider" />

					<div class="adm-field-row">
						<div class="adm-field-info">
							<label for="adm_auto_darken" class="adm-field-title">
								<?php esc_html_e( 'Auto Dark Mode', 'darkadmin' ); ?>
							</label>
							<span class="adm-field-desc">
								<?php esc_html_e(
									'Automatically darkens bright backgrounds and lightens dark text from unknown plugins. Requires Dark Mode to be active.',
									'darkadmin'
								); ?>
							</span>
						</div>
						<label class="adm-toggle">
							<input type="checkbox" id="adm_auto_darken" name="adm_auto_darken" value="1"
								<?php checked( true, $auto_darken ); ?> />
							<span class="adm-slider" aria-hidden="true"></span>
						</label>
					</div>

				</div>
			</div>

			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-color-picker"></span>
					<h2><?php esc_html_e( 'Color Customization', 'darkadmin' ); ?></h2>
				</div>
				<div class="adm-card-body">
					<p class="adm-card-description">
						<?php esc_html_e(
							'Customize every color token individually. Changes are applied globally via CSS variables.',
							'darkadmin'
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
							<?php esc_html_e( 'Restore Default Colors', 'darkadmin' ); ?>
						</button>
					</div>
				</div>
			</div>

			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-editor-code"></span>
					<h2><?php esc_html_e( 'Custom CSS', 'darkadmin' ); ?></h2>
				</div>
				<div class="adm-card-body">
					<p class="adm-card-description">
						<?php esc_html_e(
							'Add your own CSS here, loaded after the dark mode stylesheet. All CSS variables are available.',
							'darkadmin'
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
				<?php submit_button( __( 'Save Settings', 'darkadmin' ), 'primary', 'submit', false ); ?>
			</div>
		</form>

		<div class="adm-footer">
			<p>
				<?php esc_html_e( 'DarkAdmin', 'darkadmin' ); ?> &ndash;
				<a href="https://alexanderwagnerdev.com" target="_blank" rel="noopener">AlexanderWagnerDev</a>
				&mdash;
				<a href="https://github.com/AlexanderWagnerDev/wp-admin-dark-mode-plugin" target="_blank" rel="noopener">GitHub</a>
			</p>
		</div>

	</div>
	<?php
}

/**
 * Add a settings link in the Plugins list.
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $actions ) {
	$url = admin_url( 'options-general.php?page=darkadmin' );
	$actions['settings'] = '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'darkadmin' ) . '</a>';
	return $actions;
} );

/**
 * Show an admin notice after settings are saved.
 */
add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'manage_options' ) ) return;
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	if ( $page !== 'darkadmin' ) return;
	if ( empty( $_GET['settings-updated'] ) ) return;

	$enabled = (bool) get_option( 'adm_dark_mode_enabled', false );
	$msg = $enabled
		? __( '✓ Dark Mode is active. Settings have been saved.', 'darkadmin' )
		: __( '✓ Settings saved. Dark Mode is disabled.', 'darkadmin' );
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $msg ) . '</p></div>';
} );
