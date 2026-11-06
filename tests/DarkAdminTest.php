<?php
/**
 * Tests for DarkAdmin plugin functions.
 */

class DarkAdminTest extends WP_UnitTestCase {

    public function set_up(): void {
        parent::set_up();
        // Reset all plugin options before each test.
        delete_option( 'darkadmin_dark_mode_enabled' );
        delete_option( 'darkadmin_user_access_mode' );
        delete_option( 'darkadmin_allowed_users' );
        delete_option( 'darkadmin_colors' );
        delete_option( 'darkadmin_preset' );
        delete_option( 'darkadmin_db_version' );
        delete_option( 'darkadmin_plugins' );
        delete_option( 'darkadmin_layout' );
        delete_option( 'darkadmin_custom_css' );
        delete_option( 'darkadmin_excluded_pages' );
        delete_option( 'darkadmin_auto_darken' );
        delete_option( 'darkadmin_test_non_string' );
    }

    // -------------------------------------------------------------------------
    // darkadmin_default_colors
    // -------------------------------------------------------------------------

    public function test_default_colors_returns_array(): void {
        $colors = darkadmin_default_colors();
        $this->assertIsArray( $colors );
        $this->assertNotEmpty( $colors );
    }

    public function test_default_colors_contains_required_keys(): void {
        $colors   = darkadmin_default_colors();
        $required = [ 'bg', 'bg_bar', 'bg_deep', 'text', 'primary', 'border', 'link', 'success', 'warning', 'danger' ];
        foreach ( $required as $key ) {
            $this->assertArrayHasKey( $key, $colors, "Missing key: $key" );
        }
    }

    public function test_default_colors_are_valid_hex(): void {
        foreach ( darkadmin_default_colors() as $key => $value ) {
            $this->assertMatchesRegularExpression( '/^#[0-9a-fA-F]{6}$/', $value, "Invalid hex color for key: $key" );
        }
    }

    // -------------------------------------------------------------------------
    // darkadmin_preset_colors
    // -------------------------------------------------------------------------

    public function test_preset_colors_contains_classic_and_modern(): void {
        $presets = darkadmin_preset_colors();
        $this->assertArrayHasKey( 'classic', $presets );
        $this->assertArrayHasKey( 'modern', $presets );
    }

    public function test_preset_colors_classic_matches_default_colors(): void {
        $this->assertSame( darkadmin_default_colors(), darkadmin_preset_colors()['classic'] );
    }

    public function test_preset_colors_modern_has_all_keys(): void {
        $classic_keys = array_keys( darkadmin_preset_colors()['classic'] );
        $modern_keys  = array_keys( darkadmin_preset_colors()['modern'] );
        $this->assertSame( $classic_keys, $modern_keys );
    }

    public function test_preset_colors_returns_same_instance_on_repeated_calls(): void {
        $first  = darkadmin_preset_colors();
        $second = darkadmin_preset_colors();
        $this->assertSame( $first, $second );
    }

    // -------------------------------------------------------------------------
    // darkadmin_normalize_preset_slug
    // -------------------------------------------------------------------------

    public function test_normalize_preset_slug_maps_legacy_default_to_classic(): void {
        $this->assertSame( 'classic', darkadmin_normalize_preset_slug( 'default' ) );
    }

    public function test_normalize_preset_slug_unknown_falls_back_to_modern(): void {
        $this->assertSame( 'modern', darkadmin_normalize_preset_slug( 'unknown-preset' ) );
    }

    // -------------------------------------------------------------------------
    // darkadmin_preset_css_file
    // -------------------------------------------------------------------------

    public function test_preset_css_file_classic(): void {
        $this->assertSame( 'darkadmin-dark.css', darkadmin_preset_css_file( 'classic' ) );
    }

    public function test_preset_css_file_modern(): void {
        $this->assertSame( 'darkadmin-wp-modern.css', darkadmin_preset_css_file( 'modern' ) );
    }

    public function test_preset_css_file_legacy_default_uses_classic_css(): void {
        $this->assertSame( 'darkadmin-dark.css', darkadmin_preset_css_file( 'default' ) );
    }

    public function test_preset_css_file_unknown_falls_back_to_modern_css(): void {
        $this->assertSame( 'darkadmin-wp-modern.css', darkadmin_preset_css_file( 'unknown-preset' ) );
    }

    // -------------------------------------------------------------------------
    // darkadmin_css_variable_map
    // -------------------------------------------------------------------------

    public function test_css_variable_map_returns_array(): void {
        $this->assertIsArray( darkadmin_css_variable_map() );
    }

    public function test_css_variable_map_keys_match_default_colors(): void {
        $color_keys = array_keys( darkadmin_default_colors() );
        $map_keys   = array_keys( darkadmin_css_variable_map() );
        sort( $color_keys );
        sort( $map_keys );
        $this->assertSame( $color_keys, $map_keys );
    }

    public function test_css_variable_map_entries_have_required_keys(): void {
        foreach ( darkadmin_css_variable_map() as $key => $entry ) {
            $this->assertArrayHasKey( 'var',   $entry, "Missing 'var' for $key" );
            $this->assertArrayHasKey( 'label', $entry, "Missing 'label' for $key" );
            $this->assertArrayHasKey( 'group', $entry, "Missing 'group' for $key" );
        }
    }

    public function test_css_variable_map_vars_start_with_adm_prefix(): void {
        foreach ( darkadmin_css_variable_map() as $key => $entry ) {
            $this->assertStringStartsWith( '--adm-', $entry['var'], "CSS var does not start with --adm- for key: $key" );
        }
    }

    // -------------------------------------------------------------------------
    // darkadmin_sanitize_colors
    // -------------------------------------------------------------------------

    public function test_sanitize_colors_keeps_valid_hex(): void {
        $input  = darkadmin_default_colors();
        $result = darkadmin_sanitize_colors( $input );
        $this->assertSame( $input, $result );
    }

    public function test_sanitize_colors_replaces_invalid_value_with_default(): void {
        $defaults        = darkadmin_default_colors();
        $input           = $defaults;
        $input['bg']     = 'not-a-color';
        $input['_preset'] = 'classic';
        $result          = darkadmin_sanitize_colors( $input );
        $this->assertSame( $defaults['bg'], $result['bg'] );
    }

    public function test_sanitize_colors_fills_missing_keys_with_modern_defaults(): void {
        $result = darkadmin_sanitize_colors( [] );
        $this->assertSame( darkadmin_preset_colors()['modern'], $result );
    }

    public function test_sanitize_colors_uses_preset_fallback_for_modern(): void {
        $input            = [ '_preset' => 'modern', 'bg' => 'not-a-color' ];
        $result           = darkadmin_sanitize_colors( $input );
        $modern           = darkadmin_preset_colors()['modern'];
        $this->assertSame( $modern['bg'], $result['bg'] );
    }

    public function test_sanitize_colors_rejects_xss_attempt(): void {
        $input           = darkadmin_default_colors();
        $input['bg']     = '<script>alert(1)</script>';
        $input['_preset'] = 'classic';
        $result          = darkadmin_sanitize_colors( $input );
        $this->assertSame( darkadmin_default_colors()['bg'], $result['bg'] );
    }

    public function test_sanitize_colors_falls_back_for_non_string_value(): void {
        $input = [
            '_preset' => 'classic',
            'bg'      => [ '#000000' ],
        ];

        $result = darkadmin_sanitize_colors( $input );
        $this->assertSame( darkadmin_default_colors()['bg'], $result['bg'] );
    }

    // -------------------------------------------------------------------------
    // darkadmin_sanitize_layout
    // -------------------------------------------------------------------------

    public function test_sanitize_layout_keeps_valid_px_value(): void {
        $result = darkadmin_sanitize_layout(
            [
                '_preset' => 'classic',
                'space_2' => '14px',
            ]
        );

        $this->assertSame( '14px', $result['space_2'] );
    }

    public function test_sanitize_layout_falls_back_for_non_string_value(): void {
        $result = darkadmin_sanitize_layout(
            [
                '_preset' => 'classic',
                'space_2' => [ '14px' ],
            ]
        );

        $this->assertSame( darkadmin_default_layout()['space_2'], $result['space_2'] );
    }

    public function test_sanitize_layout_rejects_invalid_shadow(): void {
        $result = darkadmin_sanitize_layout(
            [
                '_preset'    => 'classic',
                'shadow_md' => 'red;}body{display:none',
            ]
        );

        $this->assertSame( darkadmin_default_layout()['shadow_md'], $result['shadow_md'] );
    }

    // -------------------------------------------------------------------------
    // darkadmin_sanitize_user_access_mode
    // -------------------------------------------------------------------------

    public function test_sanitize_user_access_mode_allows_all(): void {
        $this->assertSame( 'all', darkadmin_sanitize_user_access_mode( 'all' ) );
    }

    public function test_sanitize_user_access_mode_allows_include(): void {
        $this->assertSame( 'include', darkadmin_sanitize_user_access_mode( 'include' ) );
    }

    public function test_sanitize_user_access_mode_allows_exclude(): void {
        $this->assertSame( 'exclude', darkadmin_sanitize_user_access_mode( 'exclude' ) );
    }

    public function test_sanitize_user_access_mode_rejects_invalid(): void {
        $this->assertSame( 'all', darkadmin_sanitize_user_access_mode( 'invalid' ) );
    }

    public function test_sanitize_user_access_mode_rejects_empty(): void {
        $this->assertSame( 'all', darkadmin_sanitize_user_access_mode( '' ) );
    }

    // -------------------------------------------------------------------------
    // darkadmin_sanitize_custom_css
    // -------------------------------------------------------------------------

    public function test_sanitize_custom_css_keeps_valid_css(): void {
        $css    = 'body { color: red; }';
        $result = darkadmin_sanitize_custom_css( $css );
        $this->assertSame( $css, $result );
    }

    public function test_sanitize_custom_css_removes_style_end_tag(): void {
        $result = darkadmin_sanitize_custom_css( 'body{color:red}</StYlE ><script>alert(1)</script>' );
        $this->assertStringNotContainsStringIgnoringCase( '</style', $result );
    }

    public function test_sanitize_custom_css_preserves_inline_svg_data_uri(): void {
        $css = 'body{background-image:url("data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\'><rect/></svg>");}';
        $this->assertSame( $css, darkadmin_sanitize_custom_css( $css ) );
    }

    public function test_sanitize_custom_css_rejects_non_string(): void {
        $this->assertSame( '', darkadmin_sanitize_custom_css( [ 'body{}' ] ) );
    }

    public function test_sanitize_custom_css_allows_empty_string(): void {
        $this->assertSame( '', darkadmin_sanitize_custom_css( '' ) );
    }

    // -------------------------------------------------------------------------
    // Option read helpers
    // -------------------------------------------------------------------------

    public function test_read_string_option_returns_stored_string(): void {
        update_option( 'darkadmin_custom_css', 'body{}' );
        $this->assertSame( 'body{}', darkadmin_read_string_option( 'darkadmin_custom_css' ) );
    }

    public function test_read_string_option_returns_default_for_non_string(): void {
        update_option( 'darkadmin_test_non_string', [ 'body{}' ] );
        $this->assertSame( 'fallback', darkadmin_read_string_option( 'darkadmin_test_non_string', 'fallback' ) );
    }

    public function test_read_color_value_falls_back_for_invalid_or_non_string_values(): void {
        $this->assertSame( '#123456', darkadmin_read_color_value( 'red', '#123456' ) );
        $this->assertSame( '#123456', darkadmin_read_color_value( [ '#abcdef' ], '#123456' ) );
        $this->assertSame( '#abcdef', darkadmin_read_color_value( '#abcdef', '#123456' ) );
    }

    public function test_read_layout_value_falls_back_for_invalid_or_non_string_values(): void {
        $this->assertSame( '8px', darkadmin_read_layout_value( '', '8px' ) );
        $this->assertSame( '8px', darkadmin_read_layout_value( [ '12px' ], '8px' ) );
        $this->assertSame( '12px', darkadmin_read_layout_value( '12px', '8px' ) );
    }

    // -------------------------------------------------------------------------
    // darkadmin_sanitize_user_ids
    // -------------------------------------------------------------------------

    public function test_sanitize_user_ids_filters_empty_values(): void {
        $this->assertSame( [ 1, 2 ], darkadmin_sanitize_user_ids( [ '1', '', '2', '0' ] ) );
    }

    public function test_sanitize_user_ids_ignores_non_scalar_values(): void {
        $this->assertSame( [ 1, 2 ], darkadmin_sanitize_user_ids( [ '1', [ '3' ], '2' ] ) );
    }

    public function test_sanitize_bool_ignores_non_scalar_values(): void {
        $this->assertFalse( darkadmin_sanitize_bool( [ '1' ] ) );
        $this->assertTrue( darkadmin_sanitize_bool( '1' ) );
    }

    // -------------------------------------------------------------------------
    // darkadmin_is_dark_mode_active
    // -------------------------------------------------------------------------

    public function test_dark_mode_inactive_when_option_disabled(): void {
        update_option( 'darkadmin_dark_mode_enabled', false );
        $this->assertFalse( darkadmin_is_dark_mode_active() );
    }

    public function test_dark_mode_inactive_when_no_user_logged_in(): void {
        update_option( 'darkadmin_dark_mode_enabled', true );
        wp_set_current_user( 0 );
        $this->assertFalse( darkadmin_is_dark_mode_active() );
    }

    public function test_dark_mode_active_for_admin_when_enabled(): void {
        update_option( 'darkadmin_dark_mode_enabled', true );
        $admin = $this->factory->user->create( [ 'role' => 'administrator' ] );
        wp_set_current_user( $admin );
        $this->assertTrue( darkadmin_is_dark_mode_active() );
    }

    public function test_dark_mode_active_for_all_users_in_all_mode(): void {
        update_option( 'darkadmin_dark_mode_enabled', true );
        update_option( 'darkadmin_user_access_mode', 'all' );
        $user = $this->factory->user->create( [ 'role' => 'subscriber' ] );
        wp_set_current_user( $user );
        $this->assertTrue( darkadmin_is_dark_mode_active() );
    }

    public function test_dark_mode_active_for_included_user(): void {
        update_option( 'darkadmin_dark_mode_enabled', true );
        update_option( 'darkadmin_user_access_mode', 'include' );
        $user = $this->factory->user->create( [ 'role' => 'subscriber' ] );
        update_option( 'darkadmin_allowed_users', [ (string) $user ] );
        wp_set_current_user( $user );
        $this->assertTrue( darkadmin_is_dark_mode_active() );
    }

    public function test_dark_mode_inactive_for_non_included_user(): void {
        update_option( 'darkadmin_dark_mode_enabled', true );
        update_option( 'darkadmin_user_access_mode', 'include' );
        update_option( 'darkadmin_allowed_users', [] );
        $user = $this->factory->user->create( [ 'role' => 'subscriber' ] );
        wp_set_current_user( $user );
        $this->assertFalse( darkadmin_is_dark_mode_active() );
    }

    public function test_dark_mode_inactive_for_excluded_user(): void {
        update_option( 'darkadmin_dark_mode_enabled', true );
        update_option( 'darkadmin_user_access_mode', 'exclude' );
        $user = $this->factory->user->create( [ 'role' => 'subscriber' ] );
        update_option( 'darkadmin_allowed_users', [ (string) $user ] );
        wp_set_current_user( $user );
        $this->assertFalse( darkadmin_is_dark_mode_active() );
    }

    public function test_dark_mode_active_for_non_excluded_user(): void {
        update_option( 'darkadmin_dark_mode_enabled', true );
        update_option( 'darkadmin_user_access_mode', 'exclude' );
        update_option( 'darkadmin_allowed_users', [] );
        $user = $this->factory->user->create( [ 'role' => 'subscriber' ] );
        wp_set_current_user( $user );
        $this->assertTrue( darkadmin_is_dark_mode_active() );
    }

    // -------------------------------------------------------------------------
    // DARKADMIN_VERSION constant
    // -------------------------------------------------------------------------

    public function test_version_constant_is_defined(): void {
        $this->assertTrue( defined( 'DARKADMIN_VERSION' ) );
    }

    public function test_version_constant_is_semver(): void {
        $this->assertMatchesRegularExpression( '/^\d+\.\d+\.\d+$/', DARKADMIN_VERSION );
    }

    public function test_default_preset_constant_is_modern(): void {
        $this->assertSame( 'modern', DARKADMIN_DEFAULT_PRESET );
    }

    // -------------------------------------------------------------------------
    // Editor screen coverage
    // -------------------------------------------------------------------------

    public function test_site_editor_is_always_excluded(): void {
        $this->assertTrue( darkadminIsEditorScreenExcluded( 'site-editor.php' ) );
    }

    public function test_classic_post_editor_is_not_excluded(): void {
        $screen = WP_Screen::get( 'post' );
        $screen->is_block_editor( false );

        $this->assertFalse( darkadminIsEditorScreenExcluded( 'post.php', $screen ) );
        $this->assertFalse( darkadminIsEditorScreenExcluded( 'post-new.php', $screen ) );
    }

    public function test_block_post_editor_is_excluded(): void {
        $screen = WP_Screen::get( 'post' );
        $screen->is_block_editor( true );

        $this->assertTrue( darkadminIsEditorScreenExcluded( 'post.php', $screen ) );
        $this->assertTrue( darkadminIsEditorScreenExcluded( 'post-new.php', $screen ) );
    }

    public function test_regular_admin_screen_is_not_excluded(): void {
        $this->assertFalse( darkadminIsEditorScreenExcluded( 'plugins.php', WP_Screen::get( 'plugins' ) ) );
    }

    // -------------------------------------------------------------------------
    // Plugin styles
    // -------------------------------------------------------------------------

    public function test_plugin_registry_has_ten_entries(): void {
        $this->assertCount( 10, darkadmin_plugin_registry() );
    }

    public function test_wordpress_ai_screen_ids_include_all_ai_admin_pages(): void {
        $ids = darkadmin_wordpress_ai_screen_ids();
        $this->assertContains( 'options-connectors', $ids );
        $this->assertContains( 'settings_page_ai-wp-admin', $ids );
        $this->assertContains( 'tools_page_ai-connector-approval', $ids );
        $this->assertContains( 'tools_page_ai-request-logs', $ids );
    }

    public function test_boot_wpds_screen_ids_cover_core_boot_screens_and_ai_pages(): void {
        $ids = darkadmin_boot_wpds_screen_ids();
        $this->assertContains( 'font-library', $ids );
        $this->assertContains( 'options-connectors', $ids );
        foreach ( darkadmin_wordpress_ai_screen_ids() as $ai_id ) {
            $this->assertContains( $ai_id, $ids );
        }
        $this->assertSame( array_values( array_unique( $ids ) ), $ids );
    }

    public function test_boot_wpds_styles_enqueue_on_font_library_screen(): void {
        set_current_screen( 'font-library' );
        darkadmin_enqueue_boot_wpds_styles();
        $this->assertTrue( wp_style_is( 'darkadmin-wpds', 'enqueued' ) );
        wp_dequeue_style( 'darkadmin-wpds' );
        set_current_screen( 'front' );
    }

    public function test_boot_wpds_styles_skip_regular_screens(): void {
        set_current_screen( 'edit-post' );
        darkadmin_enqueue_boot_wpds_styles();
        $this->assertFalse( wp_style_is( 'darkadmin-wpds', 'enqueued' ) );
        set_current_screen( 'front' );
    }

    public function test_sanitize_plugins_stores_disabled_slugs_for_unchecked_installed_plugins(): void {
        if ( ! defined( 'WPSEO_VERSION' ) ) {
            define( 'WPSEO_VERSION', '1.0.0' );
        }
        if ( ! defined( 'WORDFENCE_VERSION' ) ) {
            define( 'WORDFENCE_VERSION', '1.0.0' );
        }

        $result = darkadmin_sanitize_plugins( [ 'yoast', 'invalid' ] );
        $this->assertSame( [ 'disabled' => [ 'wordfence' ] ], $result );
    }

    public function test_sanitize_plugins_returns_empty_when_all_installed_are_enabled(): void {
        if ( ! defined( 'WPSEO_VERSION' ) ) {
            define( 'WPSEO_VERSION', '1.0.0' );
        }

        $enabled = array_values(
            array_filter(
                array_keys( darkadmin_plugin_registry() ),
                static function ( string $slug ): bool {
                    return 'wordpress-ai' !== $slug && darkadmin_plugin_is_installed( $slug );
                }
            )
        );

        $this->assertSame( [ 'disabled' => [] ], darkadmin_sanitize_plugins( $enabled ) );
    }

    public function test_plugin_styles_enabled_when_installed_and_not_disabled(): void {
        if ( ! defined( 'WPSEO_VERSION' ) ) {
            define( 'WPSEO_VERSION', '1.0.0' );
        }

        update_option( 'darkadmin_plugins', [] );
        $this->assertTrue( darkadmin_plugin_styles_enabled( 'yoast' ) );
    }

    public function test_plugin_styles_disabled_when_slug_is_in_disabled_list(): void {
        if ( ! defined( 'WPSEO_VERSION' ) ) {
            define( 'WPSEO_VERSION', '1.0.0' );
        }

        update_option( 'darkadmin_plugins', [ 'yoast' ] );
        $this->assertFalse( darkadmin_plugin_styles_enabled( 'yoast' ) );
    }

    public function test_wordpress_ai_is_never_loaded_via_plugin_styles_helper(): void {
        if ( ! defined( 'WPAI_VERSION' ) ) {
            define( 'WPAI_VERSION', '1.0.0' );
        }

        $this->assertFalse( darkadmin_plugin_styles_enabled( 'wordpress-ai' ) );
    }

    // -------------------------------------------------------------------------
    // Uninstall cleanup
    // -------------------------------------------------------------------------

    public function test_uninstall_removes_options_and_notice_transients(): void {
        update_option( 'darkadmin_dark_mode_enabled', true );
        update_option( 'darkadmin_db_version', '0.4.0' );
        set_transient( 'darkadmin_settings_saved_123', 1, MINUTE_IN_SECONDS );

        if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
            define( 'WP_UNINSTALL_PLUGIN', 'darkadmin-dark-mode-for-adminpanel/darkadmin.php' );
        }

        include_once dirname( __DIR__ ) . '/uninstall.php';

        $this->assertFalse( get_option( 'darkadmin_dark_mode_enabled' ) );
        $this->assertFalse( get_option( 'darkadmin_db_version' ) );
        $this->assertFalse( get_transient( 'darkadmin_settings_saved_123' ) );
    }
}
