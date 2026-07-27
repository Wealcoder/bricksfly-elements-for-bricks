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
class THEBRBRE_Activator
{

	/**
	 * Plugin activation handler.
	 *
	 * Schedules a rewrite-rules flush so any CPT/taxonomy registered by the
	 * builder is routed correctly after activation. On multisite, the flag
	 * is set on every subsite when the plugin is network-activated, so each
	 * subsite re-flushes on its next `wp_loaded` (handled by CPT Builder, a
	 * Pro-only feature — see the-bricksfly-pro/admin/pages/cpt-builder.php).
	 *
	 * Also seeds the `thebrbre_save_widgets` option with every shipped widget set
	 * to active — first install only, so reactivations don't clobber the
	 * user's deliberate toggles.
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
				update_option('thebrbre_needs_rewrite_flush', 1, false);
				self::maybe_seed_widget_defaults();
				restore_current_blog();
			}
			return;
		}

		update_option('thebrbre_needs_rewrite_flush', 1, false);
		self::maybe_seed_widget_defaults();
	}

	/**
	 * Seed `thebrbre_save_widgets` with every shipped widget enabled.
	 *
	 * Runs only when the option is missing (fresh install or post-uninstall
	 * reinstall). Walks the `widgets` branch of the plugin config and emits
	 * a `slug => true` map for each leaf widget — the same format the
	 * dashboard save handler writes and that readers `array_filter` before
	 * `array_keys` on.
	 *
	 * Pro widgets are included in the seed but only take effect once the
	 * Pro plugin is installed and active.
	 */
	private static function maybe_seed_widget_defaults()
	{
		// Sentinel default — distinguishes "no row in wp_options" from "row
		// containing an empty array" so a user who deactivated every widget
		// isn't reseeded back to all-on.
		if (false !== get_option('thebrbre_save_widgets', false)) {
			return;
		}

		if (! isset($GLOBALS['thebrbre_config']) && defined('THEBRBRE_PATH')) {
			require_once THEBRBRE_PATH . 'config.php';
		}

		$widgets_config = isset($GLOBALS['thebrbre_config']['widgets'])
			? $GLOBALS['thebrbre_config']['widgets']
			: array();

		$map = array();
		self::collect_widget_slugs($widgets_config, $map);

		if (! empty($map)) {
			update_option('thebrbre_save_widgets', $map, false);
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



}
