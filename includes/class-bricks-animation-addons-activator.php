<?php

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Fired during plugin activation
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Bricks_Animation_Addons
 * @subpackage Bricks_Animation_Addons/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Bricks_Animation_Addons
 * @subpackage Bricks_Animation_Addons/includes
 * @author     Zilani <zilani.wealcoder@gmail.com>
 */
class AABAddons_Activator
{

	/**
	 * Plugin activation handler.
	 *
	 * Schedules a rewrite-rules flush so any CPT/taxonomy registered by the
	 * builder is routed correctly after activation. On multisite, the flag
	 * is set on every subsite when the plugin is network-activated, so each
	 * subsite re-flushes on its next `wp_loaded` (handled by the CPT Builder).
	 *
	 * Also seeds the `aab_save_widgets` AND `aab_save_extensions` options with
	 * every shipped widget/extension set to active — first install only, so
	 * reactivations don't clobber the user's deliberate toggles.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $network_wide True when the plugin was network-activated.
	 */
	public static function activate($network_wide = false)
	{
		if (is_multisite() && $network_wide) {
			$site_ids = get_sites(array('fields' => 'ids', 'number' => 0));
			foreach ($site_ids as $blog_id) {
				switch_to_blog($blog_id);
				update_option('aab_needs_rewrite_flush', 1, false);
				self::maybe_seed_widget_defaults();
				self::maybe_seed_extension_defaults();
				restore_current_blog();
			}
			return;
		}

		update_option('aab_needs_rewrite_flush', 1, false);
		self::maybe_seed_widget_defaults();
		self::maybe_seed_extension_defaults();
	}

	/**
	 * Seed `aab_save_widgets` with every shipped widget enabled.
	 *
	 * Runs only when the option is missing (fresh install or post-uninstall
	 * reinstall). Walks the `widgets` branch of the plugin config and emits
	 * a `slug => true` map for each leaf widget — the same format the
	 * dashboard save handler writes and that readers `array_filter` before
	 * `array_keys` on.
	 *
	 * Pro widgets are included in the seed but are still license-gated at
	 * runtime; flipping them on without a license simply has no effect.
	 */
	private static function maybe_seed_widget_defaults()
	{
		// Sentinel default — distinguishes "no row in wp_options" from "row
		// containing an empty array" so a user who deactivated every widget
		// isn't reseeded back to all-on.
		if (false !== get_option('aab_save_widgets', false)) {
			return;
		}

		if (! isset($GLOBALS['aabaddons_config']) && defined('AAB_ADDONS_PATH')) {
			require_once AAB_ADDONS_PATH . 'config.php';
		}

		$widgets_config = isset($GLOBALS['aabaddons_config']['widgets'])
			? $GLOBALS['aabaddons_config']['widgets']
			: array();

		$map = array();
		self::collect_widget_slugs($widgets_config, $map);

		if (! empty($map)) {
			update_option('aab_save_widgets', $map, false);
		}
	}

	/**
	 * Walk the widgets config tree and collect every leaf widget slug.
	 *
	 * A leaf widget is an array node that declares `is_extension` (group
	 * containers do not) with the value false (true marks an extension, not
	 * a widget). Nodes flagged `is_upcoming` are skipped — they are
	 * placeholders for not-yet-released widgets.
	 *
	 * @param mixed                 $node Config sub-tree to walk.
	 * @param array<string, bool>   $map  Out-param accumulator.
	 */
	private static function collect_widget_slugs($node, &$map)
	{
		if (! is_array($node)) {
			return;
		}
		foreach ($node as $key => $value) {
			if (! is_array($value)) {
				continue;
			}
			if (array_key_exists('is_extension', $value) && false === $value['is_extension']) {
				if (empty($value['is_upcoming'])) {
					$map[$key] = true;
				}
				continue;
			}
			self::collect_widget_slugs($value, $map);
		}
	}



	/**
	 * Seed `aab_save_extensions` with every shipped extension enabled.
	 *
	 * Called on plugin activation (licensing removed — every extension ships
	 * enabled by default). Skips seeding if the option already exists, so a
	 * user who deliberately toggled extensions off isn't reset on reactivation.
	 */
	public static function maybe_seed_extension_defaults()
	{

		if (false !== get_option('aab_save_extensions', false)) {
			return;
		}

		if (! isset($GLOBALS['aabaddons_config']) && defined('AAB_ADDONS_PATH')) {
			require_once AAB_ADDONS_PATH . 'config.php';
		}

		$extensions_config = isset($GLOBALS['aabaddons_config']['extensions'])
			? $GLOBALS['aabaddons_config']['extensions']
			: array();

		$map = array();
		self::collect_extension_slugs($extensions_config, $map);

		if (! empty($map)) {
			update_option('aab_save_extensions', $map, false);
		}
	}

	/**
	 * Walk the extensions config tree and collect every leaf extension slug.
	 *
	 * A leaf extension is identified by having a `location` key — group/subgroup
	 * containers don't have it. Nodes flagged `is_upcoming` are skipped.
	 *
	 * @param mixed                $node Config sub-tree to walk.
	 * @param array<string, bool>  $map  Out-param accumulator.
	 */
	private static function collect_extension_slugs($node, &$map)
	{
		if (! is_array($node)) {
			return;
		}

		foreach ($node as $key => $value) {
			if (! is_array($value)) {
				continue;
			}

			// Leaf node: has a `location` key.
			if (array_key_exists('location', $value)) {
				if (empty($value['is_upcoming'])) {
					$map[$key] = true;
				}
				continue;
			}

			// Container node (group or subgroup): emit its key then recurse.
			$map[$key] = true;
			self::collect_extension_slugs($value, $map);
		}
	}
}
