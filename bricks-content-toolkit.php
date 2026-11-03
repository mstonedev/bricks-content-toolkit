<?php
/**
 * Bricks Content Toolkit
 *
 * @package BricksContentToolkit
 * @author Michael Stone
 * @copyright 2026 Michael Stone
 * @license GPL-2.0-or-later
 *
 * Plugin Name: Bricks Content Toolkit
 * Plugin URI: https://michaelstone.dev/
 * Description: Custom Bricks elements for article content.
 * Version: 0.1.0
 * Requires at least: 6.5
 * Requires PHP: 8.3
 * Author: Michael Stone
 * Author URI: https://michaelstone.dev/
 * Text Domain: bricks-content-toolkit
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/includes/class-bct-plugin.php';

$bct_plugin = new BCT_Plugin( '...' );
$bct_plugin->register();
