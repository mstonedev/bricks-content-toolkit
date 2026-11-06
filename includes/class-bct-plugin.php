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

		require_once __DIR__ . '/class-bct-post-stats.php';
		require_once __DIR__ . '/class-bct-dynamic-tags.php';

		$dynamic_tags = new BCT_Dynamic_Tags( new BCT_Post_Stats() );
		$dynamic_tags->register();

		add_action( 'init', array( $this, 'register_elements' ), 11 );
	}

	/**
	 * Register the plugin's custom Bricks elements.
	 */
	public function register_elements(): void {
		\Bricks\Elements::register_element(
			dirname( __DIR__ ) . '/elements/class-bct-element-reading-time.php',
			'bct-reading-time',
			'BCT_Element_Reading_Time'
		);
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
