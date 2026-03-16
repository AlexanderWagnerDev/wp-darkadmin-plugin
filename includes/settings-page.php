<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the settings page.
 */
function adm_settings_page(): void {
	$enabled     = (bool) get_option( 'adm_dark_mode_enabled', false );
	$auto_darken = (bool) get_option( 'adm_auto_darken', false );
	$colors      = wp_parse_args( (array) get_option( 'adm_colors', [] ), adm_default_colors() );
	$custom      = get_option( 'adm_custom_css', '' );
	$allowed     = (array) get_option( 'adm_allowed_users', [] );
	$allowed     = array_map( 'intval', $allowed );
	$active_preset = get_option( 'adm_preset', 'default' );

	if ( $enabled ) {
		echo '<script>document.body.classList.add("adm-dark-active");</script>';
	}

	$var_map  = adm_css_variable_map();
	$defaults = adm_default_colors();
	$presets  = adm_preset_colors();

	$color_groups = [];
	foreach ( $var_map as $key => $info ) {
		$color_groups[ $info['group'] ][ $key ] = $info['label'];
	}

	$grouped_vars = [];
	foreach ( $var_map as $key => $info ) {
		$grouped_vars[ $info['group'] ][] = [ 'key' => $key, 'info' => $info ];
	}

	$selectable_users = adm_get_selectable_users();

	$preset_meta = [
		'default' => [
			'label'    => __( 'Default', 'darkadmin-dark-mode-for-adminpanel' ),
			'desc'     => __( 'Classic WP 6.x dark theme', 'darkadmin-dark-mode-for-adminpanel' ),
			'bg'       => '#1d2327',
			'surface'  => '#2c3338',
			'primary'  => '#2271b1',
			'text'     => '#dcdcde',
		],
		'modern' => [
			'label'    => __( 'Modern', 'darkadmin-dark-mode-for-adminpanel' ),
			'desc'     => __( 'WP 7.0 deep blue, high contrast', 'darkadmin-dark-mode-for-adminpanel' ),
			'bg'       => '#0a0e17',
			'surface'  => '#111827',
			'primary'  => '#3b82f6',
			'text'     => '#e2e8f0',
		],
	];
	?>
	<div class="wrap adm-settings-wrap">

		<div class="adm-page-header">
			<div class="adm-page-header-inner">
				<span class="adm-header-icon dashicons dashicons-visibility"></span>
				<div>
					<h1 class="adm-page-title"><?php esc_html_e( 'DarkAdmin', 'darkadmin-dark-mode-for-adminpanel' ); ?></h1>
					<p class="adm-page-subtitle">
						<?php esc_html_e( 'Dark theme for the WordPress backend', 'darkadmin-dark-mode-for-adminpanel' ); ?>
						&mdash; v<?php echo esc_html( ADM_VERSION ); ?>
					</p>
				</div>
			</div>
			<div class="adm-status-badge <?php echo $enabled ? 'adm-status-active' : 'adm-status-inactive'; ?>">
				<span class="adm-status-dot"></span>
				<?php echo $enabled
					? esc_html__( 'Active', 'darkadmin-dark-mode-for-adminpanel' )
					: esc_html__( 'Inactive', 'darkadmin-dark-mode-for-adminpanel' ); ?>
			</div>
		</div>

		<form method="post" action="options.php">
			<?php settings_fields( 'adm_settings' ); ?>

			<!-- General Settings -->
			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-admin-settings"></span>
					<h2><?php esc_html_e( 'General', 'darkadmin-dark-mode-for-adminpanel' ); ?></h2>
				</div>
				<div class="adm-card-body">

					<div class="adm-field-row">
						<div class="adm-field-info">
							<label for="adm_dark_mode_enabled" class="adm-field-title">
								<?php esc_html_e( 'Enable Dark Mode', 'darkadmin-dark-mode-for-adminpanel' ); ?>
							</label>
							<span class="adm-field-desc">
								<?php esc_html_e( 'Enables the dark theme for all admin pages. Administrators always see dark mode when this is active.', 'darkadmin-dark-mode-for-adminpanel' ); ?>
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
								<?php esc_html_e( 'Auto Dark Mode', 'darkadmin-dark-mode-for-adminpanel' ); ?>
							</label>
							<span class="adm-field-desc">
								<?php esc_html_e( 'Automatically darkens bright backgrounds and lightens dark text from unknown plugins. Requires Dark Mode to be active.', 'darkadmin-dark-mode-for-adminpanel' ); ?>
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

			<!-- Preset Themes -->
			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-admin-appearance"></span>
					<h2><?php esc_html_e( 'Preset Themes', 'darkadmin-dark-mode-for-adminpanel' ); ?></h2>
				</div>
				<div class="adm-card-body">
					<p class="adm-card-description">
						<?php esc_html_e( 'Choose a preset to load its color palette. You can further customize colors below after loading.', 'darkadmin-dark-mode-for-adminpanel' ); ?>
					</p>
					<div class="adm-preset-grid">
						<?php foreach ( $preset_meta as $slug => $meta ) : ?>
							<div class="adm-preset-tile <?php echo $active_preset === $slug ? 'adm-preset-active' : ''; ?>" data-preset="<?php echo esc_attr( $slug ); ?>">
								<div class="adm-preset-swatch" style="background:<?php echo esc_attr( $meta['bg'] ); ?>;">
									<span class="adm-preset-swatch-surface" style="background:<?php echo esc_attr( $meta['surface'] ); ?>;"></span>
									<span class="adm-preset-swatch-accent" style="background:<?php echo esc_attr( $meta['primary'] ); ?>;"></span>
									<span class="adm-preset-swatch-text" style="color:<?php echo esc_attr( $meta['text'] ); ?>;"><?php echo esc_html( $meta['label'] ); ?></span>
								</div>
								<div class="adm-preset-info">
									<strong><?php echo esc_html( $meta['label'] ); ?></strong>
									<span><?php echo esc_html( $meta['desc'] ); ?></span>
								</div>
								<button type="button" class="button adm-preset-load-btn" data-preset="<?php echo esc_attr( $slug ); ?>">
									<?php echo $active_preset === $slug
										? esc_html__( '✓ Active', 'darkadmin-dark-mode-for-adminpanel' )
										: esc_html__( 'Load Preset', 'darkadmin-dark-mode-for-adminpanel' ); ?>
								</button>
							</div>
						<?php endforeach; ?>
					</div>
					<input type="hidden" id="adm_preset" name="adm_preset" value="<?php echo esc_attr( $active_preset ); ?>" />
				</div>
			</div>

			<!-- User Access -->
			<?php if ( ! empty( $selectable_users ) ) : ?>
			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-groups"></span>
					<h2><?php esc_html_e( 'User Access', 'darkadmin-dark-mode-for-adminpanel' ); ?></h2>
				</div>
				<div class="adm-card-body">
					<p class="adm-card-description">
						<?php esc_html_e(
							'Select which non-admin users should see dark mode. Administrators always have dark mode active. Leave all unchecked to apply dark mode to all users.',
							'darkadmin-dark-mode-for-adminpanel'
						); ?>
					</p>
					<div class="adm-user-grid">
						<?php foreach ( $selectable_users as $user ) : ?>
							<label class="adm-user-item">
								<input
									type="checkbox"
									name="adm_allowed_users[]"
									value="<?php echo esc_attr( $user->ID ); ?>"
									<?php checked( in_array( $user->ID, $allowed, true ) ); ?>
								/>
								<span class="adm-user-avatar"><?php echo get_avatar( $user->ID, 28 ); ?></span>
								<span class="adm-user-info">
									<strong><?php echo esc_html( $user->display_name ); ?></strong>
									<span><?php echo esc_html( $user->user_login ); ?> &middot; <?php echo esc_html( implode( ', ', $user->roles ) ); ?></span>
								</span>
							</label>
						<?php endforeach; ?>
					</div>
					<p class="adm-field-desc" style="margin-top:10px;">
						<span class="dashicons dashicons-info" style="font-size:14px;width:14px;height:14px;vertical-align:middle;"></span>
						<?php esc_html_e( 'Administrators are not listed here — they always have dark mode active.', 'darkadmin-dark-mode-for-adminpanel' ); ?>
					</p>
				</div>
			</div>
			<?php endif; ?>

			<!-- Color Customization -->
			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-color-picker"></span>
					<h2><?php esc_html_e( 'Color Customization', 'darkadmin-dark-mode-for-adminpanel' ); ?></h2>
				</div>
				<div class="adm-card-body">
					<p class="adm-card-description">
						<?php esc_html_e( 'Customize every color token individually. Changes are previewed live.', 'darkadmin-dark-mode-for-adminpanel' ); ?>
					</p>

					<div class="adm-palette-actions">
						<button type="button" id="adm-export-colors" class="button">
							<span class="dashicons dashicons-download"></span>
							<?php esc_html_e( 'Export Palette', 'darkadmin-dark-mode-for-adminpanel' ); ?>
						</button>
						<label class="button adm-import-label">
							<span class="dashicons dashicons-upload"></span>
							<?php esc_html_e( 'Import Palette', 'darkadmin-dark-mode-for-adminpanel' ); ?>
							<input type="file" id="adm-import-file" accept=".json" style="display:none;" />
						</label>
						<span id="adm-import-status" class="adm-import-status"></span>
					</div>

					<?php foreach ( $color_groups as $group_name => $group_keys ) : ?>
						<div class="adm-color-group">
							<h3 class="adm-color-group-title"><?php echo esc_html( $group_name ); ?></h3>
							<div class="adm-color-grid">
								<?php foreach ( $group_keys as $key => $label ) : ?>
									<div class="adm-color-item">
										<label class="adm-color-label" for="adm_color_<?php echo esc_attr( $key ); ?>">
											<?php echo esc_html( $label ); ?>
											<code class="adm-color-var-name"><?php echo esc_html( $var_map[ $key ]['var'] ); ?></code>
										</label>
										<input
											type="text"
											id="adm_color_<?php echo esc_attr( $key ); ?>"
											name="adm_colors[<?php echo esc_attr( $key ); ?>]"
											value="<?php echo esc_attr( $colors[ $key ] ?? $defaults[ $key ] ); ?>"
											class="adm-color-picker"
											data-key="<?php echo esc_attr( $key ); ?>"
											data-default-color="<?php echo esc_attr( $defaults[ $key ] ); ?>"
										/>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>

					<div class="adm-color-reset-row">
						<button type="button" id="adm-reset-colors" class="button">
							<span class="dashicons dashicons-image-rotate"></span>
							<?php esc_html_e( 'Restore Default Colors', 'darkadmin-dark-mode-for-adminpanel' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Custom CSS -->
			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-editor-code"></span>
					<h2><?php esc_html_e( 'Custom CSS', 'darkadmin-dark-mode-for-adminpanel' ); ?></h2>
				</div>
				<div class="adm-card-body">
					<p class="adm-card-description">
						<?php esc_html_e( 'Add your own CSS here, loaded after the dark mode stylesheet. All CSS variables are available.', 'darkadmin-dark-mode-for-adminpanel' ); ?>
					</p>
					<details class="adm-var-reference">
						<summary class="adm-var-reference-summary">
							<span class="dashicons dashicons-editor-code"></span>
							<?php esc_html_e( 'Available CSS Variables', 'darkadmin-dark-mode-for-adminpanel' ); ?>
							<span class="adm-var-count"><?php echo count( $var_map ); ?></span>
						</summary>
						<?php
						$cur_colors = wp_parse_args( (array) get_option( 'adm_colors', [] ), $defaults );
						foreach ( $grouped_vars as $group_name => $entries ) :
						?>
							<div class="adm-var-group">
								<h4 class="adm-var-group-title"><?php echo esc_html( $group_name ); ?></h4>
								<div class="adm-var-grid">
									<?php foreach ( $entries as $entry ) :
										$key           = $entry['key'];
										$info          = $entry['info'];
										$current_color = sanitize_hex_color( $cur_colors[ $key ] ?? '' ) ?: $defaults[ $key ];
									?>
										<div class="adm-var-item">
											<span class="adm-var-swatch" style="background:<?php echo esc_attr( $current_color ); ?>;"></span>
											<div class="adm-var-info">
												<button type="button" class="adm-var-copy" data-var="<?php echo esc_attr( $info['var'] ); ?>" title="<?php esc_attr_e( 'Click to copy', 'darkadmin-dark-mode-for-adminpanel' ); ?>">
													<code><?php echo esc_html( $info['var'] ); ?></code>
													<span class="adm-var-copy-icon dashicons dashicons-clipboard"></span>
												</button>
												<span class="adm-var-label"><?php echo esc_html( $info['label'] ); ?></span>
											</div>
											<span class="adm-var-hex"><?php echo esc_html( $current_color ); ?></span>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</details>
					<div class="adm-css-editor-wrap">
						<textarea
							id="adm_custom_css"
							name="adm_custom_css"
							class="adm-css-editor"
							rows="12"
							spellcheck="false"
							placeholder="/* Your custom CSS here */"
						><?php echo esc_textarea( $custom ); ?></textarea>
					</div>
				</div>
			</div>

			<div class="adm-submit-row">
				<?php submit_button( __( 'Save Settings', 'darkadmin-dark-mode-for-adminpanel' ), 'primary', 'submit', false ); ?>
			</div>
		</form>

		<div class="adm-footer">
			<p>
				<?php esc_html_e( 'DarkAdmin', 'darkadmin-dark-mode-for-adminpanel' ); ?> &ndash;
				<a href="https://alexanderwagnerdev.com" target="_blank" rel="noopener">AlexanderWagnerDev</a>
				&mdash;
				<a href="https://github.com/AlexanderWagnerDev/wp-darkadmin-plugin" target="_blank" rel="noopener">GitHub</a>
			</p>
		</div>
	</div>
	<?php
}
