<?php
/**
 * Custom Bricks Reading Time element.
 *
 * @package BricksContentToolkit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the reading time element and extends \Bricks\Element.
 */
class BCT_Element_Reading_Time extends \Bricks\Element {

	/**
	 * Group shown and Bricks elements panel.
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
	 * Icon shownn in Bricks elements panel.
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
	 * Print the elements HTML.
	 */
	public function render() {
		echo '<div ' . $this->render_attributes( '_root' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Bricks generates this attribute string.
		echo esc_html__( 'X min read', 'bricks-content-toolkit' );
		echo '</div>';
	}
}
