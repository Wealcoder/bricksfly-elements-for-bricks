<?php

namespace AABAddons\Admin;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class AABAddon_Row_Actions {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_filter( 'plugin_action_links', [ $this, 'add_plugin_link' ], 10, 2 );
		add_filter( 'plugin_row_meta', [ $this, '_plugin_row_meta' ], 10, 2 );
		add_action( 'wp_ajax_aab_deactivate_feedback', [ $this, 'handle_deactivate_feedback' ] );
	}

	public function handle_deactivate_feedback() {
		if ( ! isset( $_POST['reason'] ) || ! isset( $_POST['other_text'] ) || ! isset( $_POST['nonce'] ) ) {
			wp_send_json_error( esc_html__( 'Missing parameters', 'the-bricksfly' ) );
		}
		$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'aab_deactivate_feedback_nonce' ) ) {
			wp_send_json_error( esc_html__( 'Invalid nonce', 'the-bricksfly' ) );
		}
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( esc_html__( 'Permission denied', 'the-bricksfly' ) );
		}

		$reason     = sanitize_text_field( wp_unslash( $_POST['reason'] ) );
		$other_text = sanitize_textarea_field( wp_unslash( $_POST['other_text'] ) );

		$feedback_data = array(
			'reason'         => $reason,
			'other_text'     => $other_text,
			'user_id'        => get_current_user_id(),
			'site_url'       => get_site_url(),
			'timestamp'      => current_time( 'mysql' ),
			'plugin_version' => AAB_ADDONS_VERSION,
		);

		$existing_feedback   = get_option( 'aab_deactivation_feedback', array() );
		$existing_feedback[] = $feedback_data;
		update_option( 'aab_deactivation_feedback', $existing_feedback );

		wp_send_json_success( esc_html__( 'Feedback submitted successfully', 'the-bricksfly' ) );
	}

	function _plugin_row_meta( $meta, $plugin_file ) {
		if ( basename( AAB_ADDONS_BASE ) !== basename( $plugin_file ) ) {
			return $meta;
		}

		$meta[] = '<a href="https://bricksfly.com/docs/" target="_blank">' . esc_html__( 'Documentation', 'the-bricksfly' ) . '</a>';
		$meta[] = '<a href="#" target="_blank">' . esc_html__( 'Support', 'the-bricksfly' ) . '</a>';
		if ( ! file_exists( WP_PLUGIN_DIR . '/' . 'the-bricksfly-pro/the-bricksfly-pro.php' ) ) {
			$meta[] = '<a href="https://bricksfly.com" style="color:#ff7a00; font-weight: bold;" target="_blank">' . esc_html__( 'Upgrade to Pro', 'the-bricksfly' ) . '</a>';
		}
		$meta[] = '<a href="https://wordpress.org/support/plugin/the-bricksfly/reviews/#new-post" target="_blank">' . esc_html__( ' Rate the plugin', 'the-bricksfly' ) . '</a>';
		return $meta;
	}

	function add_plugin_link( $plugin_actions, $plugin_file ) {
		$new_actions = array();
		if ( basename( AAB_ADDONS_BASE ) === basename( $plugin_file ) ) {
			$new_actions['aab-dsb-settings'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( admin_url( 'admin.php?page=bf_addons_settings' ) ),
				esc_html__( 'Settings', 'the-bricksfly' )
			);
		}
		return array_merge( $new_actions, $plugin_actions );
	}
}

new AABAddon_Row_Actions();
