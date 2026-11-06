<?php
/**
 * Plugin settings page.
 *
 * @package BricksContentToolkit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Adds the plugin's settings page to the WordPress admin.
 */
class BCT_Settings {

	/**
	 * Hook the settings page to the WordPress admin.
	 */
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
	}

	/**
	 * Add the page to the Settings menu.
	 */
	public function add_page(): void {
		add_options_page(
			__( 'Bricks Content Toolkit', 'bricks-content-toolkit' ),
			__( 'Bricks Content Toolkit', 'bricks-content-toolkit' ),
			'manage_options',
			'bct-settings',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Print the settings page.
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		echo '<div class="wrap"><h1>' . esc_html( get_admin_page_title() ) . '</h1></div>';
	}
}
