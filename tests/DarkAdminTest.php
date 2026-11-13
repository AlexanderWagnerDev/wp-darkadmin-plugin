<?php
/**
 * Tests for DarkAdmin plugin functions.
 */

class DarkAdminTest extends WP_UnitTestCase {

    /** Code of the main fixture plugin used by the plugin exclusion tests. */
    private const FIXTURE_MAIN_CODE = 'function darkadmin_fixture_main_render() {}';

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
        delete_option( 'darkadmin_block_editor' );
        delete_option( 'darkadmin_block_editor_theme' );
        delete_option( 'darkadmin_site_editor' );
        delete_option( 'darkadmin_login_page' );
        delete_option( 'darkadmin_plugin_exclusions' );
        remove_all_filters( 'darkadmin_block_editor_supported' );
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

    public function test_sanitize_layout_preserves_calc_shadow(): void {
        $shadow = '0 0 calc(1px + 1px) rgba(0,0,0,.35)';

        $result = darkadmin_sanitize_layout(
            [
                '_preset'    => 'modern',
                'shadow_md' => $shadow,
            ]
        );

        $this->assertSame( $shadow, $result['shadow_md'] );
    }

    public function test_sanitize_layout_rejects_unterminated_comment(): void {
        $result = darkadmin_sanitize_layout(
            [
                '_preset'    => 'modern',
                'shadow_md' => '0 0 0 1px /*',
            ]
        );

        $this->assertSame( darkadmin_default_layout()['shadow_md'], $result['shadow_md'] );
    }

    public function test_sanitize_layout_rejects_unbalanced_parentheses(): void {
        $result = darkadmin_sanitize_layout(
            [
                '_preset'    => 'modern',
                'shadow_md' => '0 0 calc(1px + 1px',
            ]
        );

        $this->assertSame( darkadmin_default_layout()['shadow_md'], $result['shadow_md'] );
    }

    public function test_sanitize_layout_preserves_closed_comment(): void {
        $shadow = '0 0 0 1px /* note */';

        $result = darkadmin_sanitize_layout(
            [
                '_preset'    => 'modern',
                'shadow_md' => $shadow,
            ]
        );

        $this->assertSame( $shadow, $result['shadow_md'] );
    }

    public function test_sanitize_layout_preserves_custom_property_with_underscore(): void {
        $shadow = '0 0 0 1px var(--my_shadow)';

        $result = darkadmin_sanitize_layout(
            [
                '_preset'    => 'modern',
                'shadow_md' => $shadow,
            ]
        );

        $this->assertSame( $shadow, $result['shadow_md'] );
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

    public function test_sanitize_custom_css_neutralizes_joined_style_end_tags(): void {
        $payload = '</st</styleyle>MARKER';
        $this->assertStringNotContainsStringIgnoringCase(
            '</style',
            darkadmin_sanitize_custom_css( $payload )
        );

        $this->assertStringNotContainsStringIgnoringCase(
            '</style',
            darkadmin_sanitize_custom_css( '</ST</StYlEyle>' )
        );
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

    public function test_read_layout_value_rejects_css_breakout(): void {
        $this->assertSame(
            '8px',
            darkadmin_read_layout_value( '8px;}body{display:none', '8px' )
        );
    }

    public function test_read_layout_value_rejects_disallowed_original_before_sanitizing(): void {
        $this->assertSame( '8px', darkadmin_read_layout_value( 'none<b></b>', '8px' ) );
    }

    public function test_read_layout_value_rejects_unterminated_comment(): void {
        $this->assertSame( '8px', darkadmin_read_layout_value( '0 0 0 1px /*', '8px' ) );
    }

    public function test_read_layout_value_rejects_unbalanced_parentheses(): void {
        $this->assertSame( '8px', darkadmin_read_layout_value( '0 0 calc(1px + 1px', '8px' ) );
    }

    public function test_read_layout_value_preserves_calc_and_custom_property(): void {
        $calc   = '0 0 calc(1px + 1px) rgba(0,0,0,.35)';
        $custom = '0 0 0 1px var(--my_shadow)';

        $this->assertSame( $calc, darkadmin_read_layout_value( $calc, '8px' ) );
        $this->assertSame( $custom, darkadmin_read_layout_value( $custom, '8px' ) );
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

    public function test_selectable_users_exclude_users_with_manage_options(): void {
        if ( ! get_role( 'darkadmin_manage_options_probe' ) ) {
            add_role(
                'darkadmin_manage_options_probe',
                'DarkAdmin Manage Options Probe',
                [
                    'read'           => true,
                    'manage_options' => true,
                ]
            );
        }

        $manager = $this->factory->user->create( [ 'role' => 'darkadmin_manage_options_probe' ] );
        $editor  = $this->factory->user->create( [ 'role' => 'editor' ] );
        $admin   = $this->factory->user->create( [ 'role' => 'administrator' ] );

        $ids = wp_list_pluck( darkadmin_get_selectable_users(), 'ID' );

        $this->assertNotContains( $manager, $ids );
        $this->assertNotContains( $admin, $ids );
        $this->assertContains( $editor, $ids );
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
    // Migrations
    // -------------------------------------------------------------------------

    public function test_migration_removes_legacy_plugin_option(): void {
        update_option( 'darkadmin_db_version', '0.3.0' );
        update_option( 'darkadmin_plugins', [ 'yoast' ] );

        darkadmin_maybe_migrate_options();

        $this->assertFalse( get_option( 'darkadmin_plugins', false ) );
        $this->assertSame( '0.3.1', get_option( 'darkadmin_db_version' ) );
    }

    public function test_migration_preserves_current_plugin_shape(): void {
        update_option( 'darkadmin_db_version', '0.3.0' );
        update_option( 'darkadmin_plugins', [ 'disabled' => [ 'yoast' ] ] );

        darkadmin_maybe_migrate_options();

        $this->assertSame( [ 'disabled' => [ 'yoast' ] ], get_option( 'darkadmin_plugins' ) );
        $this->assertSame( '0.3.1', get_option( 'darkadmin_db_version' ) );
    }

    // -------------------------------------------------------------------------
    // Editor screen coverage
    // -------------------------------------------------------------------------

    public function test_site_editor_is_excluded_by_default(): void {
        $this->assertTrue( darkadminIsEditorScreenExcluded( 'site-editor.php' ) );
    }

    public function test_site_editor_is_not_excluded_when_site_editor_option_enabled(): void {
        update_option( 'darkadmin_site_editor', '1' );

        $this->assertFalse( darkadminIsEditorScreenExcluded( 'site-editor.php' ) );
    }

    public function test_site_editor_option_does_not_affect_block_post_editor(): void {
        update_option( 'darkadmin_site_editor', '1' );
        add_filter( 'darkadmin_block_editor_supported', '__return_true' );
        $screen = WP_Screen::get( 'post' );
        $screen->is_block_editor( true );

        $this->assertTrue( darkadminIsEditorScreenExcluded( 'post.php', $screen ) );
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

    public function test_block_post_editor_is_not_excluded_when_block_editor_option_enabled(): void {
        update_option( 'darkadmin_block_editor', true );
        add_filter( 'darkadmin_block_editor_supported', '__return_true' );
        $screen = WP_Screen::get( 'post' );
        $screen->is_block_editor( true );

        $this->assertFalse( darkadminIsEditorScreenExcluded( 'post.php', $screen ) );
        $this->assertFalse( darkadminIsEditorScreenExcluded( 'post-new.php', $screen ) );
    }

    public function test_site_editor_stays_excluded_when_block_editor_option_enabled(): void {
        update_option( 'darkadmin_block_editor', true );
        add_filter( 'darkadmin_block_editor_supported', '__return_true' );

        $this->assertTrue( darkadminIsEditorScreenExcluded( 'site-editor.php' ) );
    }

    public function test_block_post_editor_stays_excluded_on_unsupported_wordpress(): void {
        // Before WordPress 7.1 the post editor can render without an iframe.
        update_option( 'darkadmin_block_editor', true );
        add_filter( 'darkadmin_block_editor_supported', '__return_false' );
        $screen = WP_Screen::get( 'post' );
        $screen->is_block_editor( true );

        $this->assertTrue( darkadminIsEditorScreenExcluded( 'post.php', $screen ) );
        $this->assertFalse( darkadminIsBlockEditorStylingEnabled() );
    }

    public function test_block_editor_support_follows_wordpress_version(): void {
        $expected = version_compare( get_bloginfo( 'version' ), '7.1-alpha', '>=' );

        $this->assertSame( $expected, darkadminIsBlockEditorSupported() );
    }

    public function test_block_editor_option_ignores_non_boolean_values(): void {
        update_option( 'darkadmin_block_editor', [ 'yes' ] );
        add_filter( 'darkadmin_block_editor_supported', '__return_true' );

        $this->assertFalse( darkadminIsBlockEditorStylingEnabled() );
    }

    // -------------------------------------------------------------------------
    // Block editor canvas
    // -------------------------------------------------------------------------

    /**
     * Runs the canvas settings filter on the dashboard screen as an administrator.
     *
     * @param string $context_name Block editor context name.
     * @return array<string, mixed>
     */
    private function run_canvas_settings_filter( string $context_name = 'core/edit-post' ): array {
        $previous_screen = get_current_screen();
        wp_set_current_user( $this->factory->user->create( [ 'role' => 'administrator' ] ) );
        set_current_screen( 'dashboard' );

        try {
            $settings = array( 'defaultEditorStyles' => array( array( 'css' => 'body{font-size:18px}' ) ) );

            return darkadmin_block_editor_canvas_settings( $settings, new WP_Block_Editor_Context( array( 'name' => $context_name ) ) );
        } finally {
            $GLOBALS['current_screen'] = $previous_screen;
        }
    }

    public function test_canvas_styles_are_added_when_block_editor_option_enabled(): void {
        update_option( 'darkadmin_dark_mode_enabled', true );
        update_option( 'darkadmin_block_editor', true );
        add_filter( 'darkadmin_block_editor_supported', '__return_true' );

        $styles = $this->run_canvas_settings_filter()['defaultEditorStyles'];

        $this->assertCount( 2, $styles );
        $this->assertSame( 'body{font-size:18px}', $styles[0]['css'] );
        $this->assertStringContainsString( 'background-color:', $styles[1]['css'] );
    }

    public function test_canvas_styles_are_not_added_when_block_editor_option_disabled(): void {
        update_option( 'darkadmin_dark_mode_enabled', true );

        $this->assertCount( 1, $this->run_canvas_settings_filter()['defaultEditorStyles'] );
    }

    public function test_canvas_styles_are_not_added_when_dark_mode_disabled(): void {
        update_option( 'darkadmin_block_editor', true );
        add_filter( 'darkadmin_block_editor_supported', '__return_true' );

        $this->assertCount( 1, $this->run_canvas_settings_filter()['defaultEditorStyles'] );
    }

    public function test_canvas_styles_are_not_added_to_other_editors(): void {
        update_option( 'darkadmin_dark_mode_enabled', true );
        update_option( 'darkadmin_block_editor', true );
        add_filter( 'darkadmin_block_editor_supported', '__return_true' );

        $this->assertCount( 1, $this->run_canvas_settings_filter( 'core/edit-site' )['defaultEditorStyles'] );
        $this->assertCount( 1, $this->run_canvas_settings_filter( 'core/edit-widgets' )['defaultEditorStyles'] );
    }

    public function test_canvas_styles_go_into_theme_styles_when_theme_is_darkened(): void {
        update_option( 'darkadmin_dark_mode_enabled', true );
        update_option( 'darkadmin_block_editor', true );
        update_option( 'darkadmin_block_editor_theme', true );
        add_filter( 'darkadmin_block_editor_supported', '__return_true' );

        $settings = $this->run_canvas_settings_filter();

        // The presets type keeps the entry in both editor modes ("Use theme styles"
        // on and off) without counting as theme styles.
        $this->assertCount( 1, $settings['defaultEditorStyles'] );
        $this->assertSame( 'presets', end( $settings['styles'] )['__unstableType'] );
        $this->assertStringContainsString( 'background-color:', end( $settings['styles'] )['css'] );
    }

    public function test_canvas_css_uses_saved_colors_and_rejects_invalid_values(): void {
        update_option( 'darkadmin_colors', [ 'bg' => '#123456', 'text' => 'red;}body{x:y' ] );

        $css = darkadmin_block_editor_canvas_css();

        $this->assertStringContainsString( 'background-color:#123456 !important', $css );
        $this->assertStringNotContainsString( 'red;}', $css );
        $this->assertStringContainsString( 'color:' . darkadmin_preset_fallbacks( 'modern' )['text'] . ' !important', $css );
    }

    public function test_block_editor_stylesheet_is_not_copied_into_canvas_iframe(): void {
        // The editor copies admin stylesheets whose selectors contain these
        // strings into the canvas iframe, where interface rules would leak.
        $css = (string) file_get_contents( dirname( __DIR__ ) . '/assets/css/darkadmin-block-editor.css' );
        $css = (string) preg_replace( '#/\*.*?\*/#s', '', $css );

        $this->assertStringNotContainsString( '.editor-styles-wrapper', $css );
        $this->assertStringNotContainsString( '.wp-block', $css );
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

    // -------------------------------------------------------------------------
    // Read-path hardening
    // -------------------------------------------------------------------------

    public function test_enqueue_sanitizes_custom_css_written_outside_settings_api(): void {
        update_option( 'darkadmin_dark_mode_enabled', true );
        wp_set_current_user( $this->factory->user->create( [ 'role' => 'administrator' ] ) );

        remove_filter( 'sanitize_option_darkadmin_custom_css', 'darkadmin_sanitize_custom_css' );
        try {
            update_option( 'darkadmin_custom_css', '</st</styleyle>MARKER' );
        } finally {
            add_filter( 'sanitize_option_darkadmin_custom_css', 'darkadmin_sanitize_custom_css' );
        }

        $this->assertStringContainsString( '</style', (string) get_option( 'darkadmin_custom_css' ) );

        $previous_pagenow   = $GLOBALS['pagenow'] ?? null;
        $previous_screen    = get_current_screen();
        $GLOBALS['pagenow'] = 'index.php';
        set_current_screen( 'dashboard' );

        do_action( 'admin_enqueue_scripts', 'index.php' );

        $inline = wp_styles()->get_data( 'darkadmin-darkmode', 'after' );
        $css    = is_array( $inline ) ? implode( "\n", $inline ) : (string) $inline;

        $this->assertStringContainsString( ':root{', $css );

        if ( null === $previous_pagenow ) {
            unset( $GLOBALS['pagenow'] );
        } else {
            $GLOBALS['pagenow'] = $previous_pagenow;
        }
        $GLOBALS['current_screen'] = $previous_screen;

        $this->assertStringNotContainsString( '</style', $css );
    }

    public function test_settings_body_class_is_not_added_when_page_is_excluded(): void {
        $previous_pagenow = $GLOBALS['pagenow'] ?? null;
        $previous_screen  = get_current_screen();
        $admin            = $this->factory->user->create( [ 'role' => 'administrator' ] );

        try {
            wp_set_current_user( $admin );
            update_option( 'darkadmin_dark_mode_enabled', true );
            update_option( 'darkadmin_excluded_pages', 'options-general.php' );

            $GLOBALS['pagenow'] = 'options-general.php';
            set_current_screen( 'settings_page_darkadmin' );

            wp_dequeue_style( 'darkadmin-darkmode' );
            wp_dequeue_script( 'darkadmin-settings-js' );
            wp_styles()->remove( 'darkadmin-darkmode' );
            wp_scripts()->remove( 'darkadmin-settings-js' );

            do_action( 'admin_enqueue_scripts', 'settings_page_darkadmin' );

            $inline = wp_scripts()->get_data( 'darkadmin-settings-js', 'before' );
            $script = is_array( $inline ) ? implode( "\n", $inline ) : (string) $inline;

            $this->assertStringNotContainsString( 'adm-dark-active', $script );

            update_option( 'darkadmin_excluded_pages', '' );
            wp_dequeue_style( 'darkadmin-darkmode' );
            wp_dequeue_script( 'darkadmin-settings-js' );
            wp_styles()->remove( 'darkadmin-darkmode' );
            wp_scripts()->remove( 'darkadmin-settings-js' );

            do_action( 'admin_enqueue_scripts', 'settings_page_darkadmin' );

            $inline = wp_scripts()->get_data( 'darkadmin-settings-js', 'before' );
            $script = is_array( $inline ) ? implode( "\n", $inline ) : (string) $inline;

            $this->assertStringContainsString( 'adm-dark-active', $script );
        } finally {
            wp_dequeue_style( 'darkadmin-darkmode' );
            wp_dequeue_script( 'darkadmin-settings-js' );
            delete_option( 'darkadmin_dark_mode_enabled' );
            delete_option( 'darkadmin_excluded_pages' );

            if ( null === $previous_pagenow ) {
                unset( $GLOBALS['pagenow'] );
            } else {
                $GLOBALS['pagenow'] = $previous_pagenow;
            }
            $GLOBALS['current_screen'] = $previous_screen;
        }
    }

    public function test_settings_page_ignores_non_scalar_allowed_users(): void {
        update_option( 'darkadmin_allowed_users', [ new stdClass() ] );
        wp_set_current_user( $this->factory->user->create( [ 'role' => 'administrator' ] ) );

        ob_start();
        darkadmin_settings_page();
        $html = ob_get_clean();

        $this->assertStringContainsString( 'adm-settings-wrap', $html );
    }

    /**
     * Creates a user with manage_options and optionally custom CSS editing capability.
     *
     * @param bool $can_edit_css Whether the user should pass current_user_can( 'edit_css' ).
     * @return int User ID.
     */
    private function create_custom_css_user( bool $can_edit_css ): int {
        if ( $can_edit_css ) {
            $user_id = $this->factory->user->create( [ 'role' => 'administrator' ] );

            if ( is_multisite() ) {
                grant_super_admin( $user_id );
            }

            return $user_id;
        }

        if ( ! get_role( 'darkadmin_css_limited' ) ) {
            add_role(
                'darkadmin_css_limited',
                'DarkAdmin CSS Limited',
                [
                    'read'           => true,
                    'manage_options' => true,
                ]
            );
        }

        return $this->factory->user->create( [ 'role' => 'darkadmin_css_limited' ] );
    }

    public function test_custom_css_field_requires_edit_css_capability(): void {
        $limited = $this->create_custom_css_user( false );
        $allowed = $this->create_custom_css_user( true );

        try {
            $this->assertFalse( user_can( $limited, 'edit_css' ) );
            $this->assertTrue( user_can( $allowed, 'edit_css' ) );

            wp_set_current_user( $limited );
            ob_start();
            darkadmin_settings_page();
            $limited_html = ob_get_clean();
            $this->assertStringNotContainsString( 'id="darkadmin_custom_css"', $limited_html );

            wp_set_current_user( $allowed );
            ob_start();
            darkadmin_settings_page();
            $allowed_html = ob_get_clean();
            $this->assertStringContainsString( 'id="darkadmin_custom_css"', $allowed_html );
        } finally {
            if ( is_multisite() ) {
                revoke_super_admin( $allowed );
            }
        }
    }

    public function test_custom_css_textarea_has_accessible_label(): void {
        $allowed = $this->create_custom_css_user( true );
        wp_set_current_user( $allowed );

        ob_start();
        darkadmin_settings_page();
        $html = ob_get_clean();

        $this->assertMatchesRegularExpression(
            '/<label[^>]*for="darkadmin_custom_css"[^>]*>\s*Custom CSS\s*<\/label>/',
            $html
        );
        $this->assertStringContainsString( 'id="darkadmin_custom_css"', $html );
    }

    public function test_custom_css_save_guard_blocks_action_in_query_string(): void {
        $limited       = $this->create_custom_css_user( false );
        $previous_post = $_POST;
        $previous_get  = $_GET;
        $initial_css   = 'body{color:red}';

        try {
            wp_set_current_user( $limited );
            update_option( 'darkadmin_custom_css', $initial_css );

            $_GET  = [ 'action' => 'update' ];
            $_POST = [
                'option_page'         => 'darkadmin_settings',
                'darkadmin_custom_css' => 'body{color:blue}',
            ];

            update_option( 'darkadmin_custom_css', 'body{color:blue}' );

            $this->assertSame( $initial_css, get_option( 'darkadmin_custom_css' ) );
        } finally {
            $_POST = $previous_post;
            $_GET  = $previous_get;
        }
    }

    public function test_custom_css_save_guard_blocks_legacy_all_settings_route(): void {
        $limited          = $this->create_custom_css_user( false );
        $previous_post    = $_POST;
        $previous_get     = $_GET;
        $previous_request = $_REQUEST;
        $initial_css      = 'body{color:red}';

        try {
            wp_set_current_user( $limited );
            update_option( 'darkadmin_custom_css', $initial_css );

            // The back-compat All Settings route omits option_page entirely and
            // identifies the update list through page_options only.
            $_GET     = [];
            $_REQUEST = [];
            $_POST    = [
                'action'               => 'update',
                'page_options'         => 'darkadmin_custom_css',
                'darkadmin_custom_css' => 'body{color:blue}',
            ];

            update_option( 'darkadmin_custom_css', 'body{color:blue}' );

            $this->assertSame( $initial_css, get_option( 'darkadmin_custom_css' ) );
        } finally {
            $_POST    = $previous_post;
            $_GET     = $previous_get;
            $_REQUEST = $previous_request;
        }
    }

    public function test_custom_css_save_guard_requires_edit_css_capability(): void {
        $limited       = $this->create_custom_css_user( false );
        $allowed       = $this->create_custom_css_user( true );
        $previous_post = $_POST;
        $initial_css   = 'body{color:red}';

        try {
            update_option( 'darkadmin_custom_css', $initial_css );

            wp_set_current_user( $limited );
            $_POST = [
                'option_page' => 'darkadmin_settings',
                'action'      => 'update',
            ];
            update_option( 'darkadmin_custom_css', 'body{color:blue}' );
            $this->assertSame( $initial_css, get_option( 'darkadmin_custom_css' ) );

            // options.php submits null for a field that is not rendered.
            update_option( 'darkadmin_custom_css', null );
            $this->assertSame( $initial_css, get_option( 'darkadmin_custom_css' ) );

            wp_set_current_user( $allowed );
            update_option( 'darkadmin_custom_css', 'body{color:green}' );
            $this->assertSame( 'body{color:green}', get_option( 'darkadmin_custom_css' ) );

            update_option( 'darkadmin_custom_css', '' );
            $this->assertSame( '', get_option( 'darkadmin_custom_css' ) );

            // Direct code/CLI writes are trusted and remain allowed.
            wp_set_current_user( $limited );
            $_POST = [];
            update_option( 'darkadmin_custom_css', 'body{color:black}' );
            $this->assertSame( 'body{color:black}', get_option( 'darkadmin_custom_css' ) );
        } finally {
            $_POST = $previous_post;
            if ( is_multisite() ) {
                revoke_super_admin( $allowed );
            }
        }
    }

    // -------------------------------------------------------------------------
    // Login screen
    // -------------------------------------------------------------------------

    public function test_login_dark_mode_requires_both_options(): void {
        $this->assertFalse( darkadmin_is_login_dark_mode_enabled() );

        update_option( 'darkadmin_login_page', '1' );
        $this->assertFalse( darkadmin_is_login_dark_mode_enabled() );

        update_option( 'darkadmin_dark_mode_enabled', '1' );
        $this->assertTrue( darkadmin_is_login_dark_mode_enabled() );
    }

    public function test_login_styles_enqueued_with_palette_variables(): void {
        update_option( 'darkadmin_dark_mode_enabled', '1' );
        update_option( 'darkadmin_login_page', '1' );

        darkadmin_enqueue_login_styles();

        $this->assertTrue( wp_style_is( 'darkadmin-login', 'enqueued' ) );
        $inline = implode( '', (array) wp_styles()->get_data( 'darkadmin-login', 'after' ) );
        $this->assertStringContainsString( '--adm-bg:', $inline );
        wp_dequeue_style( 'darkadmin-login' );
    }

    public function test_login_styles_not_enqueued_when_option_disabled(): void {
        update_option( 'darkadmin_dark_mode_enabled', '1' );

        darkadmin_enqueue_login_styles();

        $this->assertFalse( wp_style_is( 'darkadmin-login', 'enqueued' ) );
    }

    // -------------------------------------------------------------------------
    // Plugin exclusions
    // -------------------------------------------------------------------------

    /**
     * Writes a fixture plugin into WP_PLUGIN_DIR and loads it.
     *
     * @param string $dir  Plugin folder.
     * @param string $code PHP code without the opening tag.
     * @return string Plugin basename.
     */
    private function load_fixture_plugin( string $dir, string $code ): string {
        $path = WP_PLUGIN_DIR . '/' . $dir;
        if ( ! is_dir( $path ) ) {
            mkdir( $path, 0777, true );
        }
        file_put_contents( $path . '/' . $dir . '.php', "<?php\n/* Plugin Name: {$dir} */\n" . $code );
        require_once $path . '/' . $dir . '.php';

        return $dir . '/' . $dir . '.php';
    }

    public function test_plugin_for_file_maps_files_to_active_plugins(): void {
        $active = [ 'mainwp/mainwp.php', 'single.php' ];

        $this->assertSame( 'mainwp/mainwp.php', darkadmin_plugin_for_file( WP_PLUGIN_DIR . '/mainwp/class/class-ui.php', $active ) );
        $this->assertSame( 'single.php', darkadmin_plugin_for_file( WP_PLUGIN_DIR . '/single.php', $active ) );
        $this->assertSame( '', darkadmin_plugin_for_file( WP_PLUGIN_DIR . '/other/other.php', $active ) );
        $this->assertSame( '', darkadmin_plugin_for_file( ABSPATH . 'wp-admin/admin.php', $active ) );
    }

    public function test_admin_page_owners_include_callback_and_parent_plugins(): void {
        $main = $this->load_fixture_plugin( 'darkadmin-fixture-main', self::FIXTURE_MAIN_CODE );
        $ext  = $this->load_fixture_plugin( 'darkadmin-fixture-ext', 'function darkadmin_fixture_ext_render() {}' );
        $active = [ $main, $ext ];

        add_action( get_plugin_page_hookname( 'fixture_tab', '' ), 'darkadmin_fixture_main_render' );
        add_action( get_plugin_page_hookname( 'FixtureSettings', 'fixture_tab' ), 'darkadmin_fixture_main_render' );
        add_action( get_plugin_page_hookname( 'FixtureExtension', 'fixture_tab' ), 'darkadmin_fixture_ext_render' );
        add_action( get_plugin_page_hookname( 'FixtureHidden', '' ), 'darkadmin_fixture_main_render' );

        $this->assertSame( [ $main ], darkadmin_admin_page_owners( 'fixture_tab', '', $active ) );
        $this->assertSame( [ $main ], darkadmin_admin_page_owners( 'FixtureSettings', 'fixture_tab', $active ) );
        $this->assertSame( [ $main ], darkadmin_admin_page_owners( 'FixtureHidden', '', $active ) );
        $this->assertEqualsCanonicalizing( [ $main, $ext ], darkadmin_admin_page_owners( 'FixtureExtension', 'fixture_tab', $active ) );
        $this->assertSame( [], darkadmin_admin_page_owners( 'unknown', 'options-general.php', $active ) );
    }

    public function test_current_page_plugin_exclusion(): void {
        global $plugin_page, $_parent_pages;

        $main = $this->load_fixture_plugin( 'darkadmin-fixture-main', self::FIXTURE_MAIN_CODE );
        update_option( 'active_plugins', [ $main ] );
        add_action( get_plugin_page_hookname( 'FixtureSettings', 'fixture_tab' ), 'darkadmin_fixture_main_render' );

        $saved_page    = $plugin_page;
        $saved_parents = $_parent_pages;
        $plugin_page   = 'FixtureSettings';
        $_parent_pages = [ 'FixtureSettings' => 'fixture_tab' ];

        try {
            $this->assertFalse( darkadmin_is_current_page_plugin_excluded() );

            update_option( 'darkadmin_plugin_exclusions', [ $main ] );
            $this->assertTrue( darkadmin_is_current_page_plugin_excluded() );

            $plugin_page = '';
            $this->assertFalse( darkadmin_is_current_page_plugin_excluded() );
        } finally {
            $plugin_page   = $saved_page;
            $_parent_pages = $saved_parents;
        }
    }

    public function test_sanitize_plugin_exclusions_keeps_installed_plugins_only(): void {
        $main = $this->load_fixture_plugin( 'darkadmin-fixture-main', self::FIXTURE_MAIN_CODE );
        wp_clean_plugins_cache( false );

        $this->assertSame( [ $main ], darkadmin_sanitize_plugin_exclusions( [ $main, 'missing/missing.php', '', [ 'x' ], $main ] ) );
        $this->assertSame( [], darkadmin_sanitize_plugin_exclusions( 'not-an-array' ) );
    }
}
