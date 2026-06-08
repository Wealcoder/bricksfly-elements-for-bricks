<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Bricks_Animation_Addons
 * @subpackage Bricks_Animation_Addons/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bricks_Animation_Addons
 * @subpackage Bricks_Animation_Addons/public
 * @author     Zilani <zilani.wealcoder@gmail.com>
 */
class Bricks_Animation_Addons_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, AAB_ADDONS_URL . 'public/build/frontend.css', array('bricks-frontend'), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		// All GSAP libs and the widgets/extensions that consume them moved to
		// the Pro plugin. Pro registers and enqueues the `gsap`,
		// `scroll-to-plugin`, `split-text`, `scroll-trigger`, and
		// `scroll-smoother` handles via includes/core/lib-scripts.php.

		// Base plugin script.
		wp_enqueue_script($this->plugin_name, AAB_ADDONS_URL . 'public/build/frontend.js', array(), $this->version, false);
	}
}
