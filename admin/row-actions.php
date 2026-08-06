<?php

namespace wealcoder\bricksfly\Admin;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class BRICKSFLY_Row_Actions {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_filter( 'plugin_action_links', [ $this, 'bricksfly_add_plugin_link' ], 10, 2 );
		add_filter( 'plugin_row_meta', [ $this, '_plugin_row_meta' ], 10, 2 );
		add_action( 'wp_ajax_bricksfly_deactivate_feedback', [ $this, 'handle_deactivate_feedback' ] );
	}

	public function handle_deactivate_feedback() {
		if ( ! isset( $_POST['reason'] ) || ! isset( $_POST['other_text'] ) || ! isset( $_POST['nonce'] ) ) {
			wp_send_json_error( esc_html__( 'Missing parameters', 'bricksfly-elements-for-bricks' ) );
		}
		$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'bricksfly_deactivate_feedback_nonce' ) ) {
			wp_send_json_error( esc_html__( 'Invalid nonce', 'bricksfly-elements-for-bricks' ) );
		}
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( esc_html__( 'Permission denied', 'bricksfly-elements-for-bricks' ) );
		}

		$reason     = sanitize_text_field( wp_unslash( $_POST['reason'] ) );
		$other_text = sanitize_textarea_field( wp_unslash( $_POST['other_text'] ) );

		$feedback_data = array(
			'reason'         => $reason,
			'other_text'     => $other_text,
			'user_id'        => get_current_user_id(),
			'site_url'       => get_site_url(),
			'timestamp'      => current_time( 'mysql' ),
			'plugin_version' => BRICKSFLY_VERSION,
		);

		$existing_feedback   = get_option( 'bricksfly_deactivation_feedback', array() );
		$existing_feedback[] = $feedback_data;
		update_option( 'bricksfly_deactivation_feedback', $existing_feedback );

		wp_send_json_success( esc_html__( 'Feedback submitted successfully', 'bricksfly-elements-for-bricks' ) );
	}

	function _plugin_row_meta( $meta, $plugin_file ) {
		if ( basename( BRICKSFLY_BASE ) !== basename( $plugin_file ) ) {
			return $meta;
		}

		$meta[] = '<a href="https://bricksfly.com/docs/" target="_blank">' . esc_html__( 'Documentation', 'bricksfly-elements-for-bricks' ) . '</a>';
		$meta[] = '<a href="#" target="_blank">' . esc_html__( 'Support', 'bricksfly-elements-for-bricks' ) . '</a>';
		if ( ! file_exists( WP_PLUGIN_DIR . '/' . 'bricksfly-elements-for-bricks-pro/bricksfly-elements-for-bricks-pro.php' ) ) {
			$meta[] = '<a href="https://bricksfly.com" style="color:#ff7a00; font-weight: bold;" target="_blank">' . esc_html__( 'Upgrade to Pro', 'bricksfly-elements-for-bricks' ) . '</a>';
		}
		$meta[] = '<a href="https://wordpress.org/support/plugin/the-bricksfly/reviews/#new-post" target="_blank">' . esc_html__( ' Rate the plugin', 'bricksfly-elements-for-bricks' ) . '</a>';
		return $meta;
	}

	function bricksfly_add_plugin_link( $plugin_actions, $plugin_file ) {
		$new_actions = array();
		if ( basename( BRICKSFLY_BASE ) === basename( $plugin_file ) ) {
			$new_actions['aab-dsb-settings'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( admin_url( 'admin.php?page=bricksfly_addons_settings' ) ),
				esc_html__( 'Settings', 'bricksfly-elements-for-bricks' )
			);
		}
		return array_merge( $new_actions, $plugin_actions );
	}
}

new BRICKSFLY_Row_Actions();
