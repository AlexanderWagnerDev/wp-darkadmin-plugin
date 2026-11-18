<?php
/**
 * Tests for the repository automation that ships the plugin.
 */

class DarkAdminWorkflowTest extends WP_UnitTestCase {

    /**
     * Reads a workflow file.
     *
     * @param string $name File name inside .github/workflows.
     * @return string
     */
    private function workflow( string $name ): string {
        $path = dirname( __DIR__ ) . '/.github/workflows/' . $name;
        $this->assertFileIsReadable( $path );

        return (string) file_get_contents( $path );
    }

    public function test_language_workflow_only_rewrites_the_opening_tag(): void {
        $yaml = $this->workflow( 'compile-languages.yml' );

        // wp i18n make-php writes a single-line return array. An unscoped
        // s/<?php/.../ also rewrites a "<?php" inside a translated string,
        // which closes the string and makes the generated catalog invalid PHP.
        $this->assertMatchesRegularExpression(
            "/sed -i '1s\/<\\?php\//",
            $yaml,
            'The ABSPATH guard must only replace the opening tag on line 1.'
        );
        $this->assertStringNotContainsString( "sed -i 's/<?php/", $yaml );

        // The workflow must also fail before committing a catalog that does not parse.
        $this->assertStringContainsString( 'php -l', $yaml, 'Generated catalogs must be linted before the bot commits them.' );
    }

    public function test_markdown_workflow_pins_npm_packages_and_skips_install_scripts(): void {
        $yaml = $this->workflow( 'format-markdown.yml' );

        $this->assertStringContainsString( '--ignore-scripts', $yaml, 'Install scripts must not run in a write-scoped job.' );

        // Floating ranges resolve to whatever the registry publishes next; the
        // job pushes to main, so every tool must be pinned to an exact version.
        foreach ( [ 'prettier@', '@prettier/plugin-xml@', 'markdownlint-cli2@' ] as $package ) {
            $this->assertMatchesRegularExpression(
                '/' . preg_quote( $package, '/' ) . '\d+\.\d+\.\d+/',
                $yaml,
                $package . ' must be pinned to an exact version.'
            );
        }

        $this->assertDoesNotMatchRegularExpression( '/prettier@3\s/', $yaml );
        $this->assertDoesNotMatchRegularExpression( '/npm install[^\n]*markdownlint-cli2(\s|$)/', $yaml );
    }

    public function test_release_packages_exclude_audit_and_agent_workspaces(): void {
        // The release workflow runs in a clean checkout, but a maintainer can
        // run the same packaging commands locally, where the audit workspaces
        // exist. Neither the distribution manifest nor the ZIP build may copy
        // findings or agent configuration into the shipped plugin.
        $distignore = (string) file_get_contents( dirname( __DIR__ ) . '/.distignore' );
        $lines      = preg_split( '/\R/', $distignore ) ?: array();
        $zip        = $this->workflow( 'build-zip.yml' );

        foreach ( array( '.bug-hunter', '.cloudflare-security-audit', '.commandcode' ) as $directory ) {
            $this->assertContains( $directory, $lines, $directory . ' must be excluded from the release ZIP.' );
            $this->assertStringContainsString(
                "-not -path './" . $directory . "/*'",
                $zip,
                $directory . ' must be excluded from the build-zip workflow.'
            );
        }
    }
}
