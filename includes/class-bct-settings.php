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
		add_action( 'admin_init', array( $this, 'register_settings' ) );
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
	 * Register the setting, its section, and its field.
	 */
	public function register_settings(): void {
		register_setting(
			'bct_settings',
			'bct_words_per_minute',
			array(
				'type'              => 'integer',
				'sanitize_callback' => array( $this, 'sanitize_words_per_minute' ),
				'default'           => BCT_Post_Stats::WORDS_PER_MINUTE,
			)
		);

		add_settings_section(
			'bct_reading_time',
			__( 'Reading time', 'bricks-content-toolkit' ),
			'__return_empty_string',
			'bct-settings'
		);

		add_settings_field(
			'bct_words_per_minute',
			__( 'Words per minute', 'bricks-content-toolkit' ),
			array( $this, 'render_words_per_minute_field' ),
			'bct-settings',
			'bct_reading_time',
			array( 'label_for' => 'bct_words_per_minute' )
		);
	}

	/**
	 * Make a submitted reading speed safe to save.
	 *
	 * @param mixed $value Submitted value.
	 * @return int
	 */
	public function sanitize_words_per_minute( $value ): int {
		$words_per_minute = (int) $value;

		if ( $words_per_minute < 1 ) {
			return BCT_Post_Stats::WORDS_PER_MINUTE;
		}

		return $words_per_minute;
	}

	/**
	 * Print the words per minute input.
	 */
	public function render_words_per_minute_field(): void {
		$value = get_option( 'bct_words_per_minute', BCT_Post_Stats::WORDS_PER_MINUTE );

		echo '<input type="number" min="1" class="small-text" id="bct_words_per_minute" name="bct_words_per_minute" value="' . esc_attr( $value ) . '">';
	}

	/**
	 * Print the settings page.
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		echo '<div class="wrap"><h1>' . esc_html( get_admin_page_title() ) . '</h1>';
		echo '<form action="options.php" method="post">';

		settings_fields( 'bct_settings' );
		do_settings_sections( 'bct-settings' );
		submit_button();

		echo '</form></div>';
	}
}
