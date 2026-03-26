<?php
/**
 * Settings page output for the DarkAdmin plugin.
 *
 * @package DarkAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the DarkAdmin settings page.
 *
 * @return void
 */
function darkadmin_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'darkadmin-dark-mode-for-adminpanel' ) );
	}

	$enabled          = (bool) get_option( 'darkadmin_dark_mode_enabled', false );
	$auto_darken      = (bool) get_option( 'darkadmin_auto_darken', false );
	$colors           = wp_parse_args( (array) get_option( 'darkadmin_colors', array() ), darkadmin_default_colors() );
	$layout           = wp_parse_args( (array) get_option( 'darkadmin_layout', array() ), darkadmin_default_layout() );
	$custom           = get_option( 'darkadmin_custom_css', '' );
	$allowed          = array_map( 'intval', (array) get_option( 'darkadmin_allowed_users', array() ) );
	$user_access_mode = get_option( 'darkadmin_user_access_mode', 'all' );
	$active_preset    = get_option( 'darkadmin_preset', 'default' );
	$excluded_pages   = get_option( 'darkadmin_excluded_pages', '' );

	$var_map         = darkadmin_css_variable_map();
	$defaults        = darkadmin_default_colors();
	$presets         = darkadmin_preset_colors();
	$layout_map      = darkadmin_layout_variable_map();
	$layout_defaults = darkadmin_default_layout();

	$color_groups = array();
	$grouped_vars = array();
	foreach ( $var_map as $key => $info ) {
		$color_groups[ $info['group'] ][ $key ] = $info['label'];
		$grouped_vars[ $info['group'] ][]        = array(
			'key'  => $key,
			'info' => $info,
		);
	}

	$selectable_users = darkadmin_get_selectable_users();
	$has_users        = ! empty( $selectable_users );

	$preset_meta = array(
		'default' => array(
			'label'   => __( 'Default', 'darkadmin-dark-mode-for-adminpanel' ),
			'desc'    => __( 'Classic WP 6.x dark theme', 'darkadmin-dark-mode-for-adminpanel' ),
			'bg'      => '#1d2327',
			'surface' => '#2c3338',
			'primary' => '#2271b1',
			'text'    => '#dcdcde',
			'bar'     => '#1a1f24',
		),
		'modern'  => array(
			'label'   => __( 'Modern', 'darkadmin-dark-mode-for-adminpanel' ),
			'desc'    => __( 'WP Modern design language (dark)', 'darkadmin-dark-mode-for-adminpanel' ),
			'bg'      => '#1e1e1e',
			'surface' => '#2a2a2a',
			'primary' => '#3858e9',
			'text'    => '#f0f0f0',
			'bar'     => '#0c0c0c',
		),
	);

	$allowed_avatar_tags = array(
		'img' => array(
			'src'      => array(),
			'srcset'   => array(),
			'alt'      => array(),
			'width'    => array(),
			'height'   => array(),
			'class'    => array(),
			'style'    => array(),
			'loading'  => array(),
			'decoding' => array(),
		),
	);
	?>
	<div class="wrap adm-settings-wrap">

		<div class="adm-page-header">
			<div class="adm-page-header-inner">
				<span class="adm-header-icon dashicons dashicons-visibility"></span>
				<div>
					<h1 class="adm-page-title"><?php esc_html_e( 'DarkAdmin', 'darkadmin-dark-mode-for-adminpanel' ); ?></h1>
					<p class="adm-page-subtitle">
						<?php esc_html_e( 'Dark theme for the WordPress backend', 'darkadmin-dark-mode-for-adminpanel' ); ?>
						&mdash; v<?php echo esc_html( DARKADMIN_VERSION ); ?>
					</p>
				</div>
			</div>
			<div class="adm-status-badge <?php echo esc_attr( $enabled ? 'adm-status-active' : 'adm-status-inactive' ); ?>">
				<span class="adm-status-dot"></span>
				<?php
				echo $enabled
					? esc_html__( 'Active', 'darkadmin-dark-mode-for-adminpanel' )
					: esc_html__( 'Inactive', 'darkadmin-dark-mode-for-adminpanel' );
				?>
			</div>
		</div>

		<form method="post" action="options.php">
			<?php settings_fields( 'darkadmin_settings' ); ?>

			<!-- General Settings -->
			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-admin-settings"></span>
					<h2><?php esc_html_e( 'General', 'darkadmin-dark-mode-for-adminpanel' ); ?></h2>
				</div>
				<div class="adm-card-body">

					<div class="adm-field-row">
						<div class="adm-field-info">
							<label for="darkadmin_dark_mode_enabled" class="adm-field-title">
								<?php esc_html_e( 'Enable Dark Mode', 'darkadmin-dark-mode-for-adminpanel' ); ?>
							</label>
							<span class="adm-field-desc">
								<?php esc_html_e( 'Enables the dark theme for all admin pages. Administrators always see dark mode when this is active.', 'darkadmin-dark-mode-for-adminpanel' ); ?>
							</span>
						</div>
						<label class="adm-toggle">
							<input type="checkbox" id="darkadmin_dark_mode_enabled" name="darkadmin_dark_mode_enabled" value="1"
								<?php checked( true, $enabled ); ?> />
							<span class="adm-slider" aria-hidden="true"></span>
						</label>
					</div>

					<hr class="adm-field-divider" />

					<div class="adm-field-row">
						<div class="adm-field-info">
							<label for="darkadmin_auto_darken" class="adm-field-title">
								<?php esc_html_e( 'Auto Dark Mode', 'darkadmin-dark-mode-for-adminpanel' ); ?>
							</label>
							<span class="adm-field-desc">
								<?php esc_html_e( 'Automatically darkens bright backgrounds and lightens dark text from unknown plugins. Requires Dark Mode to be active.', 'darkadmin-dark-mode-for-adminpanel' ); ?>
							</span>
						</div>
						<label class="adm-toggle">
							<input type="checkbox" id="darkadmin_auto_darken" name="darkadmin_auto_darken" value="1"
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

					<script id="adm-preset-meta" type="application/json"><?php echo wp_json_encode( $preset_meta, JSON_HEX_TAG | JSON_HEX_AMP ); ?></script>

					<div class="adm-preset-layout">

						<div class="adm-preset-grid">
							<?php foreach ( $preset_meta as $slug => $meta ) : ?>
								<div class="adm-preset-tile <?php echo esc_attr( $active_preset === $slug ? 'adm-preset-active' : '' ); ?>" data-preset="<?php echo esc_attr( $slug ); ?>">
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
										<?php
										echo $active_preset === $slug
											? esc_html__( '&#x2713; Active', 'darkadmin-dark-mode-for-adminpanel' )
											: esc_html__( 'Load Preset', 'darkadmin-dark-mode-for-adminpanel' );
										?>
									</button>
								</div>
							<?php endforeach; ?>
						</div>

						<?php
						$prev = $preset_meta[ $active_preset ];
						?>
						<div>
							<p class="adm-preview-label"><?php esc_html_e( 'Preview', 'darkadmin-dark-mode-for-adminpanel' ); ?></p>
							<div class="adm-preset-preview" id="adm-preset-preview"
								style="--adm-preview-bg:<?php echo esc_attr( $prev['bg'] ); ?>;--adm-preview-surface:<?php echo esc_attr( $prev['surface'] ); ?>;--adm-preview-primary:<?php echo esc_attr( $prev['primary'] ); ?>;--adm-preview-text:<?php echo esc_attr( $prev['text'] ); ?>;--adm-preview-bar:<?php echo esc_attr( $prev['bar'] ); ?>;">
								<div class="adm-preview-topbar">
									<div class="adm-preview-topbar-logo"></div>
									<div class="adm-preview-topbar-items">
										<div class="adm-preview-topbar-item" style="width:40px"></div>
										<div class="adm-preview-topbar-item" style="width:28px"></div>
										<div class="adm-preview-topbar-item" style="width:34px"></div>
									</div>
								</div>
								<div class="adm-preview-main">
									<div class="adm-preview-sidebar">
										<div class="adm-preview-sidebar-item is-active"></div>
										<div class="adm-preview-sidebar-item"></div>
										<div class="adm-preview-sidebar-item"></div>
										<div class="adm-preview-sidebar-item"></div>
										<div class="adm-preview-sidebar-item"></div>
										<div class="adm-preview-sidebar-item"></div>
									</div>
									<div class="adm-preview-content">
										<div class="adm-preview-content-bar is-title"></div>
										<div class="adm-preview-content-bar" style="width:80%"></div>
										<div class="adm-preview-content-card"></div>
										<div class="adm-preview-content-btn"></div>
									</div>
								</div>
								<div class="adm-preview-name" id="adm-preview-name"><?php echo esc_html( $prev['label'] ); ?></div>
							</div>
						</div>

					</div><!-- .adm-preset-layout -->

					<input type="hidden" id="darkadmin_preset" name="darkadmin_preset" value="<?php echo esc_attr( $active_preset ); ?>" />
				</div>
			</div>

			<!-- User Access -->
			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-groups"></span>
					<h2><?php esc_html_e( 'User Access', 'darkadmin-dark-mode-for-adminpanel' ); ?></h2>
				</div>
				<div class="adm-card-body">

					<div class="adm-access-mode">
						<label class="adm-access-mode-option <?php echo esc_attr( 'all' === $user_access_mode ? 'is-active' : '' ); ?>">
							<input type="radio" name="darkadmin_user_access_mode" value="all"
								<?php checked( $user_access_mode, 'all' ); ?> />
							<span class="dashicons dashicons-groups"></span>
							<span class="adm-access-mode-label">
								<strong><?php esc_html_e( 'All Users', 'darkadmin-dark-mode-for-adminpanel' ); ?></strong>
								<span><?php esc_html_e( 'Dark mode applies to everyone.', 'darkadmin-dark-mode-for-adminpanel' ); ?></span>
							</span>
						</label>

						<label class="adm-access-mode-option <?php echo esc_attr( 'include' === $user_access_mode ? 'is-active' : '' ); ?><?php echo esc_attr( ! $has_users ? ' is-disabled' : '' ); ?>">
							<input type="radio" name="darkadmin_user_access_mode" value="include"
								<?php checked( $user_access_mode, 'include' ); ?>
								<?php disabled( ! $has_users ); ?> />
							<span class="dashicons dashicons-yes-alt"></span>
							<span class="adm-access-mode-label">
								<strong><?php esc_html_e( 'Include', 'darkadmin-dark-mode-for-adminpanel' ); ?></strong>
								<span><?php esc_html_e( 'Only selected users get dark mode.', 'darkadmin-dark-mode-for-adminpanel' ); ?></span>
							</span>
						</label>

						<label class="adm-access-mode-option <?php echo esc_attr( 'exclude' === $user_access_mode ? 'is-active' : '' ); ?><?php echo esc_attr( ! $has_users ? ' is-disabled' : '' ); ?>">
							<input type="radio" name="darkadmin_user_access_mode" value="exclude"
								<?php checked( $user_access_mode, 'exclude' ); ?>
								<?php disabled( ! $has_users ); ?> />
							<span class="dashicons dashicons-dismiss"></span>
							<span class="adm-access-mode-label">
								<strong><?php esc_html_e( 'Exclude', 'darkadmin-dark-mode-for-adminpanel' ); ?></strong>
								<span><?php esc_html_e( 'Everyone except selected users gets dark mode.', 'darkadmin-dark-mode-for-adminpanel' ); ?></span>
							</span>
						</label>
					</div>

					<?php if ( $has_users ) : ?>
					<div class="adm-user-grid" id="adm-user-grid"
						<?php
						if ( 'all' === $user_access_mode ) {
							echo 'style="display:none;"';
						}
						?>>
						<?php foreach ( $selectable_users as $user ) : ?>
							<label class="adm-user-item">
								<input
									type="checkbox"
									name="darkadmin_allowed_users[]"
									value="<?php echo esc_attr( $user->ID ); ?>"
									<?php checked( in_array( $user->ID, $allowed, true ) ); ?>
								/>
								<span class="adm-user-avatar"><?php echo wp_kses( get_avatar( $user->ID, 28 ), $allowed_avatar_tags ); ?></span>
								<span class="adm-user-info">
									<strong><?php echo esc_html( $user->display_name ); ?></strong>
									<span><?php echo esc_html( $user->user_login ); ?> &middot; <?php echo esc_html( implode( ', ', $user->roles ) ); ?></span>
								</span>
							</label>
						<?php endforeach; ?>
					</div>
					<?php else : ?>
					<div class="adm-user-empty-state">
						<span class="dashicons dashicons-groups adm-user-empty-icon"></span>
						<p class="adm-user-empty-text">
							<?php esc_html_e( 'No non-administrator users found. Create additional users to manage their dark mode access here.', 'darkadmin-dark-mode-for-adminpanel' ); ?>
						</p>
					</div>
					<?php endif; ?>

					<p class="adm-field-desc" style="margin-top:10px;">
						<span class="dashicons dashicons-info" style="font-size:14px;width:14px;height:14px;vertical-align:middle;"></span>
						<?php esc_html_e( 'Administrators are not listed here — they always have dark mode active.', 'darkadmin-dark-mode-for-adminpanel' ); ?>
					</p>
				</div>
			</div>

			<!-- Layout & Spacing -->
			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-editor-table"></span>
					<h2><?php esc_html_e( 'Layout & Spacing', 'darkadmin-dark-mode-for-adminpanel' ); ?></h2>
				</div>
				<div class="adm-card-body">
					<p class="adm-card-description">
						<?php esc_html_e( 'Adjust spacing, button sizes, border radii and shadow. px values only for size fields; shadow accepts any valid CSS box-shadow value.', 'darkadmin-dark-mode-for-adminpanel' ); ?>
					</p>
					<div class="adm-layout-grid">
						<?php foreach ( $layout_map as $key => $info ) : ?>
							<div class="adm-layout-item">
								<label class="adm-layout-label" for="adm_layout_<?php echo esc_attr( $key ); ?>">
									<?php echo esc_html( $info['label'] ); ?>
									<code class="adm-color-var-name"><?php echo esc_html( $info['var'] ); ?></code>
								</label>
								<input
									type="text"
									id="adm_layout_<?php echo esc_attr( $key ); ?>"
									name="darkadmin_layout[<?php echo esc_attr( $key ); ?>]"
									value="<?php echo esc_attr( $layout[ $key ] ?? $layout_defaults[ $key ] ); ?>"
									class="adm-layout-input"
									data-key="<?php echo esc_attr( $key ); ?>"
									data-default="<?php echo esc_attr( $layout_defaults[ $key ] ); ?>"
								/>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="adm-color-reset-row">
						<button type="button" id="adm-reset-layout" class="button">
							<span class="dashicons dashicons-image-rotate"></span>
							<?php esc_html_e( 'Restore Default Layout', 'darkadmin-dark-mode-for-adminpanel' ); ?>
						</button>
					</div>
				</div>
			</div>

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
											name="darkadmin_colors[<?php echo esc_attr( $key ); ?>]"
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
							<span class="adm-var-count"><?php echo absint( count( $var_map ) + count( $layout_map ) ); ?></span>
						</summary>
						<?php foreach ( $grouped_vars as $group_name => $entries ) : ?>
							<div class="adm-var-group">
								<h4 class="adm-var-group-title"><?php echo esc_html( $group_name ); ?></h4>
								<div class="adm-var-grid">
									<?php
									foreach ( $entries as $entry ) :
										$key           = $entry['key'];
										$info          = $entry['info'];
										$raw_color     = isset( $colors[ $key ] ) ? $colors[ $key ] : '';
										$sanitized     = sanitize_hex_color( $raw_color );
										$current_color = ( '' !== $sanitized ) ? $sanitized : $defaults[ $key ];
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
						<div class="adm-var-group">
							<h4 class="adm-var-group-title"><?php esc_html_e( 'Layout & Spacing', 'darkadmin-dark-mode-for-adminpanel' ); ?></h4>
							<div class="adm-var-grid">
								<?php foreach ( $layout_map as $key => $info ) : ?>
									<div class="adm-var-item">
										<span class="adm-var-swatch" style="background:var(--adm-surface-3);"></span>
										<div class="adm-var-info">
											<button type="button" class="adm-var-copy" data-var="<?php echo esc_attr( $info['var'] ); ?>" title="<?php esc_attr_e( 'Click to copy', 'darkadmin-dark-mode-for-adminpanel' ); ?>">
												<code><?php echo esc_html( $info['var'] ); ?></code>
												<span class="adm-var-copy-icon dashicons dashicons-clipboard"></span>
											</button>
											<span class="adm-var-label"><?php echo esc_html( $info['label'] ); ?></span>
										</div>
										<span class="adm-var-hex"><?php echo esc_html( $layout[ $key ] ?? $layout_defaults[ $key ] ); ?></span>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</details>
					<div class="adm-css-editor-wrap">
						<textarea
							id="darkadmin_custom_css"
							name="darkadmin_custom_css"
							class="adm-css-editor"
							rows="12"
							spellcheck="false"
							placeholder="/* Your custom CSS here */"
						><?php echo esc_textarea( $custom ); ?></textarea>
					</div>
				</div>
			</div>

			<!-- Advanced -->
			<div class="adm-card">
				<div class="adm-card-header">
					<span class="dashicons dashicons-admin-tools"></span>
					<h2><?php esc_html_e( 'Advanced', 'darkadmin-dark-mode-for-adminpanel' ); ?></h2>
				</div>
				<div class="adm-card-body">

					<div class="adm-field-info" style="margin-bottom:8px;">
						<label for="darkadmin_excluded_pages" class="adm-field-title">
							<?php esc_html_e( 'Excluded Pages', 'darkadmin-dark-mode-for-adminpanel' ); ?>
						</label>
						<span class="adm-field-desc">
							<?php esc_html_e( 'One entry per line. Dark mode styles will not be loaded on matching admin pages.', 'darkadmin-dark-mode-for-adminpanel' ); ?>
							<?php esc_html_e( 'Supports plain filenames (plugins.php) and custom page slugs (admin.php?page=my-plugin). Lines starting with # are treated as comments.', 'darkadmin-dark-mode-for-adminpanel' ); ?>
						</span>
					</div>

					<div class="adm-css-editor-wrap">
						<textarea
							id="darkadmin_excluded_pages"
							name="darkadmin_excluded_pages"
							class="adm-css-editor"
							rows="6"
							spellcheck="false"
							placeholder="# Examples:
plugins.php
tools.php
admin.php?page=my-plugin"
						><?php echo esc_textarea( $excluded_pages ); ?></textarea>
					</div>

					<p class="adm-field-desc" style="margin-top:8px;">
						<span class="dashicons dashicons-info" style="font-size:14px;width:14px;height:14px;vertical-align:middle;"></span>
						<?php
						echo wp_kses(
							sprintf(
								/* translators: %s: comma-separated list of always-excluded admin page filenames */
								__( 'The following pages are always excluded: %s', 'darkadmin-dark-mode-for-adminpanel' ),
								'<code>site-editor.php</code>, <code>post-new.php</code>, <code>post.php</code>'
							),
							array( 'code' => array() )
						);
						?>
					</p>

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
