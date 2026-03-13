<?php
/**
 * Plugin Name: WP Admin Dark Mode
 * Plugin URI: https://github.com/AlexanderWagnerDev/wp-admin-dark-mode-plugin
 * Description: Simple, lightweight Dark Mode toggle for the WordPress Admin Dashboard.
 * Version: 0.0.2
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

define( 'ADM_VERSION', '0.0.2' );
define( 'ADM_URL', plugin_dir_url( __FILE__ ) );

/**
 * Default color palette (matches WP sidebar #1d2327 dark scheme).
 */
function adm_default_colors(): array {
	return [
		'bg'          => '#1d2327',
		'card'        => '#2c3338',
		'border'      => '#3c434a',
		'text'        => '#dcdcde',
		'text_muted'  => '#a7aaad',
		'link'        => '#72aee6',
		'primary'     => '#2271b1',
		'success'     => '#00a32a',
		'warning'     => '#d63638',
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
 * Enqueue admin CSS + inline custom CSS when dark mode is enabled.
 */
add_action( 'admin_enqueue_scripts', function () {
	if ( ! get_option( 'adm_dark_mode_enabled', false ) ) {
		return;
	}

	$colors = wp_parse_args(
		(array) get_option( 'adm_colors', [] ),
		adm_default_colors()
	);

	$vars = sprintf(
		':root{--adm-bg:%s;--adm-card:%s;--adm-border:%s;--adm-text:%s;--adm-text-muted:%s;--adm-link:%s;--adm-primary:%s;--adm-success:%s;--adm-warning:%s;}',
		sanitize_hex_color( $colors['bg'] )         ?? '#1d2327',
		sanitize_hex_color( $colors['card'] )        ?? '#2c3338',
		sanitize_hex_color( $colors['border'] )      ?? '#3c434a',
		sanitize_hex_color( $colors['text'] )        ?? '#dcdcde',
		sanitize_hex_color( $colors['text_muted'] )  ?? '#a7aaad',
		sanitize_hex_color( $colors['link'] )        ?? '#72aee6',
		sanitize_hex_color( $colors['primary'] )     ?? '#2271b1',
		sanitize_hex_color( $colors['success'] )     ?? '#00a32a',
		sanitize_hex_color( $colors['warning'] )     ?? '#d63638'
	);

	wp_enqueue_style(
		'adm-darkmode',
		ADM_URL . 'assets/css/wp-admin-dark.css',
		[],
		ADM_VERSION
	);
	wp_add_inline_style( 'adm-darkmode', $vars );

	$custom_css = get_option( 'adm_custom_css', '' );
	if ( ! empty( $custom_css ) ) {
		wp_add_inline_style( 'adm-darkmode', wp_strip_all_tags( $custom_css ) );
	}
} );

/**
 * Settings page (Settings -> WP Admin Dark Mode).
 */
add_action( 'admin_menu', function () {
	add_options_page(
		__( 'WP Admin Dark Mode', 'wp-admin-dark-mode' ),  // Browser-Tab-Titel
		__( 'WP Admin Dark Mode', 'wp-admin-dark-mode' ),  // Sidebar-Label
		'manage_options',
		'wp-admin-dark-mode',
		'adm_settings_page'
	);
} );

/**
 * Enqueue settings page assets.
 */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( $hook !== 'settings_page_wp-admin-dark-mode' ) {
		return;
	}
	wp_enqueue_style( 'wp-color-picker' );
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
 */
function adm_settings_page() {
	$enabled = (bool) get_option( 'adm_dark_mode_enabled', false );
	$colors  = wp_parse_args( (array) get_option( 'adm_colors', [] ), adm_default_colors() );
	$custom  = get_option( 'adm_custom_css', '' );
	$version = ADM_VERSION;

	// Keys = English msgid, translated via .po
	$color_labels = [
		'bg'         => __( 'Background Color (Base)', 'wp-admin-dark-mode' ),
		'card'       => __( 'Card / Content Area', 'wp-admin-dark-mode' ),
		'border'     => __( 'Border Color', 'wp-admin-dark-mode' ),
		'text'       => __( 'Text Color', 'wp-admin-dark-mode' ),
		'text_muted' => __( 'Muted Text Color', 'wp-admin-dark-mode' ),
		'link'       => __( 'Link Color', 'wp-admin-dark-mode' ),
		'primary'    => __( 'Primary Color (Buttons)', 'wp-admin-dark-mode' ),
		'success'    => __( 'Success Color', 'wp-admin-dark-mode' ),
		'warning'    => __( 'Warning Color', 'wp-admin-dark-mode' ),
	];
	?>
	<div class="wrap adm-settings-wrap">

		<div class="adm-page-header">
			<div class="adm-page-header-inner">
				<span class="adm-header-icon dashicons dashicons-visibility"></span>
				<div>
					<h1 class="adm-page-title"><?php esc_html_e( 'WP Admin Dark Mode', 'wp-admin-dark-mode' ); ?></h1>
					<p class="adm-page-subtitle"><?php esc_html_e( 'Dark theme for the WordPress backend', 'wp-admin-dark-mode' ); ?> &mdash; v<?php echo esc_html( $version ); ?></p>
				</div>
			</div>
			<div class="adm-status-badge <?php echo $enabled ? 'adm-status-active' : 'adm-status-inactive'; ?>">
				<span class="adm-status-dot"></span>
				<?php echo $enabled ? esc_html__( 'Active', 'wp-admin-dark-mode' ) : esc_html__( 'Inactive', 'wp-admin-dark-mode' ); ?>
			</div>
		</div>

		<form method="post" action="options.php">
			<?php settings_fields( 'adm_settings' ); ?>

			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-admin-settings"></span>
					<h2><?php esc_html_e( 'General', 'wp-admin-dark-mode' ); ?></h2>
				</div>
				<div class="adm-card-body">
					<div class="adm-field-row">
						<div class="adm-field-info">
							<label for="adm_dark_mode_enabled" class="adm-field-title"><?php esc_html_e( 'Enable Dark Mode', 'wp-admin-dark-mode' ); ?></label>
							<span class="adm-field-desc"><?php esc_html_e( 'Enables the dark theme for all admin pages.', 'wp-admin-dark-mode' ); ?></span>
						</div>
						<label class="adm-toggle">
							<input type="checkbox" id="adm_dark_mode_enabled" name="adm_dark_mode_enabled" value="1" <?php checked( true, $enabled ); ?> />
							<span class="adm-slider" aria-hidden="true"></span>
						</label>
					</div>
				</div>
			</div>

			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-color-picker"></span>
					<h2><?php esc_html_e( 'Color Customization', 'wp-admin-dark-mode' ); ?></h2>
				</div>
				<div class="adm-card-body">
					<p class="adm-card-description"><?php esc_html_e( 'Customize the dark mode colors individually. The defaults are based on the WordPress sidebar palette.', 'wp-admin-dark-mode' ); ?></p>
					<div class="adm-color-grid">
						<?php foreach ( $color_labels as $key => $label ) : ?>
							<div class="adm-color-item">
								<label class="adm-color-label" for="adm_color_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
								<input
									type="text"
									id="adm_color_<?php echo esc_attr( $key ); ?>"
									name="adm_colors[<?php echo esc_attr( $key ); ?>]"
									value="<?php echo esc_attr( $colors[ $key ] ); ?>"
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

			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-editor-code"></span>
					<h2><?php esc_html_e( 'Custom CSS', 'wp-admin-dark-mode' ); ?></h2>
				</div>
				<div class="adm-card-body">
					<p class="adm-card-description"><?php esc_html_e( 'Add your own CSS here, loaded after the dark mode stylesheet. You can use the CSS variables (--adm-bg, --adm-card, etc.) directly.', 'wp-admin-dark-mode' ); ?></p>
					<div class="adm-css-editor-wrap">
						<textarea
							id="adm_custom_css"
							name="adm_custom_css"
							class="adm-css-editor"
							rows="12"
							spellcheck="false"
							placeholder="/* Your custom CSS here ... */
/* Available variables:
   --adm-bg, --adm-card, --adm-border,
   --adm-text, --adm-text-muted, --adm-link,
   --adm-primary, --adm-success, --adm-warning
*/"
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
				<a href="https://alexanderwagnerdev.com" target="_blank" rel="noopener">AlexanderWagnerDev</a> &mdash;
				<a href="https://github.com/AlexanderWagnerDev/wp-admin-dark-mode-plugin" target="_blank" rel="noopener">GitHub</a>
			</p>
		</div>

	</div>

	<style id="adm-settings-page-css">
		.adm-settings-wrap { max-width: 800px; }

		.adm-page-header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 12px;
			padding: 20px 24px;
			background: #fff;
			border: 1px solid #dcdcde;
			border-radius: 12px;
			margin-bottom: 20px;
			box-shadow: 0 1px 3px rgba(0,0,0,.07);
		}
		.adm-page-header-inner { display: flex; align-items: center; gap: 14px; }
		.adm-header-icon { font-size: 32px; width: 32px; height: 32px; color: #2271b1; }
		.adm-page-title { margin: 0 0 2px; padding: 0; font-size: 20px; font-weight: 700; line-height: 1.2; color: #1d2327; }
		.adm-page-subtitle { margin: 0; font-size: 12px; color: #646970; }

		.adm-status-badge {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			padding: 5px 12px;
			border-radius: 999px;
			font-size: 12px;
			font-weight: 600;
			letter-spacing: .4px;
			text-transform: uppercase;
		}
		.adm-status-active   { background: #d4edda; color: #155724; }
		.adm-status-inactive { background: #f8d7da; color: #721c24; }
		.adm-status-dot { width: 8px; height: 8px; border-radius: 50%; background: currentColor; }

		.adm-card {
			background: #fff;
			border: 1px solid #dcdcde;
			border-radius: 12px;
			margin-bottom: 20px;
			box-shadow: 0 1px 3px rgba(0,0,0,.07);
			overflow: hidden;
		}
		.adm-card-header {
			display: flex;
			align-items: center;
			gap: 10px;
			padding: 14px 20px;
			background: #f6f7f7;
			border-bottom: 1px solid #dcdcde;
		}
		.adm-card-header .dashicons { color: #2271b1; font-size: 18px; width: 18px; height: 18px; }
		.adm-card-header h2 { margin: 0; padding: 0; font-size: 14px; font-weight: 600; color: #1d2327; }
		.adm-card-body { padding: 20px; }
		.adm-card-description { margin: 0 0 16px; color: #646970; font-size: 13px; }

		.adm-field-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
		.adm-field-info { display: flex; flex-direction: column; gap: 3px; }
		.adm-field-title { margin: 0; font-size: 14px; font-weight: 600; color: #1d2327; cursor: pointer; }
		.adm-field-desc { font-size: 12px; color: #646970; }

		.adm-toggle { position: relative; width: 54px; height: 30px; flex: 0 0 auto; }
		.adm-toggle input { opacity: 0; width: 0; height: 0; position: absolute; }
		.adm-slider { position: absolute; inset: 0; background: #c3c4c7; border-radius: 999px; transition: background .2s ease; cursor: pointer; }
		.adm-slider::before {
			content: "";
			position: absolute;
			width: 24px; height: 24px;
			left: 3px; top: 3px;
			background: #fff;
			border-radius: 50%;
			transition: transform .2s ease;
			box-shadow: 0 1px 3px rgba(0,0,0,.3);
		}
		.adm-toggle input:checked + .adm-slider { background: #2271b1; }
		.adm-toggle input:checked + .adm-slider::before { transform: translateX(24px); }
		.adm-toggle input:focus-visible + .adm-slider { outline: 2px solid #2271b1; outline-offset: 2px; }

		.adm-color-grid {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
			gap: 16px;
			margin-bottom: 16px;
		}
		.adm-color-item { display: flex; flex-direction: column; gap: 6px; }
		.adm-color-label { font-size: 12px; font-weight: 600; color: #1d2327; }
		.adm-color-reset-row { padding-top: 8px; border-top: 1px solid #dcdcde; }
		.adm-color-reset-row .button { display: inline-flex; align-items: center; gap: 6px; }
		.adm-color-reset-row .dashicons { font-size: 16px; width: 16px; height: 16px; }

		.adm-css-editor-wrap { border: 1px solid #dcdcde; border-radius: 6px; overflow: hidden; }
		.adm-css-editor {
			width: 100%;
			box-sizing: border-box;
			resize: vertical;
			min-height: 200px;
			padding: 12px;
			font-family: Consolas, Monaco, 'Courier New', monospace;
			font-size: 13px;
			line-height: 1.6;
			background: #1e1e2e;
			color: #cdd6f4;
			border: none;
			outline: none;
			display: block;
		}
		.adm-css-editor::placeholder { color: #6c7086; }

		.adm-submit-row { display: flex; gap: 10px; margin-bottom: 16px; }

		.adm-footer { text-align: center; padding: 12px 0 4px; font-size: 12px; color: #646970; }
		.adm-footer a { color: #2271b1; }

		/* Dark Mode: Settings page itself */
		body.wp-admin.adm-dark-active .adm-page-header,
		body.wp-admin.adm-dark-active .adm-card { background: #2c3338; border-color: #3c434a; color: #dcdcde; }
		body.wp-admin.adm-dark-active .adm-card-header { background: #1d2327; border-color: #3c434a; }
		body.wp-admin.adm-dark-active .adm-card-header h2,
		body.wp-admin.adm-dark-active .adm-page-title,
		body.wp-admin.adm-dark-active .adm-field-title,
		body.wp-admin.adm-dark-active .adm-color-label { color: #dcdcde; }
		body.wp-admin.adm-dark-active .adm-page-subtitle,
		body.wp-admin.adm-dark-active .adm-field-desc,
		body.wp-admin.adm-dark-active .adm-card-description,
		body.wp-admin.adm-dark-active .adm-footer { color: #a7aaad; }
		body.wp-admin.adm-dark-active .adm-color-reset-row { border-color: #3c434a; }
		body.wp-admin.adm-dark-active .adm-css-editor-wrap { border-color: #3c434a; }
		body.wp-admin.adm-dark-active .adm-footer a { color: #72aee6; }
	</style>
	<?php
	if ( $enabled ) {
		echo "<script>document.body.classList.add('adm-dark-active');</script>";
	}
}

/**
 * Settings Link in Plugins list.
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

	$enabled = get_option( 'adm_dark_mode_enabled', false );
	$msg = $enabled
		? __( '\u2713 Dark Mode is active. Settings have been saved.', 'wp-admin-dark-mode' )
		: __( '\u2713 Settings saved. Dark Mode is disabled.', 'wp-admin-dark-mode' );
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $msg ) . '</p></div>';
} );
