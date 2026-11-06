<?php
/**
 * Class for post stats.
 *
 * @package BricksContentToolkit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class to work out Post Stats.
 */
class BCT_Post_Stats {

	/**
	 * Average reading time speed used for reading time.
	 */
	private const WORDS_PER_MINUTE = 200;

	/**
	 * Count the words in the post's content.
	 *
	 * @param WP_Post|null $post Post to count.
	 * @return int
	 */
	public function get_word_count( $post ): int {
		if ( ! $post instanceof WP_Post ) {
			return 0;
		}

		$text = wp_strip_all_tags( $post->post_content );

		return str_word_count( $text );
	}

	/**
	 * Estimate how many minutes it takes to read.
	 *
	 * @param WP_Post|null $post Post to measure.
	 * @return int
	 */
	public function get_reading_time( $post ): int {
		$words = $this->get_word_count( $post );

		return (int) ceil( $words / self::WORDS_PER_MINUTE );
	}
}
