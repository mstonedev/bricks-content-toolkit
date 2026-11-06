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
	 * Calculates the post statistics shown by the tags.
	 *
	 * @var BCT_Post_Stats
	 */
	private BCT_Post_Stats $post_stats;

	/**
	 * Store the object used to calculate post statistics.
	 *
	 * @param BCT_Post_Stats $post_stats Post statistics calculator.
	 */
	public function __construct( BCT_Post_Stats $post_stats ) {
		$this->post_stats = $post_stats;
	}

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
		foreach ( $this->get_tags() as $name => $label ) {
			$tags[] = array(
				'name'  => '{' . $name . '}',
				'label' => $label,
				'group' => __( 'Content Toolkit', 'bricks-content-toolkit' ),
			);
		}

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

		$name  = str_replace( array( '{', '}' ), '', $tag );
		$value = $this->get_tag_value( $name, $post );

		return $value ?? $tag;
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

		foreach ( array_keys( $this->get_tags() ) as $name ) {
			$placeholder = '{' . $name . '}';

			if ( str_contains( $content, $placeholder ) ) {
				$content = str_replace( $placeholder, (string) $this->get_tag_value( $name, $post ), $content );
			}
		}

		return $content;
	}

	/**
	 * List this plugin's tags as name => label.
	 *
	 * @return array<string, string>
	 */
	private function get_tags(): array {
		return array(
			'bct_word_count'   => __( 'Word Count', 'bricks-content-toolkit' ),
			'bct_reading_time' => __( 'Reading Time (minutes)', 'bricks-content-toolkit' ),
			'bct_post_age'     => __( 'Post Age (days)', 'bricks-content-toolkit' ),
		);
	}

	/**
	 * Work out the value of one of the plugin's tags.
	 *
	 * @param string       $name Tag name without curly braces.
	 * @param WP_Post|null $post Post being rendered.
	 * @return string|null The value, or null if the tag is not ours.
	 */
	private function get_tag_value( string $name, $post ): ?string {
		return match ( $name ) {
			'bct_word_count' => (string) $this->post_stats->get_word_count( $post ),
			'bct_reading_time' => (string) $this->post_stats->get_reading_time( $post ),
			'bct_post_age' => (string) $this->get_post_age( $post ),
			default => null,
		};
	}

	/**
	 * Count the whole days since a post was published.
	 *
	 * @param WP_Post|null $post Post to measure.
	 * @return int
	 */
	private function get_post_age( $post ): int {
		$published = get_post_timestamp( $post );

		if ( false === $published ) {
			return 0;
		}

		$days = (int) floor( ( time() - $published ) / DAY_IN_SECONDS );

		return max( 0, $days );
	}
}
