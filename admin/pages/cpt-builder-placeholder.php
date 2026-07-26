<?php

namespace wealcoder\thebricksfly\Admin\Pages;

if (! defined('ABSPATH')) exit;

/**
 * Placeholder admin page for the (Pro-only) CPT Builder menu.
 *
 * The real CPT Builder — including the code that registers a site's saved
 * custom post types/taxonomies with WordPress — lives in the Pro plugin and
 * only registers when a valid license is active. This class stands in when
 * Pro is missing or its license is invalid so the user sees the menu and an
 * upsell view explaining what's required, instead of the feature just
 * silently disappearing.
 *
 * Unlike Site Settings (purely cosmetic extras), losing CPT Builder also
 * means any previously-created custom post types/taxonomies stop
 * registering — their admin screens and single-post URLs stop working. The
 * copy below says that plainly rather than only upselling.
 */
class THEBRBRE_CPT_Builder_Placeholder
{
	const MENU_PAGE_SLUG  = 'thebrbre_addons_page';
	const CPT_BUILDER_SLUG = 'thebrbre-cpt-builder';
	const MENU_CAPABILITY = 'manage_options';
	const PRO_BASENAME    = 'the-bricksfly-pro/the-bricksfly-pro.php';

	private static ?self $_instance = null;

	public static function instance(): self
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function __construct()
	{
		add_action('admin_menu',    [$this, 'add_submenu'], 30);
		add_action('admin_notices', [$this, 'render_notice']);

		// Several AAB admin pages (Dashboard, Page Import, Setup Wizard)
		// call remove_all_actions('admin_notices') at in_admin_header
		// priority 1000 to strip third-party banners. Re-register the
		// upsell notice at priority 1001 so it survives the strip on
		// those screens. WP's add_action is idempotent for the same
		// callback/priority, so pages that don't strip won't double-render.
		add_action('in_admin_header', [$this, 'reattach_notice'], 1001);
	}

	public function reattach_notice(): void
	{
		add_action('admin_notices', [$this, 'render_notice']);
	}

	/**
	 * Pro is active AND its license is valid — the full CPT Builder UI
	 * (and its registration of already-created CPTs/taxonomies) is loaded
	 * from the Pro plugin, so the placeholder must stand down.
	 */
	private function is_unlocked(): bool
	{
		return function_exists('thebrbre_pro_is_license_valid') && thebrbre_pro_is_license_valid();
	}

	private function is_pro_plugin_active(): bool
	{
		if (! function_exists('is_plugin_active')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return is_plugin_active(self::PRO_BASENAME);
	}

	public function add_submenu(): void
	{
		if (! current_user_can(self::MENU_CAPABILITY)) return;
		if ($this->is_unlocked()) return; // Pro owns the real page.

		add_submenu_page(
			self::MENU_PAGE_SLUG,
			esc_html__('CPT Builder', 'the-bricksfly'),
			esc_html__('CPT Builder', 'the-bricksfly'),
			self::MENU_CAPABILITY,
			self::CPT_BUILDER_SLUG,
			[$this, 'render_page']
		);
	}

	public function render_page(): void
	{
		$pro_active = $this->is_pro_plugin_active();

		$title = $pro_active
			? __('Activate your license to unlock CPT Builder', 'the-bricksfly')
			: __('CPT Builder requires the Pro plugin', 'the-bricksfly');

		$body = $pro_active
			? __('Bricksfly Pro is installed, but its license is not active for this site. Activate the license to manage custom post types and taxonomies again.', 'the-bricksfly')
			: __('CPT Builder is a Pro feature. Install and activate Bricksfly Pro, then activate your license, to use it.', 'the-bricksfly');

		$warning = __('If you previously created custom post types or taxonomies with CPT Builder, they will stop working — their admin screens and post URLs — until Bricksfly Pro is active with a valid license.', 'the-bricksfly');

		$cta_url = $pro_active
			? admin_url('admin.php?page=thebrbre_addons_settings&bf-license=1')
			: 'https://bricksfly.com/';

		$cta_label = $pro_active
			? __('Activate License', 'the-bricksfly')
			: __('Get Pro', 'the-bricksfly');

		$cta_target = $pro_active ? '' : ' target="_blank" rel="noopener"';

		// Matches the dashboard's "pro" button variant (Button variant="pro" in
		// src/admin/dashboard/components/ui/button.jsx): bg #FFD53E, gray-600 text,
		// 10px radius, shadow-common that deepens to shadow-pro on hover.
		$cta_style = 'display:inline-flex;align-items:center;justify-content:center;'
			. 'height:40px;padding:0 18px;border-radius:10px;'
			. 'background:#FFD53E;color:hsl(222,11%,36%);'
			. 'font-size:14px;font-weight:500;text-decoration:none;'
			. 'box-shadow:0px 1px 4px 0px rgba(10,13,20,0.03);'
			. 'transition:box-shadow .15s ease;';

		echo '<div class="wrap aab-settings-placeholder" style="max-width:780px;">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<h1>' . esc_html__('CPT Builder', 'the-bricksfly') . '</h1>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="notice notice-warning inline" style="padding:16px 20px;margin-top:16px;">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<h2 style="margin-top:0;">' . esc_html($title) . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<p>' . esc_html($body) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<p><strong>' . esc_html($warning) . '</strong></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<p><a href="' . esc_url($cta_url) . '" style="' . esc_attr($cta_style) . '"'
			. ' onmouseover="this.style.boxShadow=\'0px 4px 12px 0px rgba(10,13,20,0.06)\'"'
			. ' onmouseout="this.style.boxShadow=\'0px 1px 4px 0px rgba(10,13,20,0.03)\'"'
			. $cta_target . '>' . esc_html($cta_label) . '</a></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Scoped admin-wide banner. The license-invalid case is handled by
	 * the Pro plugin's own notice to avoid duplication.
	 *
	 * Scoped directly to the CPT Builder screen via its own slug, rather
	 * than the broader THEBRBRE_Settings_Placeholder::is_thebrbre_admin_screen()
	 * helper (which matches any thebrbre-prefixed screen), so this notice
	 * only appears on the CPT Builder page itself.
	 */
	public function render_notice(): void
	{
		if (! current_user_can(self::MENU_CAPABILITY)) return;
		if ($this->is_unlocked()) return;
		if ($this->is_pro_plugin_active()) return; // Pro renders the license notice.
		if (! self::is_cpt_builder_screen()) return;

		echo '<div class="notice notice-warning"><p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<strong>' . esc_html__('Bricksfly:', 'the-bricksfly') . '</strong> '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo esc_html__('Install and activate the Pro plugin to unlock CPT Builder and other premium features.', 'the-bricksfly');
		echo '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * True when the current admin screen is the CPT Builder page itself
	 * (real or placeholder) — matches the same substring check the
	 * original CPT Builder's own `admin_scripts()` used before the move.
	 */
	public static function is_cpt_builder_screen(): bool
	{
		if (! function_exists('get_current_screen')) return false;
		$screen = get_current_screen();
		if (! $screen) return false;

		return strpos((string) $screen->id, '_page_' . self::CPT_BUILDER_SLUG) !== false;
	}
}

THEBRBRE_CPT_Builder_Placeholder::instance();
