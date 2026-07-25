<?php

namespace wealcoder\thebricksfly\Includes\Traits;

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Shared trait for reading extension and widget activation state.
 *
 * Used by the main plugin class (and future pro plugin) to determine
 * which extensions/widgets the user has enabled in the dashboard.
 * Reads from `$GLOBALS['thebrbre_config']` and the saved WP options.
 */
trait Extension_Widgets_Trait
{

	/**
	 * Get active widgets with their full config data.
	 *
	 * Recursively searches `$GLOBALS['thebrbre_config']['widgets']`
	 * and returns only the widgets the user has activated.
	 *
	 * @return array<string, array> Slug-keyed array of active widgets with config data.
	 */
	public static function get_widgets()
	{
		$widgets       = get_option('thebrbre_save_widgets');
		// Option is now a full slug => bool map; filter to active-only before
		// looking up config so disabled widgets don't get registered.
		$saved_widgets = is_array($widgets) ? array_keys(array_filter($widgets)) : [];

		$foundKeys = [];
		$active    = [];
		thebrbre_get_search_active_keys($GLOBALS['thebrbre_config']['widgets'] ?? [], $saved_widgets, $foundKeys, $active);

		return is_array($active) ? $active : [];
	}

	/**
	 * Get active extensions with their full config data.
	 *
	 * Recursively searches `$GLOBALS['thebrbre_config']['extensions']`
	 * and returns only the extensions the user has activated.
	 *
	 * @return array<string, array> Slug-keyed array of active extensions with config data.
	 */
	public static function get_extensions()
	{
		$extensions       = get_option('thebrbre_save_extensions');
		// Option is now a full slug => bool map; filter to active-only before
		// looking up config so disabled extensions don't get loaded.
		$saved_extensions = is_array($extensions) ? array_keys(array_filter($extensions)) : [];

		$foundKeys = [];
		$active    = [];
		thebrbre_get_search_active_keys($GLOBALS['thebrbre_config']['extensions'] ?? [], $saved_extensions, $foundKeys, $active);

		return is_array($active) ? $active : [];
	}

	/**
	 * Check if a specific extension is active.
	 *
	 * @param string $slug Extension slug (e.g. 'animation-effects').
	 * @return bool
	 */
	public static function is_extension_active($slug)
	{
		return (bool) thebrbre_get_settings('thebrbre_save_extensions', $slug);
	}

	/**
	 * Check if a specific widget is active.
	 *
	 * @param string $slug Widget slug.
	 * @return bool
	 */
	public static function is_widget_active($slug)
	{
		return (bool) thebrbre_get_settings('thebrbre_save_widgets', $slug);
	}
}
