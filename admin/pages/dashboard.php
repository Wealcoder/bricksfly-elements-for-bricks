<?php

namespace wealcoder\thebricksfly\Admin\Pages;

if (! defined('ABSPATH')) {
	exit();
} // Exit if accessed directly

class THEBRBRE_Admin_Init
{


	use \wealcoder\thebricksfly\Includes\Traits\Extension_Widgets_Trait;

	/**
	 * Parent Menu Page Slug
	 */
	const MENU_PAGE_SLUG = 'thebrbre_addons_page';

	/**
	 * Menu capability
	 */
	const MENU_CAPABILITY = 'manage_options';

	/**
	 * [$parent_menu_hook] Parent Menu Hook
	 *
	 * @var string
	 */
	static $parent_menu_hook = '';

	/**
	 * [$_instance]
	 *
	 * @var null
	 */
	private static $_instance = null;
	private $plugin_file = null;

	/**
	 * [instance] Initializes a singleton instance
	 *
	 * @return [_Admin_Init]
	 */
	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function __construct()
	{
		$this->plugin_file = WP_PLUGIN_DIR . '/the-bricksfly-pro/the-bricksfly-pro.php';

		$this->remove_all_notices();
		$this->include();
		$this->init();
	}

	/**
	 * Recursively walk the plugin config tree and return a map of Pro leaf
	 * slugs. Used to strip Pro toggles from AJAX save payloads when the
	 * license is not valid.
	 *
	 * @param array $nodes Config subtree.
	 * @param array $acc   Accumulator passed through recursion.
	 * @return array Map of slug => true for every leaf with `is_pro=true`.
	 */
	public static function thebrbre_collect_pro_slugs($nodes, $acc = array())
	{
		if (! is_array($nodes)) {
			return $acc;
		}
		foreach ($nodes as $slug => $data) {
			if (! is_array($data)) {
				continue;
			}
			if (isset($data['elements']) && is_array($data['elements'])) {
				$acc = self::thebrbre_collect_pro_slugs($data['elements'], $acc);
				continue;
			}
			if (! empty($data['is_pro'])) {
				$acc[$slug] = true;
			}
		}
		return $acc;
	}

	function admin_classes($classes)
	{
		// Get the current admin screen object
		$screen = get_current_screen();

		// Ensure $classes is a string
		if (! is_string($classes)) {
			$classes = '';
		}

		// Check if we are on the correct page
		if ($screen && strpos($screen->id, '_page_thebrbre_addons_settings') !== false) {
			$classes .= ' wcf-anim2024';
		}

		return $classes;
	}


	/**
	 * [init] Assets Initializes
	 *
	 * @return [void]
	 */
	public function init()
	{

		add_action('admin_menu', array($this, 'add_menu'), 25);
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action('wp_ajax_thebrbre_save_settings', array($this, 'save_settings'));
		add_action('wp_ajax_thebrbre_dashboard_notice_store', array($this, 'notice_store'));
		add_action('wp_ajax_thebrbre_get_notice_data', array($this, 'get_notice'));
		add_action('wp_ajax_thebrbre_save_dashboard_settings', array($this, 'save_settings_dashboard'));

		add_action('wp_ajax_thebrbre_save_smooth_scroller_settings', array($this, 'save_smooth_scroller_settings'));

		add_filter('admin_body_class', array($this, 'admin_classes'), 100);
		add_filter('thebrbre_dashboard_config', array($this, 'dashboard_db_widgets_config'), 11);
		add_filter('thebrbre_dashboard_config', array($this, 'dashboard_db_extnsions_config'), 10);
		add_filter('thebrbre_dashboard_config', array($this, 'thebrbre_dashboard_integrations_config'), 10);

		add_action('admin_footer', array($this, 'admin_footer'));
		// Bust the remote-menu transient whenever the builder clears its
		// cache. Bricks has no exact equivalent of Elementor's files-cache
		// hook, so the safest generic hook is `switch_theme`.
		add_action('switch_theme', function () {
			delete_transient('thebrbre_menu_42_data');
		});

		//add_action('wp_dashboard_setup', [$this, 'dashboard_widget'], 999);
	}

	public function dashboard_widget()
	{


		if (file_exists($this->plugin_file)) {
			return;
		}

		wp_add_dashboard_widget(
			'aae_dashboard_widget',
			'Animation Addons Overview',
			[$this, 'thebrbre_render_dashboard_widget']
		);


		global $wp_meta_boxes;

		// Check that our widget actually exists before reordering
		if (isset($wp_meta_boxes['dashboard']['normal']['core']['aae_dashboard_banner'])) {
			// Get current dashboard widgets
			$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

			// Backup our widget
			$aae_widget_backup = [
				'aae_dashboard_banner' => $normal_dashboard['aae_dashboard_banner']
			];

			// Remove from bottom and merge on top
			unset($normal_dashboard['aae_dashboard_banner']);
			$sorted_dashboard = array_merge($aae_widget_backup, $normal_dashboard);

			// Assign back
			$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
		}
	}

	function thebrbre_render_dashboard_widget()
	{
		$view = __DIR__ . '/banner/ads.php';
		require_once $view;
	}


	/**
	 * merge database saved data with dasboard widgets config
	 *
	 * @return [void]
	 */
	public function dashboard_db_widgets_config($configs)
	{
		$wgt           = get_option('thebrbre_save_widgets');
		$saved_widgets = is_array($wgt) ? array_keys(array_filter($wgt)) : array();
		$widgets       = $configs['widgets'];
		thebrbre_get_db_updated_config($widgets, $saved_widgets);
		$configs['widgets'] = $widgets;
		return $configs;
	}

	/**
	 * merge database saved data with dasboard ext config
	 *
	 * @return [void]
	 */
	public function dashboard_db_extnsions_config($configs)
	{
		$ext        = get_option('thebrbre_save_extensions');
		$saved_ext  = is_array($ext) ? array_keys(array_filter($ext)) : array();
		$extensions = $configs['extensions'];
		thebrbre_get_db_updated_config($extensions, $saved_ext);
		$configs['extensions'] = $extensions;
		return $configs;
	}

	/**
	 * [include] Load Necessary file
	 *
	 * @return [void]
	 */
	public function include()
	{
		$admin_dir = THEBRBRE_PATH . 'admin/';

		// Row actions & plugin installer.
		require_once $admin_dir . 'row-actions.php';
		require_once $admin_dir . 'plugin-installer.php';

		// Base importer classes.
		require_once $admin_dir . 'base/Helpers.php';
		require_once $admin_dir . 'base/Downloader.php';
		require_once $admin_dir . 'base/WPImporterLogger.php';
		require_once $admin_dir . 'base/WPImporterLoggerCLI.php';
		require_once $admin_dir . 'base/WXRImportInfo.php';

		if (! class_exists('\WP_Importer')) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
		}

		require_once $admin_dir . 'base/WXRImporter.php';
		require_once $admin_dir . 'aab-importer.php';
		require_once $admin_dir . 'Logger.php';
		require_once $admin_dir . 'Importer.php';
		require_once $admin_dir . 'st-init.php';

		// Notices system.
		require_once $admin_dir . 'Notices/Notices.php';
		require_once $admin_dir . 'Notices/ShowNotices.php';

		// CPT Builder.
		require_once $admin_dir . 'cpt-builder.php';

		// Initialize OneClickImport.
		$oneimport = \wealcoder\thebricksfly\Admin\Base\OneClickImport::get_instance();
	}



	/**
	 * [add_menu] Admin Menu
	 */
	public function add_menu()
	{
		if (! (current_user_can('manage_options'))) {
			return;
		}
		self::$parent_menu_hook = add_menu_page(
			esc_html__('Bricksfly', 'the-bricksfly'),
			esc_html__('Bricksfly', 'the-bricksfly'),
			self::MENU_CAPABILITY,
			self::MENU_PAGE_SLUG,
			'',
			THEBRBRE_URL . 'assets/images/aab.png',
			80
		);

		add_submenu_page(
			self::MENU_PAGE_SLUG,
			esc_html__('Settings', 'the-bricksfly'),
			esc_html__('Settings', 'the-bricksfly'),
			'manage_options',
			'thebrbre_addons_settings',
			array($this, 'plugin_dashboard_entry_page')
		);

		// Remove Parent Submenu
		remove_submenu_page(self::MENU_PAGE_SLUG, self::MENU_PAGE_SLUG);

		global $submenu;


		// License link â€” navigates to the real License Settings page
		// (thebrbre-license-settings), replacing the old React modal
		// entry point (?bf-license=1).
		if (is_plugin_active('the-bricksfly-pro/the-bricksfly-pro.php')) {
			$license_active = function_exists('thebrbre_license_is_valid') && thebrbre_license_is_valid();
			$license_label  = esc_html__('License', 'the-bricksfly');
			if ($license_active) {
				$license_label .= ' <span class="bf-license-menu-badge" style="display:inline-block;margin-left:6px;width:8px;height:8px;border-radius:50%;background:#10b981;vertical-align:middle;"></span>';
			}
			$submenu[self::MENU_PAGE_SLUG][] = array(
				$license_label,
				'manage_options',
				admin_url('admin.php?page=thebrbre-license-settings'),
			);
		}

		// Start Template link â€” deep-links into the React dashboard's
		// "stater-template" tab. Registered via $submenu directly so the
		// `&tab=` query string isn't URL-encoded by add_submenu_page().
		$submenu[self::MENU_PAGE_SLUG][] = array(
			esc_html__('Starter Template', 'the-bricksfly'),
			'manage_options',
			admin_url('admin.php?page=thebrbre_addons_settings&tab=stater-template'),
		);
	}

	/**
	 * [enqueue_scripts] Add Scripts Base Menu Slug
	 *
	 * @param  [string] $hook
	 *
	 * @return [void]
	 */
	public function enqueue_scripts($hook)
	{

		$total_extensions = $total_widgets = 0;

		$screen = get_current_screen();
		if (! $screen || strpos($screen->id, '_page_thebrbre_addons_settings') === false) {
			return;
		}
		//if ($hook == 'animation-addon_page_thebrbre_addons_settings') {
		// sync element manager
		// $this->disable_widgets_by_element_manager();
		$dashboard_css_path = THEBRBRE_PATH . 'public/build/admin/dashboard.css';
		$dashboard_js_path  = THEBRBRE_PATH . 'public/build/admin/dashboard.js';
		$dashboard_css_ver  = file_exists($dashboard_css_path) ? filemtime($dashboard_css_path) : THEBRBRE_VERSION;
		$dashboard_js_ver   = file_exists($dashboard_js_path)  ? filemtime($dashboard_js_path)  : THEBRBRE_VERSION;

		// CSS
		wp_enqueue_style(
			'thebrbre-admin-style', // Handle for the stylesheet
			THEBRBRE_URL . 'public/build/admin/dashboard.css',
			array(), // Dependencies (none in this case)
			$dashboard_css_ver
		);

		wp_enqueue_script('thebrbre-admin', THEBRBRE_URL . 'public/build/admin/dashboard.js', array('wp-element'), $dashboard_js_ver, true);
		thebrbre_get_total_config_elements_by_key($GLOBALS['thebrbre_config']['extensions'], $total_extensions);
		thebrbre_get_total_config_elements_by_key($GLOBALS['thebrbre_config']['widgets'], $total_widgets);

		$widgets       = get_option('thebrbre_save_widgets');
		$saved_widgets = is_array($widgets) ? array_keys(array_filter($widgets)) : array();

		thebrbre_get_search_active_keys($GLOBALS['thebrbre_config']['widgets'], $saved_widgets, $foundKeys, $awidgets);

		$extensions       = get_option('thebrbre_save_extensions');
		$saved_extensions = is_array($extensions) ? array_keys(array_filter($extensions)) : array();

		thebrbre_get_search_active_keys($GLOBALS['thebrbre_config']['extensions'], $saved_extensions, $foundext, $activeext);


		$active_widgets = self::get_widgets();
		$active_ext     = self::get_extensions();
		$font_settings  = wp_unslash(get_option('thebrbre_custom_font_setting'));

		// All Bricks breakpoints (defaults + custom). Routed through the shared
		// ResponsiveHelper so the no-Bricks fallback (with label/icon) lives in
		// one place â€” same source the frontend ResponsiveHelper consumers use.
		$bricks_breakpoints = \wealcoder\thebricksfly\Includes\Extensions\Helpers\ResponsiveHelper::getBreakpoints();

		// License info for the React LicenseDialog (mirrors the shared contract
		// from animation-addons-for-elementor-pro).
		$thebrbre_license_status = (string) get_option('thebrbre_license_status', '');
		$thebrbre_license_key    = (string) get_option('thebrbre_license_key', '');

		// The license counts as valid only when BOTH the Pro plugin folder is
		// installed AND the stored license status is "valid". This gates the
		// React UI so that deleting the Pro plugin folder (or installing just
		// the free plugin) immediately locks every pro toggle â€” regardless of
		// whatever license status survives in the database.
		$pro_installed      = function_exists('thebrbre_is_pro_installed') ? thebrbre_is_pro_installed() : file_exists($this->plugin_file);
		$thebrbre_license_valid  = $pro_installed && ('valid' === $thebrbre_license_status);

		$addons_config = apply_filters('thebrbre_dashboard_config', $GLOBALS['thebrbre_config']);
		$addons_config['sl_lic']    = $thebrbre_license_key;
		$addons_config['is_pro']    = $pro_installed;
		$addons_config['thebrbre_valid'] = $thebrbre_license_valid;

		// NOTE: the compiled React dashboard (shared with animation-addons-for-elementor)
		// uses a strict `39996 === product_status.item_id` check to flip the header button
		// to "Deactivate License" and to pick the deactivate AJAX action. We send 13
		// when the license is valid so the bundled UI recognises the activated state â€”
		// the actual EDD API request uses our real item ID (THEBRBRE_PRO_ITEM_ID).
		// Per-feature license limitations (Template / Section / Page import etc.).
		// Empty array when the license isn't valid â€” the React import gate treats
		// a missing/false flag as "not allowed" and shows the upsell popup.
		$thebrbre_limitations = function_exists('thebrbre_get_license_limitations') ? thebrbre_get_license_limitations() : array();

		$addons_config['product_status'] = [
			'item_id'      => $thebrbre_license_valid ? 39996 : 0,
			'status'       => $thebrbre_license_status,
			'real_item_id' => THEBRBRE_PRO_ITEM_ID,
			'limitations'  => $thebrbre_limitations,
		];

		// Also expose at the top level so components that read the config
		// directly (not via product_status) can reach it.
		$addons_config['limitations'] = $thebrbre_limitations;

		$localize_data = array(
			'ajaxurl'             => admin_url('admin-ajax.php'),
			'isSettingsPage' => true, // ðŸ”¥ IMPORTANT
			'nonce'               => wp_create_nonce('thebrbre_admin_nonce'),
			'addons_config'       => $addons_config,
			'adminURL'            => admin_url(),
			'smoothScroller'      => json_decode(get_option('thebrbre_smooth_scroller')),
			'cf_settings'         => is_string($font_settings) ? json_decode($font_settings) : array(),
			'extensions'          => array(
				'total'  => $total_extensions,
				'active' => is_array($active_ext) ? count($active_ext) : 0,
			),
			'widgets'             => array(
				'total'  => $total_widgets,
				'active' => is_array($active_widgets) ? count($active_widgets) : 0,
			),
			'global_settings_url' => $this->get_elementor_active_edit_url(),
			'theme_builder_url'   => admin_url('edit.php?post_type=wcf-addons-template'),
			'user_role'           => thebrbre_get_current_user_roles(),
			'version'             => THEBRBRE_VERSION,
			'st_template_domain'  => THEBRBRE_TEMPLATE_STARTER_BASE_URL,
			'home_url' => add_query_arg(['aab-cache' => 1], home_url('/')),
			'template_menu' => $this->get_template_menu_data(),
			'plugin_url' => THEBRBRE_URL,
			'has_pro' => file_exists($this->plugin_file),
			'breakpoints' => $bricks_breakpoints,
			'license_settings_url' => admin_url('admin.php?page=thebrbre-license-settings'),

			// Dynamic replacement copy for the compiled React "Upgrade" dialog.
			// The bundle hardcodes the default strings; a small inline script
			// below swaps them at runtime based on current Pro/license state.
			// 'pro_dialog_copy' => $this->get_pro_dialog_copy($thebrbre_license_status, $thebrbre_license_key),

		);
		wp_localize_script('thebrbre-admin', 'THEBRBRE_ADDONS_ADMIN', $localize_data);

		wp_add_inline_script('thebrbre-admin', $this->get_pro_dialog_swap_script(), 'after');
		//}
	}

	/**
	 * Return heading + subtitle strings that replace the hardcoded React
	 * "Upgrade to premium plan" text when the user is not yet fully licensed.
	 *
	 * @return array{heading:string, subtext:string, button:string}
	 */
	private function get_pro_dialog_copy($license_status, $license_key)
	{
		$pro_basename  = 'the-bricksfly-pro/the-bricksfly-pro.php';
		$pro_installed = file_exists(WP_PLUGIN_DIR . '/' . $pro_basename);
		$pro_active    = (function_exists('thebrbre_is_pro_active') && thebrbre_is_pro_active());

		// Case 1 â€” Pro plugin isn't installed: keep the default "Upgradeâ€¦" copy.
		if (! $pro_installed) {
			return array('heading' => '', 'subtext' => '', 'button' => '');
		}

		// Case 2 â€” Pro installed but not active yet.
		if (! $pro_active) {
			return array(
				'heading' => esc_html__('Pro plugin installed â€” activate it to continue', 'the-bricksfly'),
				'subtext' => esc_html__('Head to the Plugins screen and click "Activate" on Bricksfly Pro to enable premium features.', 'the-bricksfly'),
				'button'  => esc_html__('Activate Plugin', 'the-bricksfly'),
			);
		}

		// Case 3 â€” Both active but no license key saved yet.
		if (empty($license_key)) {
			return array(
				'heading' => esc_html__('Activate your license to unlock Pro features', 'the-bricksfly'),
				'subtext' => esc_html__('Enter your purchased license key to enable every premium extension, template, and automatic update.', 'the-bricksfly'),
				'button'  => esc_html__('Activate License', 'the-bricksfly'),
			);
		}

		// Case 4 â€” License key on file but the server says it's invalid.
		if (in_array($license_status, array('invalid', 'missing'), true)) {
			return array(
				'heading' => esc_html__('Your license key is invalid', 'the-bricksfly'),
				'subtext' => esc_html__('The saved license key is not valid for this site. Please re-enter it or purchase a new one.', 'the-bricksfly'),
				'button'  => esc_html__('Re-enter License', 'the-bricksfly'),
			);
		}

		// Case 5 â€” Expired.
		if ('expired' === $license_status) {
			return array(
				'heading' => esc_html__('Your license has expired', 'the-bricksfly'),
				'subtext' => esc_html__('Renew your license to keep receiving updates and premium features.', 'the-bricksfly'),
				'button'  => esc_html__('Renew License', 'the-bricksfly'),
			);
		}

		// Case 6 â€” Disabled / revoked.
		if (in_array($license_status, array('disabled', 'revoked'), true)) {
			return array(
				'heading' => esc_html__('Your license has been disabled', 'the-bricksfly'),
				'subtext' => esc_html__('Please contact support if you believe this is an error.', 'the-bricksfly'),
				'button'  => esc_html__('Contact Support', 'the-bricksfly'),
			);
		}

		// Case 7 â€” Site not yet activated for this URL.
		if ('site_inactive' === $license_status) {
			return array(
				'heading' => esc_html__('This site is not activated on your license', 'the-bricksfly'),
				'subtext' => esc_html__('Activate this site in your license to unlock premium features.', 'the-bricksfly'),
				'button'  => esc_html__('Activate License', 'the-bricksfly'),
			);
		}

		// Valid license â€” nothing to replace.
		return array('heading' => '', 'subtext' => '', 'button' => '');
	}

	/**
	 * Inline JS that swaps the React bundle's hardcoded strings at runtime:
	 *  1. "Upgrade to premium planâ€¦" heading/subtitle with context-aware copy.
	 *  2. "Elementor" â†’ "Bricks Builder" in user-visible text (the compiled
	 *     bundle is shared with the Elementor plugin, so it still ships
	 *     Elementor-branded copy in marketing cards / widget descriptions).
	 */
	private function get_pro_dialog_swap_script()
	{
		return '(function () {
	var copy = (window.THEBRBRE_ADDONS_ADMIN && THEBRBRE_ADDONS_ADMIN.pro_dialog_copy) || { heading: "", subtext: "" };

	var DEFAULTS = {
		heading: "Upgrade to premium plan and unlock every features!",
		subtext: "Upgrade and get access to every feature."
	};

	// Replace "Elementor" with "Bricks Builder" in a text node while leaving
	// URLs and internal slugs (anything containing "/" or ":") alone.
	function rebrandTextNode(node) {
		var text = node.textContent;
		if (text.indexOf("Elementor") === -1) return;
		if (text.indexOf("://") !== -1) return; // URL â€” leave it
		node.textContent = text.replace(/Elementor/g, "Bricks Builder");
	}

	function swap(root) {
		var scope = root || document;

		// 1. Upgrade dialog heading/subtext swap.
		if (copy.heading) {
			scope.querySelectorAll("h2").forEach(function (el) {
				if (el.textContent.trim() === DEFAULTS.heading) {
					el.textContent = copy.heading;
				}
			});
		}
		if (copy.subtext) {
			scope.querySelectorAll("p").forEach(function (el) {
				if (el.textContent.trim() === DEFAULTS.subtext) {
					el.textContent = copy.subtext;
				}
			});
		}

		// 2. Global Elementor â†’ Bricks Builder rebrand for visible text nodes.
		var selector = "h1, h2, h3, h4, h5, h6, p, span, li, button, label, strong, em, small";
		scope.querySelectorAll(selector).forEach(function (el) {
			// Only process direct text children so we don\'t touch elements that
			// contain mixed markup (avoids double-walking nested structures).
			el.childNodes.forEach(function (node) {
				if (node.nodeType === 3) rebrandTextNode(node);
			});
		});
	}

	// The compiled dashboard renders dialogs / cards lazily, so observe
	// mutations on the document body and re-run the swap on added subtrees.
	var observer = new MutationObserver(function (records) {
		for (var i = 0; i < records.length; i++) {
			for (var j = 0; j < records[i].addedNodes.length; j++) {
				var n = records[i].addedNodes[j];
				if (n.nodeType === 1) swap(n);
			}
		}
	});

	function start() {
		swap(document);
		observer.observe(document.body, { childList: true, subtree: true });
	}
	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", start);
	} else {
		start();
	}
})();';
	}

	public function get_template_menu_data()
	{
		$transient_key = 'thebrbre_menu_42_data';
		$cached_data   = get_transient($transient_key);

		// âœ… Return cached data if available
		if ($cached_data !== false) {
			return $cached_data;
		}

		$url      = "https://www.themecrowdy.com/wp-json/wcf/v1/menu/42";
		$response = wp_remote_get($url, [
			'timeout' => 15,
			'sslverify' => true,
			'headers' => [
				'Accept' => 'application/json'
			]
		]);

		// âœ… Validate response
		if (is_wp_error($response)) {
			return [];
		}

		$status_code = wp_remote_retrieve_response_code($response);
		if ($status_code !== 200) {
			return [];
		}

		$body = wp_remote_retrieve_body($response);
		if (empty($body)) {

			return [];
		}

		// âœ… Decode JSON safely
		$data = json_decode($body, true);
		if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {

			return [];
		}

		// âœ… Ensure expected structure exists
		if (! isset($data['items']) || ! is_array($data['items'])) {

			return [];
		}

		// âœ… Cache valid data for 1 hour
		set_transient($transient_key, $data['items'], HOUR_IN_SECONDS);

		return $data['items'];
	}


	function thebrbre_dashboard_integrations_config($configs)
	{

		if (! isset($configs['integrations']['plugins']['elements'])) {
			return $configs;
		}

		$action    = '';
		$data_base = '';
		foreach ($configs['integrations']['plugins']['elements'] as &$plugin) {

			if (thebrbre_get_local_plugin_data($plugin['basename']) === false) {
				$action    = 'Download';
				$data_base = $plugin['download_url'];
			} elseif (is_plugin_active($plugin['basename'])) {
				$action = 'Activated';
			} else {
				$action    = 'Active';
				$data_base = $plugin['basename'];
			}
			$plugin['action']    = $action;
			$plugin['data_base'] = $data_base;
		}

		return $configs;
	}

	/**
	 * Return a link to the builder's global settings so the React dashboard
	 * can surface a "Global Settings" shortcut. Bricks exposes theme styles
	 * at admin.php?page=bricks-settings â€” we link there when Bricks is
	 * detected, otherwise return false and the UI hides the link.
	 *
	 * Method name kept as `get_elementor_active_edit_url()` to preserve the
	 * localized-data key the React bundle reads (`global_settings_url`).
	 */
	public function get_elementor_active_edit_url()
	{
		if (class_exists('\\Bricks\\Elements')) {
			return admin_url('admin.php?page=bricks-settings');
		}

		return false;
	}

	public function admin_footer()
	{
		if (! is_admin()) {
			return;
		}
		// Get the current admin screen
		$screen = get_current_screen();

		// Check if we are on the correct admin page
		if ($screen && strpos($screen->id, '_page_thebrbre_addons_settings') !== false) {
			echo '<div id="aab-admin-toast"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	public function plugin_dashboard_entry_page()
	{
?>
		<div class="wrap wcf-admin-wrapper" id="wcf-admin-ds-cr-js"></div>
<?php
	}

	/**
	 * [remove_all_notices] remove addmin notices
	 *
	 * @return [void]
	 */
	public function remove_all_notices()
	{
		add_action(
			'in_admin_header',
			function () {
				$screen = get_current_screen();
				if ($screen && strpos($screen->id, '_page_thebrbre_addons_settings') !== false) {
					remove_all_actions('admin_notices');
					remove_all_actions('all_admin_notices');
					remove_all_actions('user_admin_notices');
					remove_all_actions('network_admin_notices');
				}
			},
			1000
		);
	}

	/**
	 * Save Settings
	 * Save EA settings data through ajax request
	 *
	 * @access public
	 * @return  void
	 * @since 1.1.2
	 */
	public function save_settings()
	{


		check_ajax_referer('thebrbre_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('you are not allowed to do this action', 'the-bricksfly'));
		}

		if (! isset($_POST['fields'])) {
			return;
		}

		$actives       = $foundkeys = array();
		$option_name   = isset($_POST['settings']) ? sanitize_key(wp_unslash($_POST['settings'])) : '';
		$sanitize_data = sanitize_text_field(wp_unslash($_POST['fields']));
		$settings      = json_decode($sanitize_data, true);
		thebrbre_get_nested_active_config_keys($settings, $found, $actives);


		thebrbre_get_nested_config_keys($settings, $foundkeys, $updatedSettings);

		// License gate: force Pro-only slugs to false when the license is not
		// valid. Guards against forged AJAX payloads that would otherwise
		// bypass the React UI's pro-toggle lockout. Pro slugs stay in the
		// map (as false) so the saved option remains a complete slug list.
		$license_valid = function_exists('thebrbre_is_license_valid') && thebrbre_is_license_valid();
		if (! $license_valid && is_array($updatedSettings)) {
			$pro_slugs = self::thebrbre_collect_pro_slugs(isset($GLOBALS['thebrbre_config']) ? $GLOBALS['thebrbre_config'] : array());
			foreach (array_keys($updatedSettings) as $slug) {
				if (isset($pro_slugs[$slug])) {
					$updatedSettings[$slug] = false;
				}
			}
		}

		if ('thebrbre_save_widgets' === $option_name) {
			$updated = update_option('thebrbre_save_widgets', $updatedSettings);
		} elseif ('thebrbre_save_extensions' === $option_name) {
			$updated = update_option('thebrbre_save_extensions', $updatedSettings);
		} else {
			wp_send_json_error(esc_html__('Invalid settings type.', 'the-bricksfly'), 400);
		}

		$return_message = array(
			'status' => $updated,
			'total'  => is_array($actives) ? count($actives) : 0,
		);
		wp_send_json($return_message);
	}

	public function notice_store()
	{

		check_ajax_referer('thebrbre_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('you are not allowed to do this action', 'the-bricksfly'));
		}

		if (! isset($_POST['notice'])) {
			return;
		}

		$sanitize_data = sanitize_text_field(wp_unslash($_POST['notice']));
		update_option('thebrbre_notice_data', $sanitize_data);

		$return_message = array(
			'message' => esc_html__('Notice Updated', 'the-bricksfly'),
		);
		wp_send_json($return_message);
	}

	public function get_notice()
	{

		check_ajax_referer('thebrbre_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('you are not allowed to do this action', 'the-bricksfly'));
		}

		$return_message = array(
			'notice' => json_decode(get_option('thebrbre_notice_data')),
		);
		wp_send_json($return_message);
	}

	public function save_settings_dashboard()
	{

		check_ajax_referer('thebrbre_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('you are not allowed to do this action', 'the-bricksfly'));
		}

		if (! isset($_POST['fields'])) {
			return;
		}

		$sanitize_data = sanitize_text_field(wp_unslash($_POST['fields']));
		$settings      = json_decode($sanitize_data, true);
		$actives       = get_option('thebrbre_save_widgets');
		if (! is_array($actives)) {
			$actives = array();
		}

		$license_valid = function_exists('thebrbre_is_license_valid') && thebrbre_is_license_valid();
		$pro_slugs     = ! $license_valid
			? self::thebrbre_collect_pro_slugs(isset($GLOBALS['thebrbre_config']) ? $GLOBALS['thebrbre_config'] : array())
			: array();

		// Merge the incoming payload into the stored map. Every slug from the
		// frontend is recorded as true/false; existing keys not in the payload
		// are left alone so partial toggles don't drop other items.
		if (is_array($settings)) {
			foreach ($settings as $slug => $item) {
				$is_active = ! empty($item['is_active']);

				// License gate: Pro slugs are forced to false when the license
				// is not valid, regardless of what the payload requested.
				if (! $license_valid && isset($pro_slugs[$slug])) {
					$actives[$slug] = false;
					continue;
				}

				$actives[$slug] = $is_active;
			}
		}

		// Downgrade any pre-existing Pro slugs in the stored map to false when
		// the license becomes invalid (e.g. expiry between saves).
		if (! $license_valid && ! empty($pro_slugs)) {
			foreach (array_keys($actives) as $slug) {
				if (isset($pro_slugs[$slug])) {
					$actives[$slug] = false;
				}
			}
		}

		$updated  = update_option('thebrbre_save_widgets', $actives);
		$elements = get_option('thebrbre_save_widgets');

		$return_message = array(
			'status' => $updated,
			'total'  => is_array($elements) ? count(array_filter($elements)) : 0,
		);
		wp_send_json($return_message);
	}

	/**
	 * Save smooth scroller Settings
	 * settings data through ajax request
	 *
	 * @access public
	 * @return  void
	 * @since 1.1.2
	 */
	public function save_smooth_scroller_settings()
	{

		check_ajax_referer('thebrbre_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('you are not allowed to do this action', 'the-bricksfly'));
		}

		$raw_settings = isset($_POST['smooth']) ? sanitize_text_field(wp_unslash($_POST['smooth'])) : '';
		if (! is_string($raw_settings) || '' === trim($raw_settings)) {
			wp_send_json_error(esc_html__('Smooth scroller settings are required.', 'the-bricksfly'), 400);
		}

		$settings = sanitize_text_field(wp_unslash($_POST['smooth']));

		$decode = json_decode($settings);
		$option = wp_json_encode($decode);

		// update new settings
		if (! empty($_POST['smooth'])) {

			update_option('thebrbre_smooth_scroller', $option);
			wp_send_json($option);
		}
	}

}

THEBRBRE_Admin_Init::instance();
