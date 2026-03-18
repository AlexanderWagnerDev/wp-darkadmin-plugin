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

    public function test_preset_colors_contains_default_and_modern(): void {
        $presets = darkadmin_preset_colors();
        $this->assertArrayHasKey( 'default', $presets );
        $this->assertArrayHasKey( 'modern', $presets );
    }

    public function test_preset_colors_default_matches_default_colors(): void {
        $this->assertSame( darkadmin_default_colors(), darkadmin_preset_colors()['default'] );
    }

    public function test_preset_colors_modern_has_all_keys(): void {
        $default_keys = array_keys( darkadmin_default_colors() );
        $modern_keys  = array_keys( darkadmin_preset_colors()['modern'] );
        $this->assertSame( $default_keys, $modern_keys );
    }

    public function test_preset_colors_returns_same_instance_on_repeated_calls(): void {
        $first  = darkadmin_preset_colors();
        $second = darkadmin_preset_colors();
        $this->assertSame( $first, $second );
    }

    // -------------------------------------------------------------------------
    // darkadmin_preset_css_file
    // -------------------------------------------------------------------------

    public function test_preset_css_file_default(): void {
        $this->assertSame( 'darkadmin-dark.css', darkadmin_preset_css_file( 'default' ) );
    }

    public function test_preset_css_file_modern(): void {
        $this->assertSame( 'darkadmin-wp-modern.css', darkadmin_preset_css_file( 'modern' ) );
    }

    public function test_preset_css_file_unknown_falls_back_to_default(): void {
        $this->assertSame( 'darkadmin-dark.css', darkadmin_preset_css_file( 'unknown-preset' ) );
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
        $this->assertSame( sort( $color_keys ), sort( $map_keys ) );
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
        $result          = darkadmin_sanitize_colors( $input );
        $this->assertSame( $defaults['bg'], $result['bg'] );
    }

    public function test_sanitize_colors_fills_missing_keys_with_defaults(): void {
        $result = darkadmin_sanitize_colors( [] );
        $this->assertSame( darkadmin_default_colors(), $result );
    }

    public function test_sanitize_colors_rejects_xss_attempt(): void {
        $input        = darkadmin_default_colors();
        $input['bg']  = '<script>alert(1)</script>';
        $result       = darkadmin_sanitize_colors( $input );
        $this->assertSame( darkadmin_default_colors()['bg'], $result['bg'] );
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

    public function test_sanitize_custom_css_strips_html_tags(): void {
        $result = darkadmin_sanitize_custom_css( '<script>alert(1)</script>body{}' );
        $this->assertStringNotContainsString( '<script>', $result );
    }

    public function test_sanitize_custom_css_allows_empty_string(): void {
        $this->assertSame( '', darkadmin_sanitize_custom_css( '' ) );
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
}
