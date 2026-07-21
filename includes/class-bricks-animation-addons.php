<?php

if (! defined('ABSPATH')) {
	exit;
}

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Bricks_Animation_Addons
 * @subpackage Bricks_Animation_Addons/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Bricks_Animation_Addons
 * @subpackage Bricks_Animation_Addons/includes
 * @author     Zilani <zilani.wealcoder@gmail.com>
 */
class AABAddons_Plugin
{
	use \AABAddons\Includes\Traits\Extension_Widgets_Trait;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      AABAddons_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('AAB_ADDONS_VERSION')) {
			$this->version = AAB_ADDONS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'bricksfly';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		// Licensing removed: ship with every extension AND widget enabled by
		// default. The activator seeds these on a fresh install, but an install
		// that predates this change (or where seeding never ran because it used
		// to wait for a license) can still have them all off. Ensure the
		// defaults exist on load — only when the option is entirely absent, so
		// a user's deliberate on/off toggles are never overwritten.
		add_action('admin_init', array($this, 'aab_ensure_default_enabled'), 1);
	}

	/**
	 * Seed `aab_save_extensions` / `aab_save_widgets` with every shipped item
	 * enabled when either option is missing. Idempotent and non-destructive:
	 * once the option row exists (even as an empty array) it is left untouched.
	 *
	 * @return void
	 */
	public function aab_ensure_default_enabled()
	{
		if (! isset($GLOBALS['aabaddons_config']) && defined('AAB_ADDONS_PATH')) {
			require_once AAB_ADDONS_PATH . 'config.php';
		}
		$config = isset($GLOBALS['aabaddons_config']) && is_array($GLOBALS['aabaddons_config'])
			? $GLOBALS['aabaddons_config']
			: array();

		// Extensions — enable every leaf extension (has a `location`) plus its
		// group/subgroup containers; skip `is_upcoming` placeholders.
		if (false === get_option('aab_save_extensions', false)) {
			$ext_map = array();
			$this->aab_collect_default_slugs(
				isset($config['extensions']) ? $config['extensions'] : array(),
				$ext_map,
				'location'
			);
			if (! empty($ext_map)) {
				update_option('aab_save_extensions', $ext_map, false);
			}
		}

		// Widgets — enable every leaf widget (is_extension === false); skip
		// `is_upcoming` placeholders.
		if (false === get_option('aab_save_widgets', false)) {
			$wgt_map = array();
			$this->aab_collect_default_slugs(
				isset($config['widgets']) ? $config['widgets'] : array(),
				$wgt_map,
				'widget'
			);
			if (! empty($wgt_map)) {
				update_option('aab_save_widgets', $wgt_map, false);
			}
		}
	}

	/**
	 * Walk a config sub-tree and collect slugs to enable by default.
	 *
	 * @param mixed               $node Config sub-tree.
	 * @param array<string,bool>  $map  Out-param accumulator (slug => true).
	 * @param string              $mode 'location' for extensions, 'widget' for widgets.
	 * @return void
	 */
	private function aab_collect_default_slugs($node, &$map, $mode)
	{
		if (! is_array($node)) {
			return;
		}
		foreach ($node as $key => $value) {
			if (! is_array($value)) {
				continue;
			}

			if ('location' === $mode) {
				// Leaf extension: has a `location` key.
				if (array_key_exists('location', $value)) {
					if (empty($value['is_upcoming'])) {
						$map[$key] = true;
					}
					continue;
				}
				// Container (group/subgroup): enable it, then recurse.
				$map[$key] = true;
				$this->aab_collect_default_slugs($value, $map, $mode);
			} else {
				// Leaf widget: declares is_extension === false.
				if (array_key_exists('is_extension', $value) && false === $value['is_extension']) {
					if (empty($value['is_upcoming'])) {
						$map[$key] = true;
					}
					continue;
				}
				$this->aab_collect_default_slugs($value, $map, $mode);
			}
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - AABAddons_Loader. Orchestrates the hooks of the plugin.
	 * - AABAddons_Admin. Defines all hooks for the admin area.
	 * - AABAddons_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		// Core classes.
		require_once AAB_ADDONS_PATH . 'includes/class-bricks-animation-addons-loader.php';
		require_once AAB_ADDONS_PATH . 'admin/class-bricks-animation-addons-admin.php';
		require_once AAB_ADDONS_PATH . 'public/class-bricks-animation-addons-public.php';

		// Extension helpers.
		require_once AAB_ADDONS_PATH . 'includes/extensions/helpers/ResponsiveHelper.php';
		require_once AAB_ADDONS_PATH . 'includes/extensions/helpers/BRICKS_ELEMENTS.php';
		require_once AAB_ADDONS_PATH . 'includes/extensions/helpers/BricksElementsHelper.php';

		// License AJAX endpoints + admin status notice live in the Pro plugin
		// (includes/license/update.php) — Pro must be active to activate or
		// deactivate a license. The free plugin only reads the resulting
		// option value via aabaddons_is_license_valid() / aabaddons_is_pro_active().

		// CPT Builder must load on every request (admin + frontend) so the
		// init hooks that register user-defined CPTs/taxonomies fire on the
		// frontend too. Without this, single-post URLs for builder CPTs
		// don't match any rewrite rule and WordPress redirects them to home.
		require_once AAB_ADDONS_PATH . 'admin/cpt-builder.php';

		// Admin pages. The "Site Settings" page is a Pro-only feature; the
		// real UI lives in the Pro plugin and only registers when a valid
		// license is active. The free plugin provides a placeholder that
		// registers the menu + shows an upsell notice when Pro is missing
		// or unlicensed, so the feature doesn't silently disappear.
		if (is_admin()) {
			require_once AAB_ADDONS_PATH . 'admin/pages/dashboard.php';
			require_once AAB_ADDONS_PATH . 'admin/pages/template-importer.php';
			require_once AAB_ADDONS_PATH . 'admin/pages/page-import.php';
			// require_once AAB_ADDONS_PATH . 'admin/pages/settings-placeholder.php';
		}

		// Builder Template Library — adds the "Import Section" button to
		// the Bricks Builder toolbar + the AJAX endpoints behind it. Must
		// load on every request (not just is_admin()) because the Bricks
		// builder runs on the frontend with `wp_enqueue_scripts`, and the
		// admin-ajax endpoints need to be hooked before the AJAX call hits.
		require_once AAB_ADDONS_PATH . 'admin/pages/builder-template-library.php';

		// Extensions.
		$this->register_elements();
		$this->register_extensions();

		// Dispatch the Pro plugin bootstrap action once every plugin file has
		// been parsed (so the pro plugin has had a chance to register its
		// add_action('aabaddons/pro/register', …) handler).
		add_action('plugins_loaded', function () {
			if (function_exists('aabaddons_is_pro_active') && aabaddons_is_pro_active()) {
				do_action('aabaddons/pro/register');
			}
		}, 20);

		$this->loader = new AABAddons_Loader();
	}

	/**
	 * Register extensions from config.php.
	 *
	 * Reads the extensions → gsap-extensions groups and loads each
	 * child extension whose slug is active in `aab_save_extensions`.
	 * Skips extensions marked as upcoming.
	 * In Bricks builder, all extensions are loaded for live preview.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function register_extensions()
	{
		$extention_list = self::get_extensions();
		$ext_dir        = AAB_ADDONS_PATH . 'includes/extensions/';

		foreach ($extention_list as $slug => $data) {
			// Skip upcoming extensions.
			if (! empty($data['is_upcoming'])) {
				continue;
			}

			// `is_pro=true` slugs (including all Site Settings tabs) are owned
			// exclusively by the Pro plugin. The free plugin must NEVER load
			// them so Pro features stay disabled without the Pro plugin
			// folder installed. Pro, when active, loads its own copies via
			// `aab_pro_register()`.
			if (! empty($data['is_pro'])) {
				continue;
			}

			// Extensions.
			$path = $ext_dir . $slug . '.php';
			if (file_exists($path)) {
				require_once $path;
			}
		}

		// Smooth scroller is Pro-owned; the free plugin never loads it.
		// Pro, when active, loads it via its own bootstrap.
	}

	/**
	 * Register elements from config.php.
	 *
	 * Reads the widgets groups and registers each child element
	 * whose slug is active in `aab_save_widgets`.
	 * Skips elements marked as upcoming.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function register_elements()
	{

		add_action('init', function () {


			if (! class_exists('\Bricks\Elements')) {
				return;
			}

			$widget_list  = self::get_widgets();

			$elements_dir = AAB_ADDONS_PATH . 'includes/elements/';

			foreach ($widget_list as $slug => $data) {

				// Skip upcoming widgets.
				if (! empty($data['is_upcoming'])) {
					continue;
				}

				// `is_pro=true` widgets are owned exclusively by the Pro plugin
				// (animated offcanvas, video popup, youtube videos, …). Free
				// must never load them — Pro registers its own elements when a
				// valid license is active.
				if (! empty($data['is_pro'])) {
					continue;
				}
				$path = $elements_dir . $slug . '.php';

				if (file_exists($path)) {
					require_once $path;
					\Bricks\Elements::register_element($path);
				}
			}
		}, 11);
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new AABAddons_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new AABAddons_Public($this->get_plugin_name(), $this->get_version());

		add_action('bricks/frontend/enqueue_scripts', $plugin_public, 'enqueue_scripts');


		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_editor_panel', 100);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    AABAddons_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
