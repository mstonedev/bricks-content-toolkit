<?php
/**
 * Custom Bricks dynamic data tags.
 *
 * @package BricksContentToolkit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers and renders the plugin's dynamic data tags.
 */
class BCT_Dynamic_Tags {

	/**
	 * Average reading time speed used for reading time.
	 */
	private const WORDS_PER_MINUTE = 200;

	/**
	 * Hook the tags into Bricks.
	 */
	public function register(): void {
		add_filter( 'bricks/dynamic_tags_list', array( $this, 'add_tags_to_builder' ) );
		add_filter( 'bricks/dynamic_data/render_tag', array( $this, 'render_tag' ), 20, 2 );
		add_filter( 'bricks/dynamic_data/render_content', array( $this, 'render_content' ), 20, 2 );
		add_filter( 'bricks/frontend/render_data', array( $this, 'render_content' ), 20, 2 );
	}

	/**
	 * Add the plugin's tags to the Bricks dynamic data picker.
	 *
	 * @param array $tags Tags already registered with Bricks.
	 * @return array
	 */
	public function add_tags_to_builder( array $tags ): array {
		$tags[] = array(
			'name'  => '{bct_word_count}',
			'label' => __( 'Word Count', 'bricks-content-toolkit' ),
			'group' => __( 'Content Toolkit', 'bricks-content-toolkit' ),
		);

		$tags[] = array(
			'name'  => '{bct_reading_time}',
			'label' => __( 'Reading Time (minutes)', 'bricks-content-toolkit' ),
			'group' => __( 'Content Toolkit', 'bricks-content-toolkit' ),
		);

		return $tags;
	}

	/**
	 * Return the value when a field contains only one of the plugin's tags.
	 *
	 * @param mixed   $tag Tag name with or without curly braces.
	 * @param WP_Post $post Post being rendered.
	 * @return mixed
	 */
	public function render_tag( $tag, $post ) {
		if ( ! is_string( $tag ) ) {
			return $tag;
		}

		$name = str_replace( array( '{', '}' ), '', $tag );

		if ( 'bct_word_count' === $name ) {
			return $this->get_word_count( $post );
		}

		if ( 'bct_reading_time' === $name ) {
			return $this->get_reading_time( $post );
		}

		return $tag;
	}

	/**
	 * Replace the plugin's tags inside longer text.
	 *
	 * @param mixed   $content Text that may contain tags.
	 * @param WP_Post $post    Post being rendered.
	 * @return mixed
	 */
	public function render_content( $content, $post ) {
		if ( ! is_string( $content ) ) {
			return $content;
		}

		if ( str_contains( $content, '{bct_word_count}' ) ) {
			$content = str_replace( '{bct_word_count}', (string) $this->get_word_count( $post ), $content );
		}

		if ( str_contains( $content, '{bct_reading_time}' ) ) {
			$content = str_replace( '{bct_reading_time}', (string) $this->get_reading_time( $post ), $content );
		}

		return $content;
	}

	/**
	 * Count the words in the post's content.
	 *
	 * @param WP_Post|null $post Post to count.
	 * @return int
	 */
	private function get_word_count( $post ): int {
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
	private function get_reading_time( $post ): int {
		$words = $this->get_word_count( $post );

		return (int) ceil( $words / self::WORDS_PER_MINUTE );
	}
}
