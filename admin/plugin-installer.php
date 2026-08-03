<?php

namespace wealcoder\bricksfly\Admin;

if (! defined('ABSPATH')) {
	exit();
}

/**
 * AJAX endpoints for activating / deactivating other WordPress plugins from
 * the React dashboard (GetProButton, IntegrationCard, ProConfirmDialog).
 */
class BRICKSFLY_Plugin_Installer
{

	public function __construct()
	{
		add_action('wp_ajax_bricksfly_active_plugin', [$this, 'ajax_activate_plugin']);
		add_action('wp_ajax_bricksfly_deactive_plugin', [$this, 'ajax_deactivate_plugin']);
	}

	public function ajax_activate_plugin()
	{

		check_ajax_referer('bricksfly_admin_nonce', 'nonce');

		if (! current_user_can('activate_plugins')) {
			wp_send_json_error(__('You are not allowed to do this action', 'bricksfly-elements-for-bricks'));
		}

		$basename = isset($_POST['action_base']) ? sanitize_text_field(wp_unslash($_POST['action_base'])) : '';
		$result   = activate_plugin($basename, '', false, true);

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

		$basename = isset($_POST['action_base']) ? sanitize_text_field(wp_unslash($_POST['action_base'])) : '';
		$result   = deactivate_plugins([$basename], true);

		if (is_wp_error($result)) {
			wp_send_json_error($result->get_error_message());
		}

		wp_send_json_success(__('Plugin deactivated successfully!', 'bricksfly-elements-for-bricks'));
	}
}

new BRICKSFLY_Plugin_Installer();
