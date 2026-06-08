<?php

namespace AABAddons\Admin\Pages;

if (! defined('ABSPATH')) exit;

/**
 * Placeholder admin page for the (Pro-only) Site Settings menu.
 *
 * The real Site Settings page lives in the Pro plugin and only registers
 * when a valid license is present. This class stands in when Pro is
 * missing or its license is invalid so the user sees the menu and an
 * upsell view explaining what's required, instead of the feature just
 * silently disappearing.
 */
class AAB_Settings_Placeholder
{
	const MENU_PAGE_SLUG  = 'aab_addons_page';
	const SETTINGS_SLUG   = 'bf-site-settings';
	const MENU_CAPABILITY = 'manage_options';
	const PRO_BASENAME    = 'bricksfly-pro/bricksfly-pro.php';

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
	 * Pro is active AND its license is valid — the full Site Settings UI
	 * is loaded from the Pro plugin, so the placeholder must stand down.
	 */
	private function is_unlocked(): bool
	{
		return function_exists('aab_pro_is_license_valid') && aab_pro_is_license_valid();
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
			esc_html__('Site Settings', 'bricksfly'),
			esc_html__('Site Settings', 'bricksfly'),
			self::MENU_CAPABILITY,
			self::SETTINGS_SLUG,
			[$this, 'render_page']
		);
	}

	public function render_page(): void
	{
		$pro_active = $this->is_pro_plugin_active();

		$title = $pro_active
			? __('Activate your license to unlock Site Settings', 'bricksfly')
			: __('Site Settings requires the Pro plugin', 'bricksfly');

		$body = $pro_active
			? __('BricksFly Pro is installed, but its license is not active for this site. Activate the license to enable Site Settings (Preloader, Cursor, Scroll Indicator, Scroll-to-Top).', 'bricksfly')
			: __('Site Settings (Preloader, Cursor, Scroll Indicator, Scroll-to-Top) is a Pro feature. Install and activate BricksFly Pro, then activate your license, to use it.', 'bricksfly');

		$cta_url = $pro_active
			? admin_url('admin.php?page=bf_addons_settings&bf-license=1')
			: admin_url('plugin-install.php?s=bricksfly-pro&tab=search&type=term');

		$cta_label = $pro_active
			? __('Activate License', 'bricksfly')
			: __('Get Pro', 'bricksfly');

		echo '<div class="wrap aab-settings-placeholder" style="max-width:780px;">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<h1>' . esc_html__('Site Settings', 'bricksfly') . '</h1>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="notice notice-warning inline" style="padding:16px 20px;margin-top:16px;">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<h2 style="margin-top:0;">' . esc_html($title) . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<p>' . esc_html($body) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<p><a href="' . esc_url($cta_url) . '" class="button button-primary">' . esc_html($cta_label) . '</a></p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Scoped admin-wide banner. The license-invalid case is handled by
	 * the Pro plugin's own notice to avoid duplication.
	 */
	public function render_notice(): void
	{
		if (! current_user_can(self::MENU_CAPABILITY)) return;
		if ($this->is_unlocked()) return;
		if ($this->is_pro_plugin_active()) return; // Pro renders the license notice.
		if (! self::is_aab_admin_screen()) return;

		echo '<div class="notice notice-warning"><p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<strong>' . esc_html__('BricksFly:', 'bricksfly') . '</strong> '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo esc_html__('Install and activate the Pro plugin to unlock Site Settings and other premium features.', 'bricksfly');
		echo '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * True when the current admin screen is part of the AAB plugin's own
	 * menu tree (top-level dashboard or any submenu). Shared by the Pro
	 * plugin's license notice via `AAB_Settings_Placeholder::is_aab_admin_screen()`.
	 */
	public static function is_aab_admin_screen(): bool
	{
		if (! function_exists('get_current_screen')) return false;
		$screen = get_current_screen();
		if (! $screen) return false;

		$id = (string) $screen->id;
		return (
			$id === 'toplevel_page_aab_addons_page'
			|| strpos($id, '_page_aab_addons_') !== false
			|| strpos($id, '_page_aab-') !== false
		);
	}
}

// AAB_Settings_Placeholder::instance();
