<?php

namespace AAB\Includes\Extensions\Helpers;

defined('ABSPATH') || exit;

class ResponsiveHelper
{
  /**
   * Cached Bricks breakpoint list (sorted desktop-first by Bricks).
   *
   * @var array<int, array{key:string, width:int, base?:bool}>|null
   */
  private static ?array $breakpoint_cache = null;

  /**
   * Cached base breakpoint key.
   *
   * @var string|null
   */
  private static ?string $base_key_cache = null;

  /**
   * Get the live Bricks Builder breakpoints. Falls back to the historical
   * 4-breakpoint default if Bricks isn't loaded (e.g. theme deactivated).
   *
   * @return array<int, array{key:string, width:int, base?:bool}>
   */
  public static function getBreakpoints(): array
  {
    if (self::$breakpoint_cache !== null) {
      return self::$breakpoint_cache;
    }

    if (class_exists('\\Bricks\\Breakpoints')) {
      $bp = \Bricks\Breakpoints::get_breakpoints();
      if (is_array($bp) && ! empty($bp)) {
        self::$breakpoint_cache = $bp;
        return self::$breakpoint_cache;
      }
    }

    // No-Bricks fallback. `label` and `icon` mirror what real Bricks
    // breakpoint entries carry, so admin UIs (e.g. ScrollSmootherSettings)
    // can render correctly even when Bricks is inactive. Frontend consumers
    // that only need {key, width, base} (e.g. responsive-bootstrap.php)
    // already strip these extra fields, so adding them is safe.
    self::$breakpoint_cache = [
      ['key' => 'desktop',          'label' => 'Desktop',          'icon' => 'laptop',          'width' => 1279, 'base' => true],
      ['key' => 'tablet_portrait',  'label' => 'Tablet portrait',  'icon' => 'tablet-portrait', 'width' => 991],
      ['key' => 'mobile_landscape', 'label' => 'Mobile landscape', 'icon' => 'phone-landscape', 'width' => 767],
      ['key' => 'mobile_portrait',  'label' => 'Mobile portrait',  'icon' => 'phone-portrait',  'width' => 478],
    ];

    return self::$breakpoint_cache;
  }

  /**
   * Get the key of the base breakpoint (the one whose setting key has no suffix).
   * Defaults to 'desktop' if no breakpoint is flagged base.
   */
  public static function getBaseKey(): string
  {
    if (self::$base_key_cache !== null) {
      return self::$base_key_cache;
    }

    if (class_exists('\\Bricks\\Breakpoints') && isset(\Bricks\Breakpoints::$base_key)) {
      self::$base_key_cache = (string) \Bricks\Breakpoints::$base_key;
      return self::$base_key_cache;
    }

    foreach (self::getBreakpoints() as $bp) {
      if (! empty($bp['base'])) {
        self::$base_key_cache = (string) $bp['key'];
        return self::$base_key_cache;
      }
    }

    self::$base_key_cache = 'desktop';
    return self::$base_key_cache;
  }

  /**
   * Build the list of setting-key suffixes to probe for a responsive value.
   * The base breakpoint contributes the empty suffix; non-base breakpoints
   * contribute ":{breakpoint_key}".
   *
   * @return array<int, string>
   */
  private static function getSuffixes(): array
  {
    $suffixes = [];
    foreach (self::getBreakpoints() as $bp) {
      $suffixes[] = ! empty($bp['base']) ? '' : ':' . $bp['key'];
    }
    return $suffixes;
  }

  /**
   * Check if any responsive value exists for $baseKey across all breakpoints.
   */
  public static function hasAnyValue(array $settings, string $baseKey, string $check_value = ''): bool
  {
    foreach (self::getSuffixes() as $suffix) {

      if (! empty($check_value)) {
        if (
          isset($settings[$baseKey . $suffix])
          && $settings[$baseKey . $suffix] !== $check_value
        ) {
          return true;
        }
      } else {
        if (! empty($settings[$baseKey . $suffix])) {
          return true;
        }
      }
    }

    return false;
  }


  /**
   * Normalize responsive values into an array keyed by Bricks breakpoint keys.
   *
   * Output shape: ['<bp_key_1>' => mixed, '<bp_key_2>' => mixed, ...] in the
   * order Bricks returns them (largest width first by default). Each smaller
   * breakpoint inherits from the immediately-larger one when its own value is
   * unset, ultimately falling back to the base breakpoint's value (or $default).
   */
  public static function normalize(
    array $settings,
    string $baseKey,
    $default = null,
    bool $forceDefault = false
  ): ?array {

    // 🚫 User never touched + default not forced
    if (
      ! self::hasAnyValue($settings, $baseKey)
      && ! $forceDefault
    ) {
      return null;
    }

    $base_key   = self::getBaseKey();
    $base_value = $settings[$baseKey] ?? $default;

    $result = [];
    foreach (self::getBreakpoints() as $bp) {
      $key = (string) $bp['key'];

      if ($key === $base_key) {
        $result[$key] = $base_value;
        continue;
      }

      $setting_key = $baseKey . ':' . $key;

      if (array_key_exists($setting_key, $settings) && $settings[$setting_key] !== null && $settings[$setting_key] !== '') {
        $result[$key] = $settings[$setting_key];
      } else {
        // Inherit from the immediately-larger already-processed breakpoint.
        // $result preserves insertion order; Bricks::get_breakpoints() returns
        // entries sorted by width DESC (desktop-first) by default, so the most
        // recently inserted entry is the next-larger breakpoint. If the order
        // is mobile-first, this still works — "previous" still means "the
        // breakpoint Bricks considers next up the cascade."
        $last_key = array_key_last($result);
        $result[$key] = $last_key !== null ? $result[$last_key] : $base_value;
      }
    }

    return $result;
  }

  /**
   * Compact a normalized responsive array for transport (data-aabsettings JSON).
   *
   * - If the input isn't a normalized responsive map (keys don't match Bricks
   *   breakpoint keys, or input isn't an array), returned unchanged.
   * - If every breakpoint has the same value, collapses to that scalar value.
   * - Otherwise drops entries equal to their immediately-larger (cascade-up)
   *   parent — the JS `aabResponsive.resolveResponsive` reconstructs them at
   *   runtime by walking the cascade. Lossless.
   *
   * Use this only on values headed for the wire (JSON in DOM attributes).
   * Don't use it on values fed back into `conditional_responsive_value`,
   * which iterates by literal breakpoint keys.
   *
   * @param mixed $value
   * @return mixed
   */
  public static function compact($value)
  {
    if ( ! is_array( $value ) || empty( $value ) ) {
      return $value;
    }

    $breakpoints = self::getBreakpoints();
    $bp_keys     = array_column( $breakpoints, 'key' );

    // Bail if the array isn't a responsive map (e.g. nested struct like {hex, rgb}).
    foreach ( array_keys( $value ) as $k ) {
      if ( ! in_array( $k, $bp_keys, true ) ) {
        return $value;
      }
    }

    // Collapse to scalar if all values are deeply equal.
    $first     = null;
    $first_set = false;
    $all_equal = true;
    foreach ( $value as $v ) {
      if ( ! $first_set ) {
        $first     = $v;
        $first_set = true;
        continue;
      }
      if ( $v !== $first ) {
        $all_equal = false;
        break;
      }
    }
    if ( $first_set && $all_equal ) {
      return $first;
    }

    // Drop entries equal to their cascade-up parent. Iterate in the same
    // order Bricks returns breakpoints — for desktop-first that's largest →
    // smallest, so the previous kept entry is always the immediately-larger
    // one. For mobile-first it's the immediately-smaller one; either way the
    // cascade direction matches iteration order.
    $compact        = [];
    $previous_value = null;
    $previous_set   = false;
    $base_key       = self::getBaseKey();

    foreach ( $breakpoints as $bp ) {
      $key = (string) $bp['key'];
      if ( ! array_key_exists( $key, $value ) ) {
        continue;
      }
      $current = $value[$key];

      // Always keep the base — it anchors the cascade.
      if ( $key === $base_key ) {
        $compact[$key]  = $current;
        $previous_value = $current;
        $previous_set   = true;
        continue;
      }

      if ( ! $previous_set || $current !== $previous_value ) {
        $compact[$key]  = $current;
        $previous_value = $current;
        $previous_set   = true;
      }
    }

    // If only the base survived, return its scalar.
    if ( count( $compact ) === 1 && array_key_exists( $base_key, $compact ) ) {
      return $compact[$base_key];
    }

    return $compact;
  }

  /**
   * Apply `compact()` to every value in an associative array. Non-responsive
   * fields and nested structs (color objects, etc.) pass through unchanged.
   *
   * @param array $data
   * @return array
   */
  public static function compactAll(array $data): array
  {
    foreach ( $data as $field => $value ) {
      $data[$field] = self::compact( $value );
    }
    return $data;
  }

  public static function conditional_responsive_value(
    string $baseKey, // key name of that field
    array $benchmarkRequiredValue, // [ image_animation => reveal ]
    array $benchmarkCurrentList,  // image_animation: [ desktop => reveal, tablet_portrait => none, mobile_portrait => scale ]
    array $settings,
    $default = null,
    bool $forceDefault = false
  ) {

    $normalized = self::normalize(
      $settings,
      $baseKey,
      $default,
      $forceDefault
    );

    if ($normalized === null) {
      return null;
    }

    $result = []; // start_from: [ desktop => right, tablet_portrait => left, mobile_portrait => null ]

    foreach ($benchmarkCurrentList as $key => $value) {

      if (in_array($value, $benchmarkRequiredValue, true)) {
        $result[$key] = $normalized[$key] ?? null;
      } else {
        $result[$key] = null;
      }
    }

    return $result;
  }
}
