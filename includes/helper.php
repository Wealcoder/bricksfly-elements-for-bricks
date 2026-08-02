<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'bricksfly_validate_content_json' ) ) {
	function bricksfly_validate_content_json( $input ) {
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


if (! function_exists('bricksfly_get_current_user_roles')) {
  function bricksfly_get_current_user_roles()
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


if (! function_exists('bricksfly_get_total_config_elements_by_key')) {
  function bricksfly_get_total_config_elements_by_key($array, &$foundKeys = 0)
  {
    foreach ($array as $key => $value) {
      // Check if the current key is one we're looking for
      if (isset($value['is_active']) && isset($value['is_extension']) && isset($value['is_pro'])) {
        ++$foundKeys;
      }

      // If value is an array, recurse into it
      if (is_array($value)) {
        bricksfly_get_total_config_elements_by_key($value, $foundKeys);
      }
    }
  }
}


if (! function_exists('bricksfly_get_nested_active_config_keys')) {
  function bricksfly_get_nested_active_config_keys($array, &$foundKeys, &$active)
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
        bricksfly_get_nested_active_config_keys($value, $foundKeys, $active);
      }
    }
  }
}


if (! function_exists('bricksfly_get_nested_config_keys')) {
  // Walks the config tree and emits a flat slug => bool map for every node
  // that exposes an is_active flag (leaves AND group/subgroup containers).
  // Groups need to be included because the React UI surfaces master toggles
  // for them (e.g. extensions sub-groups like `aab-smooth-scroller`, widget
  // group master switches). Skipping them would drop that toggle state.
  function bricksfly_get_nested_config_keys($array, &$foundKeys, &$active)
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
        bricksfly_get_nested_config_keys($value, $foundKeys, $active);
      }
    }
  }
}


if (! function_exists('bricksfly_get_search_active_keys')) {
  function bricksfly_get_search_active_keys($array, $keysToFind, &$foundKeys, &$active)
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
        bricksfly_get_search_active_keys($value, $keysToFind, $foundKeys, $active);
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
if (! function_exists('bricksfly_get_local_plugin_data')) :
  function bricksfly_get_local_plugin_data($basename = '')
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


if (! function_exists('bricksfly_get_db_updated_config')) {

  function bricksfly_get_db_updated_config(array &$configs, array $dbActiveElements)
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
        bricksfly_get_db_updated_config($element, $dbActiveElements);
      }
    }
  }
}




if (! function_exists('bricksfly_get_settings')) {

  /**
   * Return saved settings
   */
  function bricksfly_get_settings($option_name, $element = null)
  {
    $elements = get_option($option_name);
    return (isset($element) ? (isset($elements[$element]) ? $elements[$element] : 0) : array_keys(array_filter($elements)));
  }
}

if (! function_exists('bricksfly_is_extension_active')) {

  /**
   * Check if a specific extension is active.
   *
   * @param string $slug Extension slug (e.g. 'aab-smooth-scroller').
   * @return bool
   */
  function bricksfly_is_extension_active($slug)
  {
    return (bool) bricksfly_get_settings('bricksfly_save_extensions', $slug);
  }
}

if (! function_exists('bricksfly_is_widget_active')) {

  /**
   * Check if a specific widget is active.
   *
   * @param string $slug Widget slug (e.g. 'animated-offcanvas').
   * @return bool
   */
  function bricksfly_is_widget_active($slug)
  {
    return (bool) bricksfly_get_settings('bricksfly_save_widgets', $slug);
  }
}


if (! function_exists('bricksfly_get_config')) {

  /**
   * Return the merged plugin config tree (widgets / extensions / integrations).
   *
   * Built by `config.php` into `$GLOBALS['bricksfly_config']` at load time,
   * then filtered through the dashboard config filter to fold in DB state.
   *
   * @return array
   */
  function bricksfly_get_config()
  {
    $config = isset($GLOBALS['bricksfly_config']) && is_array($GLOBALS['bricksfly_config'])
      ? $GLOBALS['bricksfly_config']
      : array();

    return apply_filters('bricksfly_dashboard_config', $config);
  }
}

if (! function_exists('bricksfly_translate_config_tree')) {

  /**
   * Recursively translate the user-facing strings in the config tree.
   *
   * config.php loads at plugin bootstrap (before WP's `init` hook), so it
   * cannot call `__()` directly without triggering WP 6.7+ "translation
   * loaded too early" notices. Instead, the labels stay raw English in the
   * static array and are translated on demand here — wired into the
   * `bricksfly_addons_dashboard_config` filter so the React dashboard receives
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
  function bricksfly_translate_config_tree($node)
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
      $translations = function_exists('bricksfly_get_config_translations')
        ? bricksfly_get_config_translations()
        : array();
    }

    foreach ($node as $key => $value) {
      if (is_array($value)) {
        $node[$key] = bricksfly_translate_config_tree($value);
      } elseif (is_string($value) && in_array($key, $translatable_keys, true) && $value !== '') {
        $node[$key] = isset($translations[$value]) ? $translations[$value] : $value;
      }
    }

    return $node;
  }
}

add_filter('bricksfly_dashboard_config', 'bricksfly_translate_config_tree', 5);

if (! function_exists('bricksfly_is_pro_active')) {

  /**
   * Whether the Bricksfly Pro plugin is installed and active.
   *
   * @return bool
   */
  function bricksfly_is_pro_active()
  {
    if (defined('BRICKSFLY_PRO_VERSION')) {
      return true;
    }

    if (! function_exists('is_plugin_active')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    return is_plugin_active('the-bricksfly-pro/the-bricksfly-pro.php');
  }
}

if (! function_exists('bricksfly_is_pro_installed')) {

  /**
   * Whether the Bricksfly Pro plugin folder + main file exist on
   * disk. Independent of whether the plugin is currently activated — used to
   * gate features that must not run at all when Pro isn't available, such as
   * the site-settings extensions and pro extension toggles.
   *
   * @return bool
   */
  function bricksfly_is_pro_installed()
  {
    return file_exists(WP_PLUGIN_DIR . '/the-bricksfly-pro/the-bricksfly-pro.php');
  }
}


if (! function_exists('bricksfly_is_license_valid')) {

  /**
   * Whether the license is active AND the Pro plugin folder exists on disk.
   *
   * The stored license option is unreliable on its own: if the user removes
   * the Pro plugin folder manually, `bricksfly_license_status` stays
   * 'valid' until the next remote check. We require both to be true before
   * unlocking Pro-gated UI / features.
   *
   * @return bool
   */
  function bricksfly_is_license_valid()
  {
    return bricksfly_is_pro_installed()
      && ('valid' === get_option('bricksfly_license_status'));
  }
}

if (! function_exists('bricksfly_get_license_limitations')) {

  /**
   * Return the per-feature limitation flags for the active license.
   *
   * The flags are written by the Pro plugin's license activate/check flow
   * (see the-bricksfly-pro/includes/license/update.php) into the
   * `bricksfly_license_limitations` option, as a map of feature => bool.
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
   * resolves to "not allowed" via bricksfly_is_feature_allowed().
   *
   * @return array<string,bool>
   */
  function bricksfly_get_license_limitations()
  {
    if (! bricksfly_is_license_valid()) {
      return array();
    }

    $limitations = get_option('bricksfly_license_limitations', array());

    return is_array($limitations) ? $limitations : array();
  }
}

if (! function_exists('bricksfly_is_feature_allowed')) {

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
  function bricksfly_is_feature_allowed($feature)
  {
    $limitations = bricksfly_get_license_limitations();

    return ! empty($limitations[$feature]);
  }
}

if (! function_exists('bricksfly_get_element_bricks_name')) {

  /**
   * Resolve a widget's real Bricks element `$name` from its element file's
   * declared property, without ever `require`ing the file — so an element
   * that's toggled off, or Pro-only without a valid license, never has its
   * real code loaded just to resolve its name for a placeholder.
   *
   * The config slug (e.g. `button-pro`, same as the element's filename in
   * `includes/elements/`) is not always the same as the Bricks element
   * `$name` it registers under (e.g. `aab-button-pro`), so this reads the
   * declared value directly out of the file's source.
   *
   * @param string $slug Config slug.
   * @return string Bricks element name, or '' if it can't be resolved.
   */
  function bricksfly_get_element_bricks_name($slug)
  {
    static $cache = array();

    if (isset($cache[$slug])) {
      return $cache[$slug];
    }

    $path = BRICKSFLY_PATH . 'includes/elements/' . $slug . '.php';

    if (file_exists($path)) {
      $contents = file_get_contents($path);

      if ($contents && preg_match('/public\s+\$name\s*=\s*[\'"]([a-z0-9\-_]+)[\'"]/i', $contents, $matches)) {
        return $cache[$slug] = $matches[1];
      }
    }

    return $cache[$slug] = '';
  }
}

if (! function_exists('bricksfly_register_widget_placeholder')) {

  /**
   * Register a branded placeholder Bricks element under a widget's real
   * Bricks `$name`, so `class_exists()` succeeds in Bricks core's
   * `Frontend::render_element()` and its raw "PHP class does not exist"
   * fallback never fires. Used for widgets that are configured but not
   * currently loaded (toggled off, or Pro/license unavailable) — the
   * placeholder carries no real widget behavior, only a short admin-facing
   * notice shown inside the builder (see BRICKSFLY_Placeholder_Element).
   *
   * Shared between the free and Pro plugins so both loading paths produce
   * the same placeholder instead of duplicating the class-generation logic.
   *
   * Safe to call more than once for the same $bricks_name, and never
   * overwrites an already-registered (real or placeholder) class.
   *
   * @param string $bricks_name Real Bricks element name (e.g. 'aab-button-pro').
   * @param string $label       Human-readable widget label for the message.
   */
  function bricksfly_register_widget_placeholder($bricks_name, $label)
  {
    if (empty($bricks_name) || ! class_exists('\Bricks\Elements')) {
      return;
    }

    // Something already registered a real (or placeholder) class under
    // this name — never clobber it.
    if (isset(\Bricks\Elements::$elements[$bricks_name])) {
      return;
    }

    if (! class_exists('BRICKSFLY_Placeholder_Element')) {
      $base = BRICKSFLY_PATH . 'includes/elements/class-bricksfly-placeholder-element.php';

      if (! file_exists($base)) {
        return;
      }

      require_once $base;
    }

    // Bricks keys its own registry off a class NAME string and always
    // instantiates via `new $element_class_name()` with no constructor args
    // it lets us supply, so each widget still needs its own distinct class
    // name. class_alias() gives every widget a unique, independently
    // `new`-able name for the same shared class body — no eval() needed,
    // since per-widget identity (real name + label) now lives in
    // BRICKSFLY_Placeholder_Element's static registry, keyed by that class
    // name, rather than in hardcoded subclass property defaults.
    // md5() keeps the generated identifier valid regardless of characters
    // in $bricks_name.
    $class_name = 'BRICKSFLY_Placeholder_' . md5($bricks_name);

    if (! class_exists($class_name)) {
      class_alias('BRICKSFLY_Placeholder_Element', $class_name);
    }

    BRICKSFLY_Placeholder_Element::register($class_name, $bricks_name, $label);

    \Bricks\Elements::register_element(
      BRICKSFLY_PATH . 'includes/elements/class-bricksfly-placeholder-element.php',
      '',
      $class_name
    );
  }
}

if (! function_exists('bricksfly_feature_denied_message')) {

  /**
   * Human-readable message shown when a license-gated feature is blocked.
   * Centralized so the server AJAX handlers and the client popups agree on
   * the wording per feature.
   *
   * @param string $feature Feature key.
   * @return string
   */
  function bricksfly_feature_denied_message($feature)
  {
    switch ($feature) {
      case 'starter_tpl_import':
        return __('Starter template import is not included in your current license plan. Please upgrade your plan to import starter templates.', 'bricksfly-elements-for-bricks');
      case 'section_import':
        return __('Section import is not included in your current license plan. Please upgrade your plan to import sections.', 'bricksfly-elements-for-bricks');
      case 'starter_page_import':
        return __('Page import is not included in your current license plan. Please upgrade your plan to import pages.', 'bricksfly-elements-for-bricks');
      case 'live_copy':
        return __('Live Copy is not included in your current license plan. Please upgrade your plan to use it.', 'bricksfly-elements-for-bricks');
      default:
        return __('This feature is not included in your current license plan. Please upgrade your plan to use it.', 'bricksfly-elements-for-bricks');
    }
  }
}

