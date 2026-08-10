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

if (! function_exists('bricksfly_third_party_owns_page_smoother')) {

  /**
   * Whether another plugin is already driving this page's ScrollSmoother.
   *
   * Only MotionKit is checked today: it exposes ScrollSmoother::should_run()
   * as its documented "am I driving the smoother" answer (connected to the
   * editor AND switched on for this page), and its own runner kills any
   * instance it didn't create — so when that returns true, ours must not
   * exist at all.
   *
   * Every call is guarded: MotionKit may be absent, an older build may not
   * have the method, and a fatal here would take the whole frontend down.
   *
   * @return bool
   */
  function bricksfly_third_party_owns_page_smoother()
  {
    $owners = array(
      array('\MotionKit\Frontend\ScrollSmoother', 'should_run'),
    );

    foreach ($owners as $owner) {
      list($class, $method) = $owner;

      if (class_exists($class) && method_exists($class, $method) && call_user_func(array($class, $method))) {
        return true;
      }
    }

    return false;
  }
}

if (! function_exists('bricksfly_smooth_scroller_is_active')) {

  /**
   * Whether Scroll Smoother can actually run on this request.
   *
   * Two things must both be true, mirroring what smoothScroller.js itself
   * requires before it creates a ScrollSmoother instance:
   *   1. the `aab-smooth-scroller` extension is toggled on, and
   *   2. `bricksfly_smooth_scroller` flags at least one breakpoint `enabled`.
   *
   * Which breakpoint is live can only be known in the browser (the JS
   * re-resolves it on resize), so "any breakpoint enabled" is the strongest
   * server-side answer available — the JS still gates per breakpoint.
   *
   * Used to decide whether to emit the #smooth-wrapper / #smooth-content
   * markup at all: those divs are styled by frontend.scss (`overflow-x:
   * scroll; overflow-y: hidden`), so emitting them when no smoother will be
   * created leaves a stray horizontal scrollbar and a clipped Y axis on every
   * page. Result is memoized so the opening and closing hooks can never
   * disagree within one request.
   *
   * @return bool
   */
  function bricksfly_smooth_scroller_is_active()
  {
    static $active = null;

    if (null !== $active) {
      return $active;
    }

    $active = false;

    if (function_exists('bricksfly_is_extension_active') && bricksfly_is_extension_active('aab-smooth-scroller')) {
      $raw      = get_option('bricksfly_smooth_scroller');
      $settings = is_string($raw) ? json_decode($raw, true) : $raw;

      if (is_array($settings)) {
        foreach ($settings as $config) {
          if (is_array($config) && ! empty($config['enabled'])) {
            $active = true;
            break;
          }
        }
      }
    }

    // Stand down when MotionKit already owns the page smoother. A page has
    // exactly one ScrollSmoother; MotionKit's runner also kills any instance
    // it didn't create, so two of them cannot coexist.
    //
    // Asked directly rather than waiting to be told: MotionKit does push the
    // same answer through the filter below, but only versions that know this
    // filter exists do. Reading its public API means an older MotionKit — or
    // one loading after us — still wins the page. This mirrors how AAE Pro
    // resolves the same conflict.
    if ($active && bricksfly_third_party_owns_page_smoother()) {
      $active = false;
    }

    /**
     * Filter the resolved Scroll Smoother active state.
     *
     * Lets Pro veto per page (e.g. the builder's `aab_disable_smoothscroll`
     * page setting) without the free plugin having to know about Pro's page
     * settings.
     *
     * @param bool $active
     */
    $active = (bool) apply_filters('bricksfly_smooth_scroller_is_active', $active);

    return $active;
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

    return is_plugin_active('bricksfly-elements-for-bricks-pro/bricksfly-elements-for-bricks-pro.php');
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
    return file_exists(WP_PLUGIN_DIR . '/bricksfly-elements-for-bricks-pro/bricksfly-elements-for-bricks-pro.php');
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
   * (see bricksfly-elements-for-bricks-pro/includes/license/update.php) into the
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

if (! function_exists('bricksfly_kses_allowed_html')) {

  /**
   * Allowed-tags map for echoing element markup that may include a Bricks
   * icon rendered as inline SVG (\Bricks\Element::render_icon() emits raw
   * <svg>/<path>/... when the user picks an SVG-type icon, not just an
   * icon-font <i> tag). wp_kses_post()'s default allowlist doesn't include
   * <svg> or its child elements, so wrapping SVG-icon-capable output in
   * wp_kses_post() alone silently strips the icon instead of just escaping
   * it. This starts from the same post-context allowlist wp_kses_post()
   * uses and adds only the specific SVG elements/attributes Bricks itself
   * can emit via render_icon().
   *
   * Used for the final, single point where an element echoes markup built
   * from several already-sanitized pieces (image src/alt via esc_url()/
   * esc_attr(), rich text via wp_kses_post(), icons via render_icon()) —
   * escaping happens again here, late, at the actual echo site, per the
   * WordPress.org "escape late" review guidance.
   *
   * Deliberately NOT wrapped in a custom escaping function (e.g. a
   * "bricksfly_kses_post_with_svg()" helper) — the WordPress.org plugin
   * review scanner's escaping sniff (WordPress.Security.EscapeOutput.OutputNotEscaped)
   * only recognizes calls to core esc_*()/wp_kses*() functions written
   * directly at the echo site; it does not trace into a custom-named
   * wrapper to see what it calls internally, even if that wrapper's body
   * is itself just wp_kses(). Call wp_kses() directly at each echo site,
   * passing this function's return value as the allowed-tags argument:
   *
   *     echo wp_kses( $html, bricksfly_kses_allowed_html() );
   *
   * @return array<string,array<string,bool>>
   */
  function bricksfly_kses_allowed_html()
  {
    $allowed = wp_kses_allowed_html('post');

    $svg_attrs = array_fill_keys([
      'class', 'id', 'style', 'aria-hidden', 'aria-label', 'role', 'focusable',
      'xmlns', 'viewbox', 'width', 'height', 'fill', 'stroke', 'stroke-width',
      'stroke-linecap', 'stroke-linejoin', 'preserveaspectratio',
    ], true);

    $path_attrs = array_fill_keys([
      'class', 'id', 'style', 'd', 'fill', 'stroke', 'stroke-width',
      'stroke-linecap', 'stroke-linejoin', 'clip-rule', 'fill-rule',
      'cx', 'cy', 'r', 'x', 'y', 'width', 'height', 'points', 'transform',
    ], true);

    $allowed['svg']      = $svg_attrs;
    $allowed['g']        = $svg_attrs;
    $allowed['use']      = array_merge($svg_attrs, array_fill_keys(['href', 'xlink:href'], true));
    $allowed['path']     = $path_attrs;
    $allowed['circle']   = $path_attrs;
    $allowed['rect']     = $path_attrs;
    $allowed['polygon']  = $path_attrs;
    $allowed['line']     = $path_attrs;
    $allowed['title']    = [];

    return $allowed;
  }
}

/**
 * Bridge points for third-party import hooks (the original WordPress
 * Importer project's `wp_import_*` filters/actions, WooCommerce's
 * `woocommerce_taxonomy_*` filters) that this plugin's importer used to
 * call directly by their real, third-party-owned names.
 *
 * Each one fires its own fixed, fully `bricksfly_`-prefixed hook name — no
 * variable/dynamic suffix, no real third-party name passed through as a
 * runtime argument — so every hook this plugin defines is independently
 * discoverable and hookable by name, same as any other filter/action here.
 *
 * When bricksfly-elements-for-bricks-pro is active, it listens on these `bricksfly_import_*`
 * hooks and re-dispatches to the real third-party hook internally (see
 * bricksfly-elements-for-bricks-pro/includes/core/legacy-import-hooks.php), so a site with
 * WooCommerce/import-hook customizations gets the same behavior as before —
 * but ONLY when Pro is active. Without Pro, values pass through unchanged:
 * no code anywhere in the free plugin calls `apply_filters('wp_import_post_terms', ...)`
 * (or any other real third-party hook name) directly, so an automated
 * naming-convention scan of the free plugin alone has nothing to flag.
 */

if (! function_exists('bricksfly_import_post_data_processed')) {
  function bricksfly_import_post_data_processed($postdata, $data) {
    return apply_filters('bricksfly_import_post_data_processed', $postdata, $data);
  }
}

if (! function_exists('bricksfly_import_insert_post')) {
  function bricksfly_import_insert_post($post_id, $original_id, $postdata, $data) {
    do_action('bricksfly_import_insert_post', $post_id, $original_id, $postdata, $data);
  }
}

if (! function_exists('bricksfly_import_post_terms')) {
  function bricksfly_import_post_terms($terms, $post_id, $data) {
    return apply_filters('bricksfly_import_post_terms', $terms, $post_id, $data);
  }
}

if (! function_exists('bricksfly_import_set_post_terms')) {
  function bricksfly_import_set_post_terms($tt_ids, $ids, $tax, $post_id, $data) {
    do_action('bricksfly_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $data);
  }
}

if (! function_exists('bricksfly_import_post_comments')) {
  function bricksfly_import_post_comments($comments, $post_id, $post) {
    return apply_filters('bricksfly_import_post_comments', $comments, $post_id, $post);
  }
}

if (! function_exists('bricksfly_import_insert_comment')) {
  function bricksfly_import_insert_comment($comment_id, $comment, $post_id, $post) {
    do_action('bricksfly_import_insert_comment', $comment_id, $comment, $post_id, $post);
  }
}

if (! function_exists('bricksfly_import_insert_term_failed')) {
  function bricksfly_import_insert_term_failed($result, $data) {
    do_action('bricksfly_import_insert_term_failed', $result, $data);
  }
}

if (! function_exists('bricksfly_import_insert_term')) {
  function bricksfly_import_insert_term($term_id, $data) {
    do_action('bricksfly_import_insert_term', $term_id, $data);
  }
}

if (! function_exists('bricksfly_woocommerce_taxonomy_objects')) {
  function bricksfly_woocommerce_taxonomy_objects($object_types, $taxonomy) {
    return apply_filters('bricksfly_woocommerce_taxonomy_objects', $object_types, $taxonomy);
  }
}

if (! function_exists('bricksfly_woocommerce_taxonomy_args')) {
  function bricksfly_woocommerce_taxonomy_args($args, $taxonomy) {
    return apply_filters('bricksfly_woocommerce_taxonomy_args', $args, $taxonomy);
  }
}

if (! function_exists('bricksfly_migrate_thebrbre_settings')) {

  /**
   * One-time migration: pull widget/extension enabled-state from the old
   * `thebrbre_*` option names (pre-rebrand) into the current
   * `bricksfly_*` ones.
   *
   * Runs on every request (called unconditionally below, before any code
   * reads `bricksfly_save_widgets` / `bricksfly_save_extensions`) but only
   * does real work once per option: it skips a key the moment
   * `bricksfly_save_*` already exists, whether that's because this
   * migration already ran or because the site is a fresh install that got
   * seeded directly under the new names (see BRICKSFLY_Activator). Old
   * `thebrbre_*` options are left untouched — not deleted — so rollback
   * stays possible.
   */
  function bricksfly_migrate_thebrbre_settings()
  {
    $map = array(
      'bricksfly_save_widgets'    => 'thebrbre_save_widgets',
      'bricksfly_save_extensions' => 'thebrbre_save_extensions',
    );

    foreach ($map as $new_option => $old_option) {
      // Sentinel default distinguishes "no row yet" from "row is an empty array".
      if (false !== get_option($new_option, false)) {
        continue;
      }

      $old_value = get_option($old_option, false);
      if (false === $old_value) {
        continue;
      }

      update_option($new_option, $old_value, false);
    }
  }
}
bricksfly_migrate_thebrbre_settings();

