<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'aabaddons_validate_content_json' ) ) {
	function aabaddons_validate_content_json( $input ) {
		if ( ! is_string( $input ) || empty( $input ) ) {
			return false;
		}
		$decoded = json_decode( $input, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return false;
		}
		return $decoded;
	}
}


if (! function_exists('aabaddons_get_current_user_roles')) {
  function aabaddons_get_current_user_roles()
  {

    if (is_user_logged_in()) {

      $user = wp_get_current_user();

      $roles = (array) $user->roles;

      if (is_super_admin()) {
        $roles[] = 'administrator'; // Add administrator role for super admins
      }

      return $roles; // This will returns an array

    } else {

      return array();
    }
  }
}


if (! function_exists('aabaddons_get_total_config_elements_by_key')) {
  function aabaddons_get_total_config_elements_by_key($array, &$foundKeys = 0)
  {
    foreach ($array as $key => $value) {
      // Check if the current key is one we're looking for
      if (isset($value['is_active']) && isset($value['is_extension']) && isset($value['is_pro'])) {
        ++$foundKeys;
      }

      // If value is an array, recurse into it
      if (is_array($value)) {
        aabaddons_get_total_config_elements_by_key($value, $foundKeys);
      }
    }
  }
}


if (! function_exists('aabaddons_get_nested_active_config_keys')) {
  function aabaddons_get_nested_active_config_keys($array, &$foundKeys, &$active)
  {
    foreach ($array as $key => $value) {
      // Check if the current key is one we're looking for
      if (isset($value['is_upcoming']) && isset($value['is_pro']) && isset($value['is_active']) && $value['is_active'] == true) {
        // Add to found keys list
        if (isset($value['is_upcoming']) && $value['is_upcoming'] !== true) {
          $foundKeys[] = $key;
          // Store the entire element in $active
          $active[$key] = true;
        }
      }

      // If value is an array, recurse into it
      if (is_array($value)) {
        aabaddons_get_nested_active_config_keys($value, $foundKeys, $active);
      }
    }
  }
}


if (! function_exists('aabaddons_get_nested_config_keys')) {
  // Walks the config tree and emits a flat slug => bool map for every node
  // that exposes an is_active flag (leaves AND group/subgroup containers).
  // Groups need to be included because the React UI surfaces master toggles
  // for them (e.g. extensions sub-groups like `aab-smooth-scroller`, widget
  // group master switches). Skipping them would drop that toggle state.
  function aabaddons_get_nested_config_keys($array, &$foundKeys, &$active)
  {
    if (! is_array($array)) {
      return;
    }
    foreach ($array as $key => $value) {
      if (is_array($value) && array_key_exists('is_active', $value)) {
        $foundKeys[]  = $key;
        $active[$key] = ! empty($value['is_active']);
      }
      if (is_array($value)) {
        aabaddons_get_nested_config_keys($value, $foundKeys, $active);
      }
    }
  }
}


if (! function_exists('aabaddons_get_search_active_keys')) {
  function aabaddons_get_search_active_keys($array, $keysToFind, &$foundKeys, &$active)
  {

    foreach ($array as $key => $value) {
      // Check if the current key is one we're looking for
      if (in_array($key, $keysToFind) && is_array($value) && array_key_exists('is_extension', $value)) {
        // Add to found keys list
        $foundKeys[] = sanitize_text_field($key);
        // Store the entire element in $active
        $value['is_active'] = 1;
        $active[$key]     = $value;
      }
      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
      if (is_array($value)) {
        aabaddons_get_search_active_keys($value, $keysToFind, $foundKeys, $active);
      }
    }
  }
}



/**
 * Get local plugin data
 *
 * @param string $basename
 *
 * @return false|mixed|string
 */
if (! function_exists('aabaddons_get_local_plugin_data')) :
  function aabaddons_get_local_plugin_data($basename = '')
  {
    if (empty($basename)) {
      return false;
    }

    if (! function_exists('get_plugins')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $plugins = get_plugins();

    if (! isset($plugins[$basename])) {
      return false;
    }

    return $plugins[$basename];
  }
endif;


if (! function_exists('aabaddons_get_db_updated_config')) {

  function aabaddons_get_db_updated_config(array &$configs, array $dbActiveElements)
  {
    // Loop through each item in the configs array
    foreach ($configs as $key => &$element) {

      // Check if the current element is an array and has an 'is_active' field
      if (is_array($element) && isset($element['is_active'])) {
        // If the current key is in the dbActiveElements array, update is_active to true
        if (in_array($key, $dbActiveElements)) {
          $element['is_active'] = true;
        }
      }

      // Recursively call the function for any nested elements
      if (is_array($element)) {
        aabaddons_get_db_updated_config($element, $dbActiveElements);
      }
    }
  }
}




if (! function_exists('aabaddons_get_settings')) {

  /**
   * Return saved settings
   */
  function aabaddons_get_settings($option_name, $element = null)
  {
    $elements = get_option($option_name);
    return (isset($element) ? (isset($elements[$element]) ? $elements[$element] : 0) : array_keys(array_filter($elements)));
  }
}

if (! function_exists('aabaddons_is_extension_active')) {

  /**
   * Check if a specific extension is active.
   *
   * @param string $slug Extension slug (e.g. 'aab-smooth-scroller').
   * @return bool
   */
  function aabaddons_is_extension_active($slug)
  {
    return (bool) aabaddons_get_settings('aab_save_extensions', $slug);
  }
}

if (! function_exists('aabaddons_is_widget_active')) {

  /**
   * Check if a specific widget is active.
   *
   * @param string $slug Widget slug (e.g. 'animated-offcanvas').
   * @return bool
   */
  function aabaddons_is_widget_active($slug)
  {
    return (bool) aabaddons_get_settings('aab_save_widgets', $slug);
  }
}


if (! function_exists('aabaddons_get_config')) {

  /**
   * Return the merged plugin config tree (widgets / extensions / integrations).
   *
   * Built by `config.php` into `$GLOBALS['aabaddons_config']` at load time,
   * then filtered through the dashboard config filter to fold in DB state.
   *
   * @return array
   */
  function aabaddons_get_config()
  {
    $config = isset($GLOBALS['aabaddons_config']) && is_array($GLOBALS['aabaddons_config'])
      ? $GLOBALS['aabaddons_config']
      : array();

    return apply_filters('aabaddons_dashboard_config', $config);
  }
}

if (! function_exists('aabaddons_translate_config_tree')) {

  /**
   * Recursively translate the user-facing strings in the config tree.
   *
   * config.php loads at plugin bootstrap (before WP's `init` hook), so it
   * cannot call `__()` directly without triggering WP 6.7+ "translation
   * loaded too early" notices. Instead, the labels stay raw English in the
   * static array and are translated on demand here — wired into the
   * `aab_addons_dashboard_config` filter so the React dashboard receives
   * translated strings.
   *
   * Translatable keys: `label`, `title`, `description`. Slugs, icons,
   * URLs, booleans and other non-display values are left untouched.
   *
    * Literal source and translated values are mapped in
    * `includes/config-i18n.php`.
   *
   * @param array $node
   * @return array
   */
  function aabaddons_translate_config_tree($node)
  {
    // Skip translation before `init` fires — Pro's bootstrap reads the
    // config on plugins_loaded for widget file-loading (slugs only, no
    // labels), and calling __() that early triggers WP 6.7+ "translation
    // loaded too early" notices.
    if (! did_action('init')) {
      return $node;
    }

    if (! is_array($node)) {
      return $node;
    }

    static $translatable_keys = array('label', 'title', 'description');

    static $translations = null;
    if (null === $translations) {
      $translations = function_exists('aabaddons_get_config_translations')
        ? aabaddons_get_config_translations()
        : array();
    }

    foreach ($node as $key => $value) {
      if (is_array($value)) {
        $node[$key] = aabaddons_translate_config_tree($value);
      } elseif (is_string($value) && in_array($key, $translatable_keys, true) && $value !== '') {
        $node[$key] = isset($translations[$value]) ? $translations[$value] : $value;
      }
    }

    return $node;
  }
}

add_filter('aabaddons_dashboard_config', 'aabaddons_translate_config_tree', 5);

if (! function_exists('aabaddons_is_pro_active')) {

  /**
   * Whether the Bricksfly Pro plugin is installed and active.
   *
   * @return bool
   */
  function aabaddons_is_pro_active()
  {
    if (defined('AAB_PRO_ADDONS_VERSION')) {
      return true;
    }

    if (! function_exists('is_plugin_active')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    return is_plugin_active('the-bricksfly-pro/the-bricksfly-pro.php');
  }
}

if (! function_exists('aabaddons_is_pro_installed')) {

  /**
   * Whether the Bricksfly Pro plugin folder + main file exist on
   * disk. Independent of whether the plugin is currently activated — used to
   * gate features that must not run at all when Pro isn't available, such as
   * the site-settings extensions and pro extension toggles.
   *
   * @return bool
   */
  function aabaddons_is_pro_installed()
  {
    return file_exists(WP_PLUGIN_DIR . '/the-bricksfly-pro/the-bricksfly-pro.php');
  }
}


if (! function_exists('aab_is_license_valid')) {

  /**
   * Whether the license is active AND the Pro plugin folder exists on disk.
   *
   * The stored license option is unreliable on its own: if the user removes
   * the Pro plugin folder manually, `wcf_addon_sl_license_status` stays
   * 'valid' until the next remote check. We require both to be true before
   * unlocking Pro-gated UI / features.
   *
   * @return bool
   */
  function aab_is_license_valid()
  {
    // License removed: always report a valid license so every Pro feature works.
    return true;
  }
}

if (! function_exists('aab_is_pro_installed')) {

  /**
   * Whether the Bricksfly Pro plugin folder + main file exist on
   * disk. Independent of whether the plugin is currently activated — used to
   * gate features that must not run at all when Pro isn't available, such as
   * the site-settings extensions and pro extension toggles.
   *
   * @return bool
   */
  function aab_is_pro_installed()
  {
    return file_exists(WP_PLUGIN_DIR . '/the-bricksfly-pro/the-bricksfly-pro.php');
  }
}

if (! function_exists('aabaddons_is_license_valid')) {

  /**
   * Whether the license is active AND the Pro plugin folder exists on disk.
   *
   * The stored license option is unreliable on its own: if the user removes
   * the Pro plugin folder manually, `wcf_addon_sl_license_status` stays
   * 'valid' until the next remote check. We require both to be true before
   * unlocking Pro-gated UI / features.
   *
   * @return bool
   */
  function aabaddons_is_license_valid()
  {
    // License removed: always report a valid license so every Pro feature works.
    return true;
  }
}

if (! function_exists('aab_get_license_limitations')) {

  /**
   * Return the per-feature limitation flags for the active license.
   *
   * The flags are written by the Pro plugin's license activate/check flow
   * (see the-bricksfly-pro/includes/license/update.php) into the
   * `wcf_addon_sl_license_limitations` option, as a map of feature => bool.
   *
   * Known feature keys (tier-dependent — any may be absent):
   *   - starter_tpl_import  Template (starter / full-demo) import
   *   - section_import      Section import in the Bricks builder
   *   - starter_page_import Page import (Page Importer)
   *   - live_copy           Live copy
   *   - widget              Widgets
   *   - animation           Animations
   *
   * Returns an empty array when there is no valid license, so every feature
   * resolves to "not allowed" via aab_is_feature_allowed().
   *
   * @return array<string,bool>
   */
  function aab_get_license_limitations()
  {
    // License removed: grant every per-feature flag so all Pro features (and
    // the React import/feature gates that read these) unlock unconditionally.
    return array(
      'starter_tpl_import'  => true,
      'section_import'      => true,
      'starter_page_import' => true,
      'live_copy'           => true,
      'widget'              => true,
      'animation'           => true,
      'dynamic_data'        => true,
      'ai'                  => true,
    );
  }
}

if (! function_exists('aab_is_feature_allowed')) {

  /**
   * Whether a license-gated feature is available on this site.
   *
   * A feature is allowed only when BOTH:
   *   1. the license is valid (Pro installed + status "valid"), AND
   *   2. the license's limitations map flags the feature as `true`.
   *
   * A missing flag counts as not allowed (fail-closed), so a license tier
   * that doesn't include a feature — or a forged request that never went
   * through activation — cannot unlock it.
   *
   * @param string $feature Feature key, e.g. 'starter_tpl_import'.
   * @return bool
   */
  function aab_is_feature_allowed($feature)
  {
    // License removed: every license-gated feature is allowed.
    return true;
  }
}

if (! function_exists('aab_feature_denied_message')) {

  /**
   * Human-readable message shown when a license-gated feature is blocked.
   * Centralized so the server AJAX handlers and the client popups agree on
   * the wording per feature.
   *
   * @param string $feature Feature key.
   * @return string
   */
  function aab_feature_denied_message($feature)
  {
    switch ($feature) {
      case 'starter_tpl_import':
        return __('Starter template import is not included in your current license plan. Please upgrade your plan to import starter templates.', 'the-bricksfly');
      case 'section_import':
        return __('Section import is not included in your current license plan. Please upgrade your plan to import sections.', 'the-bricksfly');
      case 'starter_page_import':
        return __('Page import is not included in your current license plan. Please upgrade your plan to import pages.', 'the-bricksfly');
      case 'live_copy':
        return __('Live Copy is not included in your current license plan. Please upgrade your plan to use it.', 'the-bricksfly');
      default:
        return __('This feature is not included in your current license plan. Please upgrade your plan to use it.', 'the-bricksfly');
    }
  }
}

