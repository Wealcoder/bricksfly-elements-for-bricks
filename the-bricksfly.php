<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://bricksfly.com
 * @since             1.0.0
 * @package           Bricks_Fly
 *
 * @wordpress-plugin
 * Plugin Name:       The BricksFly – Bricks Elements, Bricks Templates & GSAP Animation
 * Plugin URI:        https://bricksfly.com/
 * Description:       The Bricksfly for Bricks comes with GSAP Animation Builder, Customizable Elements, Header Footer, Single Post, Archive Page Builder, and Many more.
 * Version:           1.0.1
 * Author:            Wealcoder
 * Author URI:        https://bricksfly.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       the-bricksfly
 * Domain Path:       /languages
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Tested up to:      7.0
 */

// If this file is called directly, abort.
if (! defined('ABSPATH')) {
	exit;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if (! defined('THEBRBRE_FILE')) {
	/**
	 * Plugin File Ref.
	 */
	define('THEBRBRE_FILE', __FILE__);
}
if (! defined('THEBRBRE_BASE')) {
	/**
	 * Plugin Base Name.
	 */
	define('THEBRBRE_BASE', plugin_basename(THEBRBRE_FILE));
}
if (! defined('THEBRBRE_PATH')) {
	/**
	 * Plugin Dir Ref.
	 */
	define('THEBRBRE_PATH', plugin_dir_path(THEBRBRE_FILE));
}

if (! defined('THEBRBRE_URL')) {
	/**
	 * Plugin URL.
	 */
	define('THEBRBRE_URL', plugin_dir_url(THEBRBRE_FILE));
}

if (! defined('THEBRBRE_VERSION')) {
	/**
	 * Plugin Version.
	 */
	define('THEBRBRE_VERSION', '1.0.0');
}

if (! defined('THEBRBRE_TEMPLATE_STARTER_BASE_URL')) {
	/**
	 * Template Path
	 */
	define('THEBRBRE_TEMPLATE_STARTER_BASE_URL', 'https://www.themecrowdy.com/');
}

/**
 * Shared EDD Software Licensing identifiers.
 *
 * The license activation/deactivation handlers live in the Pro plugin
 * (includes/license/update.php), but the free dashboard reads these
 * constants when localizing the React UI. Defining them here keeps the
 * dashboard renderable when Pro is inactive. Pro's own files re-define
 * the same names with `! defined()` guards, so this is safe.
 */
if (! defined('THEBRBRE_PRO_STORE_URL')) {
	define('THEBRBRE_PRO_STORE_URL', 'https://my.bricksfly.com/');
}
if (! defined('THEBRBRE_PRO_ITEM_ID')) {
	define('THEBRBRE_PRO_ITEM_ID', 39996);
}
if (! defined('THEBRBRE_PRO_ITEM_NAME')) {
	define('THEBRBRE_PRO_ITEM_NAME', 'TheBricksFly');
}

/**
 * The code that runs during plugin activation
 * This action is documented in includes/class-bricks-animation-addons-activator.php
 */
function thebrbre_activate($network_wide = false)
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-bricks-animation-addons-activator.php';
	THEBRBRE_Activator::activate($network_wide);
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bricks-animation-addons-deactivator.php
 */
function thebrbre_deactivate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-bricks-animation-addons-deactivator.php';
	THEBRBRE_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'thebrbre_activate');
register_deactivation_hook(__FILE__, 'thebrbre_deactivate');

// Bootstrap: config, helpers, trait loaded before the main class.
require_once THEBRBRE_PATH . 'config.php';
require_once THEBRBRE_PATH . 'includes/helper.php';
require_once THEBRBRE_PATH . 'includes/hook.php';
// Backward-compat aliases for the previous symbol names (keeps an already
// installed the-bricksfly-pro build working after the rename). Loaded after
// helper.php/constants so the new symbols exist before the shims reference them.
require_once THEBRBRE_PATH . 'includes/compat-legacy.php';
require_once THEBRBRE_PATH . 'includes/traits/Extension_Widgets_Trait.php';
require_once THEBRBRE_PATH . 'includes/class-bricks-theme-dependency.php';

// Literal translation map for config.php labels, titles, and descriptions.
// The map is evaluated only when the dashboard config filter runs on init.
require_once THEBRBRE_PATH . 'includes/config-i18n.php';

// Group Icon Helper: single source of truth for the plugin-branded
// control-group title rendered in Bricks element panels. Used by Starter
// Animations below and proxied by the Pro plugin's GroupHelper so both
// plugins share one icon/markup definition.
require_once THEBRBRE_PATH . 'includes/extensions/helpers/Label_Name_Helper.php';

// Starter Animations: injects an animation control group into Bricks core
// elements (heading, text, image, container, etc.) and ships the front-end
// engine that plays the animations on viewport-enter.
require_once THEBRBRE_PATH . 'includes/extensions/class-aab-starter-animations.php';

// The core plugin class — loads all remaining dependencies internally.
require_once THEBRBRE_PATH . 'includes/class-bricks-animation-addons.php';



/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function thebrbre_run()
{

	$plugin = new THEBRBRE_Plugin();
	$plugin->run();

	// Localize THEBRBRE_ADDONS_JS against the always-enqueued public script so the
	// global is available to free elements (e.g. post-social-share) AND to
	// Pro extensions (smooth scroller, scroll-to). The scroll-to runtime
	// (scroll-to-el.js) itself lives in Pro and is enqueued by the Pro
	// scrollto extension.
	add_action('wp_enqueue_scripts', function () {
		$data = apply_filters('thebrbre_js_data',
			array(
				'ajaxUrl'        => admin_url('admin-ajax.php'),
				'post_id'        => get_the_ID(),
				'i18n'           => array(
					'okay'    => esc_html__('Okay', 'the-bricksfly'),
					'cancel'  => esc_html__('Cancel', 'the-bricksfly'),
					'submit'  => esc_html__('Submit', 'the-bricksfly'),
					'success' => esc_html__('Success', 'the-bricksfly'),
					'warning' => esc_html__('Warning', 'the-bricksfly'),
				),
				'smoothScroller' => json_decode(get_option('thebrbre_smooth_scroller')),
				// All Bricks breakpoints (defaults + custom). Empty array if
				// Bricks isn't active so JS can rely on a consistent shape.
				'breakpoints'    => class_exists('\\Bricks\\Breakpoints')
					? \Bricks\Breakpoints::get_breakpoints()
					: array(),
			)
		);

		wp_localize_script('bricksfly', 'THEBRBRE_ADDONS_JS', $data);
	}, 20);
}
thebrbre_run();
