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

		add_settings_section(
			'bct_features',
			__( 'Features', 'bricks-content-toolkit' ),
			'__return_empty_string',
			'bct-settings'
		);

		foreach ( $this->get_features() as $option => $label ) {
			register_setting(
				'bct_settings',
				$option,
				array(
					'type'              => 'integer',
					'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
					'default'           => 1,
				)
			);

			add_settings_field(
				$option,
				$label,
				array( $this, 'render_feature_field' ),
				'bct-settings',
				'bct_features',
				array( 'label_for' => $option )
			);
		}
	}

	/**
	 * List the features that can be switched on or off as option => label.
	 *
	 * @return array<string, string>
	 */
	private function get_features(): array {
		return array(
			'bct_enable_dynamic_tags' => __( 'Dynamic tags', 'bricks-content-toolkit' ),
			'bct_enable_reading_time' => __( 'Reading Time element', 'bricks-content-toolkit' ),
		);
	}

	/**
	 * Check whether a feature is switched on.
	 *
	 * @param string $option Feature's option name.
	 * @return bool
	 */
	public function is_feature_enabled( string $option ): bool {
		return 1 === (int) get_option( $option, 1 );
	}

	/**
	 * Turn a submitted checkbox into 1 or 0.
	 *
	 * @param mixed $value Submitted value.
	 * @return int
	 */
	public function sanitize_checkbox( $value ): int {
		return empty( $value ) ? 0 : 1;
	}

	/**
	 * Print a feature's checkbox.
	 *
	 * @param array $args Field arguments.
	 */
	public function render_feature_field( array $args ): void {
		$option  = $args['label_for'];
		$enabled = $this->is_feature_enabled( $option );

		echo '<input type="checkbox" value="1" id="' . esc_attr( $option ) . '" name="' . esc_attr( $option ) . '" ' . checked( $enabled, true, false ) . '>';
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
