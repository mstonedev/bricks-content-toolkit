<?php
/**
 * Custom Bricks Reading Time element.
 *
 * @package BricksContentToolkit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shows an estimated reading time for the current post.
 */
class BCT_Element_Reading_Time extends \Bricks\Element {

	/**
	 * Group shown in the Bricks elements panel.
	 *
	 * @var string
	 */
	public $category = 'general';

	/**
	 * Unique element identifier.
	 *
	 * @var string
	 */
	public $name = 'bct-reading-time';

	/**
	 * Icon shown in the Bricks elements panel.
	 *
	 * @var string
	 */
	public $icon = 'ti-time';

	/**
	 * Return the element's name in the elements panel.
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Reading Time', 'bricks-content-toolkit' );
	}

	/**
	 * Add the element's settings to the Bricks builder.
	 */
	public function set_controls() {
		$this->controls['suffix'] = array(
			'label'       => esc_html__( 'Text after number', 'bricks-content-toolkit' ),
			'type'        => 'text',
			'inline'      => true,
			'placeholder' => esc_html__( 'min read', 'bricks-content-toolkit' ),
		);

		$this->controls['wordsPerMinute'] = array(
			'label'       => esc_html__( 'Words per minute', 'bricks-content-toolkit' ),
			'type'        => 'number',
			'min'         => 1,
			'inline'      => true,
			'placeholder' => 200,
		);
	}

	/**
	 * Print the element's HTML.
	 */
	public function render() {
		if ( BCT_Post_Stats::is_measuring() ) {
			return;
		}

		$post_stats       = new BCT_Post_Stats();
		$words_per_minute = (int) ( $this->settings['wordsPerMinute'] ?? 0 );
		$minutes          = $post_stats->get_reading_time( get_post( $this->post_id ), $words_per_minute );
		$suffix           = $this->settings['suffix'] ?? __( 'min read', 'bricks-content-toolkit' );

		echo '<div ' . $this->render_attributes( '_root' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Bricks generates this attribute string.

		echo esc_html( $minutes . ' ' . $suffix );
		echo '</div>';
	}
}
