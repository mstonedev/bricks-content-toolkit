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
	 * Message in footer.
	 *
	 * @var string
	 */
	private string $message;

	/**
	 * Store message to print.
	 *
	 * @param string $message Text to show.
	 */
	public function __construct( string $message ) {
		$this->message = $message;
	}

	/**
	 * Hook the plugin's methods into WP.
	 */
	public function register(): void {
		add_action( 'wp_footer', array( $this, 'say_hello' ) );
	}

	/**
	 * Print the message.
	 */
	public function say_hello(): void {
		echo '<p>' . esc_html( $this->message ) . '</p>';
	}
}
