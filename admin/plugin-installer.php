<?php

namespace wealcoder\bricksfly\Admin;

if (! defined('ABSPATH')) {
	exit();
}

/**
 * AJAX endpoints behind the dashboard's "Activate / Deactivate BricksFly Pro"
 * buttons (ProPluginButton, IntegrationCard).
 *
 * Only the BricksFly Pro add-on can be switched here, and only when it is
 * already installed and the user clicks the button. Any other plugin is refused.
 */
class BRICKSFLY_Plugin_Installer
{

	const PRO_BASENAME = 'bricksfly-elements-for-bricks-pro/bricksfly-elements-for-bricks-pro.php';

	public function __construct()
	{
		add_action('wp_ajax_bricksfly_active_plugin', [$this, 'ajax_activate_plugin']);
		add_action('wp_ajax_bricksfly_deactive_plugin', [$this, 'ajax_deactivate_plugin']);
	}

	/**
	 * Basename posted by the dashboard, if it is one this endpoint may switch.
	 *
	 * @return string Basename, or '' when not allowed.
	 */
	private function requested_basename()
	{
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Callers verify the nonce first.
		$basename = isset($_POST['action_base']) ? sanitize_text_field(wp_unslash($_POST['action_base'])) : '';

		if (self::PRO_BASENAME !== $basename) {
			return '';
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$installed = get_plugins();

		return isset($installed[$basename]) ? $basename : '';
	}

	public function ajax_activate_plugin()
	{

		check_ajax_referer('bricksfly_admin_nonce', 'nonce');

		if (! current_user_can('activate_plugins')) {
			wp_send_json_error(__('You are not allowed to do this action', 'bricksfly-elements-for-bricks'));
		}

		$basename = $this->requested_basename();

		if ('' === $basename) {
			wp_send_json_error(__('This plugin cannot be activated from here.', 'bricksfly-elements-for-bricks'));
		}

		$result = activate_plugin($basename, '', false, true);

		if (is_wp_error($result)) {
			wp_send_json_error($result->get_error_message());
		}

		wp_send_json_success(['message' => __('Plugin activated successfully!', 'bricksfly-elements-for-bricks')]);
	}

	public function ajax_deactivate_plugin()
	{
		check_ajax_referer('bricksfly_admin_nonce', 'nonce');

		if (! current_user_can('activate_plugins')) {
			wp_send_json_error(__('You are not allowed to do this action', 'bricksfly-elements-for-bricks'));
		}

		$basename = $this->requested_basename();

		if ('' === $basename) {
			wp_send_json_error(__('This plugin cannot be deactivated from here.', 'bricksfly-elements-for-bricks'));
		}

		deactivate_plugins([$basename], true);

		wp_send_json_success(__('Plugin deactivated successfully!', 'bricksfly-elements-for-bricks'));
	}
}

new BRICKSFLY_Plugin_Installer();
