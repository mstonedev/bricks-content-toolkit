<?php
/**
 * Main plugin class.
 *
 * @package BricksContentToolkit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Boots plugin and registers its hooks.
 */
class BCT_Plugin {

	/**
	 * Hook the plugin's methods into WP.
	 */
	public function register(): void {
		add_action( 'after_setup_theme', array( $this, 'boot' ) );
	}

	/**
	 * Start the plugin's features if Bricks is active theme.
	 */
	public function boot(): void {
		if ( ! defined( 'BRICKS_VERSION' ) ) {
			add_action( 'admin_notices', array( $this, 'show_missing_bricks_notice' ) );
			return;

		}

		require_once __DIR__ . '/class-bct-dynamic-tags.php';

		$dynamic_tags = new BCT_Dynamic_Tags();
		$dynamic_tags->register();
	}

	/**
	 * Tell admins that the plugin requires the Bricks theme.
	 */
	public function show_missing_bricks_notice(): void {
		echo '<div class="notice notice-warning"><p>';
		echo esc_html__( 'Bricks Content Toolkit requires the Bricks theme to be active.', 'bricks-content-toolkit' );
		echo '</p></div>';
	}
}
