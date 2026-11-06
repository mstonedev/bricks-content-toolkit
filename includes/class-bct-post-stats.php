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
	 * Whether Bricks content is currently being rendered for measuring.
	 *
	 * @var bool
	 */
	private static bool $is_measuring = false;

	/**
	 * Word counts already worked out, keyed by post ID.
	 *
	 * @var array<int, int>
	 */
	private static array $word_counts = array();

	/**
	 * Check whether Bricks content is currently being rendered for measuring.
	 *
	 * @return bool
	 */
	public static function is_measuring(): bool {
		return self::$is_measuring;
	}

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

		if ( isset( self::$word_counts[ $post->ID ] ) ) {
			return self::$word_counts[ $post->ID ];
		}

		$text = $this->get_bricks_text( $post );

		if ( '' === $text ) {
			$text = wp_strip_all_tags( $post->post_content );
		}

		$count = str_word_count( $text );

		if ( ! self::$is_measuring ) {
			self::$word_counts[ $post->ID ] = $count;
		}

		return $count;
	}

	/**
	 * Estimate how many minutes it takes to read.
	 *
	 * @param WP_Post|null $post             Post to measure.
	 * @param int          $words_per_minute Reading speed. Below 1 uses the default.
	 * @return int
	 */
	public function get_reading_time( $post, int $words_per_minute = self::WORDS_PER_MINUTE ): int {
		if ( $words_per_minute < 1 ) {
			$words_per_minute = self::WORDS_PER_MINUTE;
		}

		$words = $this->get_word_count( $post );

		return (int) ceil( $words / $words_per_minute );
	}

	/**
	 * Render a post's Bricks content and return its plain text.
	 *
	 * @param WP_Post $post Post to render.
	 * @return string
	 */
	private function get_bricks_text( WP_Post $post ): string {
		if ( self::$is_measuring ) {
			return '';
		}

		$elements = get_post_meta( $post->ID, BRICKS_DB_PAGE_CONTENT, true );

		if ( ! is_array( $elements ) || array() === $elements ) {
			return '';
		}

		$previous_elements  = \Bricks\Frontend::$elements;
		$previous_area      = \Bricks\Frontend::$area;
		self::$is_measuring = true;

		try {
			$html = \Bricks\Frontend::render_data( $elements, 'content', $post->ID );

			return wp_strip_all_tags( str_replace( '<', ' <', $html ) );
		} finally {
			\Bricks\Frontend::$elements = $previous_elements;
			\Bricks\Frontend::$area     = $previous_area;
			self::$is_measuring         = false;
		}
	}
}
