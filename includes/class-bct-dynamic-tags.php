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
	 * Hook the tags into Bricks.
	 */
	public function register(): void {
		add_filter( 'bricks/dynamic_tags_list', array( $this, 'add_tags_to_builder' ) );
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

		return $tags;
	}
}
