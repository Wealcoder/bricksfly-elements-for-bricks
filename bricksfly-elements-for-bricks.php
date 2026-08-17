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
 * Plugin Name:       BricksFly Elements and Templates for Bricks with GSAP Animations
 * Plugin URI:        https://bricksfly.com/
 * Description:       Bricksfly for Bricks comes with GSAP Animation Builder, Customizable Elements, Header Footer, Single Post, Archive Page Builder, and Many more.
 * Version:           1.0.2
 * Author:            Wealcoder
 * Author URI:        https://profiles.wordpress.org/wealcoder/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bricksfly-elements-for-bricks
 * Domain Path:       /languages
 * Requires at least: 6.9
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
if (! defined('BRICKSFLY_FILE')) {
	/**
	 * Plugin File Ref.
	 */
	define('BRICKSFLY_FILE', __FILE__);
}
if (! defined('BRICKSFLY_BASE')) {
	/**
	 * Plugin Base Name.
	 */
	define('BRICKSFLY_BASE', plugin_basename(BRICKSFLY_FILE));
}
if (! defined('BRICKSFLY_PATH')) {
	/**
	 * Plugin Dir Ref.
	 */
	define('BRICKSFLY_PATH', plugin_dir_path(BRICKSFLY_FILE));
}

if (! defined('BRICKSFLY_URL')) {
	/**
	 * Plugin URL.
	 */
	define('BRICKSFLY_URL', plugin_dir_url(BRICKSFLY_FILE));
}

if (! defined('BRICKSFLY_VERSION')) {
	/**
	 * Plugin Version.
	 */
	define('BRICKSFLY_VERSION', '1.0.2');
}

if (! defined('BRICKSFLY_TEMPLATE_STARTER_BASE_URL')) {
	/**
	 * Template Path
	 */
	define('BRICKSFLY_TEMPLATE_STARTER_BASE_URL', 'https://www.themecrowdy.com/');
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

if (! defined('BRICKSFLY_PRO_ITEM_ID')) {
	define('BRICKSFLY_PRO_ITEM_ID', 39996);
}
if (! defined('BRICKSFLY_PRO_ITEM_NAME')) {
	define('BRICKSFLY_PRO_ITEM_NAME', 'TheBricksFly');
}

/**
 * The code that runs during plugin activation
 * This action is documented in includes/class-bricks-animation-addons-activator.php
 */
function bricksfly_activate($network_wide = false)
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-bricks-animation-addons-activator.php';
	BRICKSFLY_Activator::activate($network_wide);
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bricks-animation-addons-deactivator.php
 */
function bricksfly_deactivate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-bricks-animation-addons-deactivator.php';
	BRICKSFLY_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'bricksfly_activate');
register_deactivation_hook(__FILE__, 'bricksfly_deactivate');

// Bootstrap: config, helpers, trait loaded before the main class.
require_once BRICKSFLY_PATH . 'config.php';
require_once BRICKSFLY_PATH . 'includes/helper.php';
require_once BRICKSFLY_PATH . 'includes/hook.php';
// BRICKSFLY_BRICKS_ELEMENTS is normally required later by
// load_dependencies() (includes/class-bricks-animation-addons.php), but
// property default at load time — require it here too (idempotent via
// require_once) so it's guaranteed to exist before Pro loads.
require_once BRICKSFLY_PATH . 'includes/extensions/helpers/BRICKS_ELEMENTS.php';
require_once BRICKSFLY_PATH . 'includes/traits/Extension_Widgets_Trait.php';
require_once BRICKSFLY_PATH . 'includes/class-bricks-theme-dependency.php';

// Literal translation map for config.php labels, titles, and descriptions.
// The map is evaluated only when the dashboard config filter runs on init.
require_once BRICKSFLY_PATH . 'includes/config-i18n.php';

// Group Icon Helper: single source of truth for the plugin-branded
// control-group title rendered in Bricks element panels. Used by Starter
// Animations below and proxied by the Pro plugin's GroupHelper so both
// plugins share one icon/markup definition.
require_once BRICKSFLY_PATH . 'includes/extensions/helpers/Label_Name_Helper.php';

// Starter Animations: injects an animation control group into Bricks core
// elements (heading, text, image, container, etc.) and ships the front-end
// engine that plays the animations on viewport-enter.
require_once BRICKSFLY_PATH . 'includes/extensions/class-aab-starter-animations.php';

// The core plugin class — loads all remaining dependencies internally.
require_once BRICKSFLY_PATH . 'includes/class-bricks-animation-addons.php';



/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function bricksfly_run()
{

	$plugin = new BRICKSFLY_Plugin();
	$plugin->run();

	// Localize BRICKSFLY_ADDONS_JS against the always-enqueued public script so the
	// global is available to free elements AND to Pro extensions/elements
	// (smooth scroller, scroll-to, post-social-share). The scroll-to runtime
	// (scroll-to-el.js) itself lives in Pro and is enqueued by the Pro
	// scrollto extension.
	add_action('wp_enqueue_scripts', function () {
		$data = apply_filters('bricksfly_js_data',
			array(
				'ajaxUrl'        => admin_url('admin-ajax.php'),
				'post_id'        => get_the_ID(),
				'i18n'           => array(
					'okay'    => esc_html__('Okay', 'bricksfly-elements-for-bricks'),
					'cancel'  => esc_html__('Cancel', 'bricksfly-elements-for-bricks'),
					'submit'  => esc_html__('Submit', 'bricksfly-elements-for-bricks'),
					'success' => esc_html__('Success', 'bricksfly-elements-for-bricks'),
					'warning' => esc_html__('Warning', 'bricksfly-elements-for-bricks'),
				),
				// Null unless Scroll Smoother can actually run — the same predicate
				// that decides whether to emit #smooth-wrapper (includes/hook.php).
				// Without this gate the JS would still read an enabled config and
				// call ScrollSmoother.create() (which builds its own wrapper when
				// ours is absent), so a plugin vetoing us through
				// `bricksfly_smooth_scroller_is_active` — MotionKit does, when it
				// owns the page smoother — would lose the wrapper but still end up
				// with two smoothers fighting over one page.
				'smoothScroller' => (function () {
					if (function_exists('bricksfly_smooth_scroller_is_active') && ! bricksfly_smooth_scroller_is_active()) {
						return null;
					}

					$raw = get_option('bricksfly_smooth_scroller');
					return is_string($raw) ? json_decode($raw) : null;
				})(),
				// All Bricks breakpoints (defaults + custom). Empty array if
				// Bricks isn't active so JS can rely on a consistent shape.
				'breakpoints'    => class_exists('\\Bricks\\Breakpoints')
					? \Bricks\Breakpoints::get_breakpoints()
					: array(),
			)
		);

		wp_localize_script('bricksfly-elements-for-bricks', 'BRICKSFLY_ADDONS_JS', $data);
	}, 20);
}
bricksfly_run();
