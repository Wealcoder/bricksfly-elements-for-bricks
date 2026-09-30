<?php

namespace wealcoder\bricksfly\Admin\Pages;

if (! defined('ABSPATH')) {
	exit();
} // Exit if accessed directly

class BRICKSFLY_Admin_Init
{


	use \wealcoder\bricksfly\Includes\Traits\Extension_Widgets_Trait;

	/**
	 * Parent Menu Page Slug
	 */
	const MENU_PAGE_SLUG = 'bricksfly_addons_page';

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
		$this->remove_all_notices();
		$this->include();
		$this->init();
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
		if ($screen && strpos($screen->id, '_page_bricksfly_addons_settings') !== false) {
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
		add_action('wp_ajax_bricksfly_save_settings', array($this, 'save_settings'));
		add_action('wp_ajax_bricksfly_dashboard_notice_store', array($this, 'notice_store'));
		add_action('wp_ajax_bricksfly_get_notice_data', array($this, 'get_notice'));
		add_action('wp_ajax_bricksfly_save_dashboard_settings', array($this, 'save_settings_dashboard'));

		add_action('wp_ajax_bricksfly_save_smooth_scroller_settings', array($this, 'save_smooth_scroller_settings'));

		add_filter('admin_body_class', array($this, 'admin_classes'), 100);
		add_filter('bricksfly_dashboard_config', array($this, 'dashboard_db_widgets_config'), 11);
		add_filter('bricksfly_dashboard_config', array($this, 'dashboard_db_extnsions_config'), 10);
		add_filter('bricksfly_dashboard_config', array($this, 'bricksfly_dashboard_integrations_config'), 10);

		add_action('admin_footer', array($this, 'admin_footer'));
		// Bust the remote-menu transient whenever the builder clears its
		// cache. Bricks has no exact equivalent of Elementor's files-cache
		// hook, so the safest generic hook is `switch_theme`.
		add_action('switch_theme', function () {
			delete_transient('bricksfly_menu_42_data');
		});

		//add_action('wp_dashboard_setup', [$this, 'dashboard_widget'], 999);
	}

	public function dashboard_widget()
	{


		if (bricksfly_is_pro_installed()) {
			return;
		}

		wp_add_dashboard_widget(
			'bricksfly_dashboard_widget',
			'Animation Addons Overview',
			[$this, 'bricksfly_render_dashboard_widget']
		);


		global $wp_meta_boxes;

		// Check that our widget actually exists before reordering
		if (isset($wp_meta_boxes['dashboard']['normal']['core']['bricksfly_dashboard_widget'])) {
			// Get current dashboard widgets
			$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

			// Backup our widget
			$bricksfly_widget_backup = [
				'bricksfly_dashboard_widget' => $normal_dashboard['bricksfly_dashboard_widget']
			];

			// Remove from bottom and merge on top
			unset($normal_dashboard['bricksfly_dashboard_widget']);
			$sorted_dashboard = array_merge($bricksfly_widget_backup, $normal_dashboard);

			// Assign back
			$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
		}
	}

	function bricksfly_render_dashboard_widget()
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
		$wgt           = get_option('bricksfly_save_widgets');
		$saved_widgets = is_array($wgt) ? array_keys(array_filter($wgt)) : array();
		$widgets       = $configs['widgets'];
		bricksfly_get_db_updated_config($widgets, $saved_widgets);
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
		$ext        = get_option('bricksfly_save_extensions');
		$saved_ext  = is_array($ext) ? array_keys(array_filter($ext)) : array();
		$extensions = $configs['extensions'];
		bricksfly_get_db_updated_config($extensions, $saved_ext);
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
		$admin_dir = BRICKSFLY_PATH . 'admin/';

		// Row actions & plugin installer.
		require_once $admin_dir . 'row-actions.php';
		require_once $admin_dir . 'plugin-installer.php';

		// Base importer classes.
		require_once $admin_dir . 'base/Helpers.php';
		require_once $admin_dir . 'base/Downloader.php';
		require_once $admin_dir . 'base/WPImporterLogger.php';
		require_once $admin_dir . 'base/WPImporterLoggerCLI.php';
		require_once $admin_dir . 'base/WXRImportInfo.php';

		// The WXR importer classes (and core's class-wp-importer.php they
		// extend) are loaded only when a content import runs — see
		// OneClickImport::load_importer_classes().
		require_once $admin_dir . 'Logger.php';
		require_once $admin_dir . 'st-init.php';

		// Notices system.
		require_once $admin_dir . 'Notices/Notices.php';
		require_once $admin_dir . 'Notices/ShowNotices.php';

		// CPT Builder is part of the separate BricksFly Pro plugin; the free
		// plugin registers no CPT Builder menu or placeholder.

		// Initialize OneClickImport.
		$oneimport = \wealcoder\bricksfly\Admin\Base\OneClickImport::get_instance();
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
			esc_html__('Bricksfly', 'bricksfly-elements-for-bricks'),
			esc_html__('Bricksfly', 'bricksfly-elements-for-bricks'),
			self::MENU_CAPABILITY,
			self::MENU_PAGE_SLUG,
			'',
			BRICKSFLY_URL . 'assets/images/aab.png',
			80
		);

		add_submenu_page(
			self::MENU_PAGE_SLUG,
			esc_html__('Settings', 'bricksfly-elements-for-bricks'),
			esc_html__('Settings', 'bricksfly-elements-for-bricks'),
			'manage_options',
			'bricksfly_addons_settings',
			array($this, 'plugin_dashboard_entry_page')
		);

		// Remove Parent Submenu
		remove_submenu_page(self::MENU_PAGE_SLUG, self::MENU_PAGE_SLUG);

		global $submenu;


		// Start Template link â€” deep-links into the React dashboard's
		// "stater-template" tab. Registered via $submenu directly so the
		// `&tab=` query string isn't URL-encoded by add_submenu_page().
		$submenu[self::MENU_PAGE_SLUG][] = array(
			esc_html__('Starter Template', 'bricksfly-elements-for-bricks'),
			'manage_options',
			admin_url('admin.php?page=bricksfly_addons_settings&tab=stater-template'),
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
		if (! $screen || strpos($screen->id, '_page_bricksfly_addons_settings') === false) {
			return;
		}
		//if ($hook == 'animation-addon_page_bricksfly_addons_settings') {
		// sync element manager
		// $this->disable_widgets_by_element_manager();
		$dashboard_css_path = BRICKSFLY_PATH . 'public/build/admin/dashboard.css';
		$dashboard_js_path  = BRICKSFLY_PATH . 'public/build/admin/dashboard.js';
		$dashboard_css_ver  = file_exists($dashboard_css_path) ? filemtime($dashboard_css_path) : BRICKSFLY_VERSION;
		$dashboard_js_ver   = file_exists($dashboard_js_path)  ? filemtime($dashboard_js_path)  : BRICKSFLY_VERSION;

		// CSS
		wp_enqueue_style(
			'bricksfly-admin-style', // Handle for the stylesheet
			BRICKSFLY_URL . 'public/build/admin/dashboard.css',
			array(), // Dependencies (none in this case)
			$dashboard_css_ver
		);

		wp_enqueue_script('bricksfly-admin', BRICKSFLY_URL . 'public/build/admin/dashboard.js', array('wp-element'), $dashboard_js_ver, true);
		bricksfly_get_total_config_elements_by_key($GLOBALS['bricksfly_config']['extensions'], $total_extensions);
		bricksfly_get_total_config_elements_by_key($GLOBALS['bricksfly_config']['widgets'], $total_widgets);

		$widgets       = get_option('bricksfly_save_widgets');
		$saved_widgets = is_array($widgets) ? array_keys(array_filter($widgets)) : array();

		bricksfly_get_search_active_keys($GLOBALS['bricksfly_config']['widgets'], $saved_widgets, $foundKeys, $awidgets);

		$extensions       = get_option('bricksfly_save_extensions');
		$saved_extensions = is_array($extensions) ? array_keys(array_filter($extensions)) : array();

		bricksfly_get_search_active_keys($GLOBALS['bricksfly_config']['extensions'], $saved_extensions, $foundext, $activeext);


		$active_widgets = self::get_widgets();
		$active_ext     = self::get_extensions();
		$font_settings  = wp_unslash(get_option('bricksfly_custom_font_setting'));

		// All Bricks breakpoints (defaults + custom). Routed through the shared
		// ResponsiveHelper so the no-Bricks fallback (with label/icon) lives in
		// one place â€” same source the frontend ResponsiveHelper consumers use.
		$bricks_breakpoints = \wealcoder\bricksfly\Includes\Extensions\Helpers\ResponsiveHelper::getBreakpoints();

		$addons_config = apply_filters('bricksfly_dashboard_config', $GLOBALS['bricksfly_config']);
		// Whether the separate BricksFly Pro plugin is installed (drives the
		// "Get Pro" / "Activate plugin" buttons on Pro items).
		$addons_config['is_pro'] = bricksfly_is_pro_installed();

		$localize_data = array(
			'ajaxurl'             => admin_url('admin-ajax.php'),
			'isSettingsPage' => true, // ðŸ”¥ IMPORTANT
			'nonce'               => wp_create_nonce('bricksfly_admin_nonce'),
			'addons_config'       => $addons_config,
			'adminURL'            => admin_url(),
			'smoothScroller'      => (function () {
				$raw = get_option('bricksfly_smooth_scroller');
				return is_string($raw) ? json_decode($raw) : null;
			})(),
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
			'user_role'           => bricksfly_get_current_user_roles(),
			'version'             => BRICKSFLY_VERSION,
			'st_template_domain'  => BRICKSFLY_TEMPLATE_STARTER_BASE_URL,
			// Pro library items show a "Pro" badge and this link. The separate
			// BricksFly Pro plugin sets `pro_library` so its users can import
			// them; the template library decides what it releases.
			'pro_url'             => 'https://bricksfly.com/pricing/',
			'pro_library'         => (bool) apply_filters( 'bricksfly_pro_library_enabled', false ),
			// What the button on Pro cards does (get / activate Pro, or Pro's own action).
			'pro_action'          => function_exists( 'bricksfly_pro_action' ) ? bricksfly_pro_action() : array(),
			// True when an add-on (BricksFly Pro) installs/activates a template's
			// required plugins during import. The free plugin never does.
			'import_plugins'      => (bool) apply_filters( 'bricksfly_import_manages_plugins', false ),
			'home_url' => home_url('/'),
			'plugin_url' => BRICKSFLY_URL,
			'has_pro' => bricksfly_is_pro_installed(),
			'breakpoints' => $bricks_breakpoints,
			// Pro elements / extensions live in the separate BricksFly Pro
			// plugin. It sets `pro_features` when its features are available and
			// may replace the call-to-action shown on Pro items.
			'pro_features'        => (bool) apply_filters( 'bricksfly_pro_features_enabled', false ),
			'pro_cta'             => apply_filters(
				'bricksfly_pro_cta',
				array(
					'label' => __( 'Get BricksFly Pro', 'bricksfly-elements-for-bricks' ),
					'url'   => 'https://bricksfly.com/pricing/',
				)
			),

		);
		wp_localize_script('bricksfly-admin', 'BRICKSFLY_ADDONS_ADMIN', $localize_data);

		//}
	}



	function bricksfly_dashboard_integrations_config($configs)
	{

		if (! isset($configs['integrations']['plugins']['elements'])) {
			return $configs;
		}

		$action    = '';
		$data_base = '';
		foreach ($configs['integrations']['plugins']['elements'] as &$plugin) {

			if (bricksfly_get_local_plugin_data($plugin['basename']) === false) {
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
		if ($screen && strpos($screen->id, '_page_bricksfly_addons_settings') !== false) {
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
				if ($screen && strpos($screen->id, '_page_bricksfly_addons_settings') !== false) {
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


		check_ajax_referer('bricksfly_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('you are not allowed to do this action', 'bricksfly-elements-for-bricks'));
		}

		if (! isset($_POST['fields'])) {
			return;
		}

		$actives       = $foundkeys = array();
		$option_name   = isset($_POST['settings']) ? sanitize_key(wp_unslash($_POST['settings'])) : '';
		$sanitize_data = sanitize_text_field(wp_unslash($_POST['fields']));
		$settings      = json_decode($sanitize_data, true);
		bricksfly_get_nested_active_config_keys($settings, $found, $actives);


		bricksfly_get_nested_config_keys($settings, $foundkeys, $updatedSettings);

		if ('bricksfly_save_widgets' === $option_name) {
			$updated = update_option('bricksfly_save_widgets', $updatedSettings);
		} elseif ('bricksfly_save_extensions' === $option_name) {
			$updated = update_option('bricksfly_save_extensions', $updatedSettings);
		} else {
			wp_send_json_error(esc_html__('Invalid settings type.', 'bricksfly-elements-for-bricks'), 400);
		}

		$return_message = array(
			'status' => $updated,
			'total'  => is_array($actives) ? count($actives) : 0,
		);
		wp_send_json($return_message);
	}

	public function notice_store()
	{

		check_ajax_referer('bricksfly_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('you are not allowed to do this action', 'bricksfly-elements-for-bricks'));
		}

		if (! isset($_POST['notice'])) {
			return;
		}

		$sanitize_data = sanitize_text_field(wp_unslash($_POST['notice']));
		update_option('bricksfly_notice_data', $sanitize_data);

		$return_message = array(
			'message' => esc_html__('Notice Updated', 'bricksfly-elements-for-bricks'),
		);
		wp_send_json($return_message);
	}

	public function get_notice()
	{

		check_ajax_referer('bricksfly_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('you are not allowed to do this action', 'bricksfly-elements-for-bricks'));
		}

		$return_message = array(
			'notice' => json_decode(get_option('bricksfly_notice_data')),
		);
		wp_send_json($return_message);
	}

	public function save_settings_dashboard()
	{

		check_ajax_referer('bricksfly_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('you are not allowed to do this action', 'bricksfly-elements-for-bricks'));
		}

		if (! isset($_POST['fields'])) {
			return;
		}

		$sanitize_data = sanitize_text_field(wp_unslash($_POST['fields']));
		$settings      = json_decode($sanitize_data, true);
		$actives       = get_option('bricksfly_save_widgets');
		if (! is_array($actives)) {
			$actives = array();
		}

		// Merge the incoming payload into the stored map. Every slug from the
		// frontend is recorded as true/false; existing keys not in the payload
		// are left alone so partial toggles don't drop other items.
		if (is_array($settings)) {
			foreach ($settings as $slug => $item) {
				$is_active = ! empty($item['is_active']);

				$actives[$slug] = $is_active;
			}
		}

		$updated  = update_option('bricksfly_save_widgets', $actives);
		$elements = get_option('bricksfly_save_widgets');

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

		check_ajax_referer('bricksfly_admin_nonce', 'nonce');

		if (! current_user_can('manage_options')) {
			wp_send_json_error(esc_html__('you are not allowed to do this action', 'bricksfly-elements-for-bricks'));
		}

		$raw_settings = isset($_POST['smooth']) ? sanitize_text_field(wp_unslash($_POST['smooth'])) : '';
		if (! is_string($raw_settings) || '' === trim($raw_settings)) {
			wp_send_json_error(esc_html__('Smooth scroller settings are required.', 'bricksfly-elements-for-bricks'), 400);
		}

		$settings = sanitize_text_field(wp_unslash($_POST['smooth']));

		$decode = json_decode($settings);
		$option = wp_json_encode($decode);

		// update new settings
		if (! empty($_POST['smooth'])) {

			update_option('bricksfly_smooth_scroller', $option);
			wp_send_json($option);
		}
	}

}

BRICKSFLY_Admin_Init::instance();
