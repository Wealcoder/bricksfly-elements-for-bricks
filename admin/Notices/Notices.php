<?php

namespace wealcoder\bricksfly\Admin\Notices;

defined( 'ABSPATH' ) || exit();

class Notices {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	protected $notices = array();

	public function __construct() {
		add_action( 'admin_init', array( $this, 'add_admin_notices' ) );

		add_action( 'wp_ajax_bricksfly_notice_dismiss_notice', array( $this, 'ajax_dismiss_notice' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	public function add_admin_notices() {
		$installed_time = absint( get_option( 'bricksfly_installed' ) );
		$current_time   = absint( wp_date( 'U' ) );
		$plugin_file    = WP_PLUGIN_DIR . '/the-bricksfly-pro/the-bricksfly-pro.php';

		if ( ! file_exists( $plugin_file ) ) {
			// Add promotional notices here when needed.
		}
	}

	public function ajax_dismiss_notice() {
		if ( ! check_ajax_referer( 'bricksfly_notice_dismiss_notice', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
			exit;
		}
		$notice_id   = isset( $_POST['notice_id'] ) ? sanitize_text_field( wp_unslash( $_POST['notice_id'] ) ) : '';
		$snooze      = isset( $_POST['snooze'] ) ? filter_var( wp_unslash( $_POST['snooze'] ), FILTER_VALIDATE_BOOLEAN ) : false;
		$snooze_time = isset( $_POST['snooze_time'] ) ? absint( wp_unslash( $_POST['snooze_time'] ) ) : 7 * DAY_IN_SECONDS;
		$notice      = array_key_exists( $notice_id, $this->notices ) ? $this->notices[ $notice_id ] : null;
		if ( ! is_null( $notice ) ) {
			if ( $snooze ) {
				$this->snooze( $notice_id, $snooze_time );
			} else {
				$this->dismiss( $notice_id );
			}
			wp_cache_flush();
			wp_send_json_success();
			exit;
		}
		wp_send_json_error();
		exit;
	}

	public function admin_notices() {
		if ( empty( $this->notices ) ) {
			return;
		}

		foreach ( $this->notices as $notice ) {
			if ( $this->should_display( $notice ) ) {
				$classes = array_unique( array_filter( wp_parse_list( $notice['class'] ) ) );
				$style   = ! empty( $notice['style'] ) ? $notice['style'] : '';
				$message = $notice['message'];
				if ( '.php' === substr( $message, -4 ) ) {
					$path = wp_normalize_path( $message );
					if ( file_exists( $path ) ) {
						ob_start();
						include $path;
						$message = ob_get_clean();
					}
				}
				if ( $notice['dismissible'] ) {
					$classes[] = 'is-dismissible';
				}
				if ( empty( $message ) ) {
					continue;
				}
				if ( ! preg_match( '/<[^>]+>/', $message ) ) {
					$message = wpautop( $message );
				}

				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Literal format string; values escaped per-arg below.
				printf(
					'<div class="notice aab-notice notice-%1$s %2$s" data-notice_id="%3$s" data-nonce="%4$s" data-action="%5$s" style="%6$s">%7$s%8$s</div>',
					esc_attr( $notice['type'] ),
					esc_attr( implode( ' ', $classes ) ),
					esc_attr( $notice['notice_id'] ),
					esc_attr( wp_create_nonce( 'bricksfly_notice_dismiss_notice' ) ),
					esc_attr( 'bricksfly_notice_dismiss_notice' ),
					esc_attr( $style ),
					wp_kses_post( wptexturize( $message ) ),
					$notice['dismissible'] ? '<button type="button" class="notice-dismiss"><span class="screen-reader-text">' . esc_html__( 'Dismiss this notice', 'bricksfly-elements-for-bricks' ) . '</span></button>' : ''
				);
			}
		}
	}

	public function add( $args ) {
		if ( is_string( $args ) ) {
			$args = array( 'message' => $args );
		}
		$args = wp_parse_args(
			$args,
			array(
				'message'     => '',
				'type'        => 'info',
				'dismissible' => true,
				'capability'  => 'manage_options',
				'notice_id'   => '',
				'class'       => '',
				'style'       => '',
			)
		);
		if ( empty( $args['message'] ) ) {
			return;
		}
		if ( empty( $args['notice_id'] ) ) {
			$args['notice_id'] = 'bricksfly_notice_' . md5( $args['message'] . $args['type'] );
		}
		if ( true === filter_var( $args['dismissible'], FILTER_VALIDATE_BOOLEAN ) && $this->is_dismissed( $args['notice_id'] ) ) {
			return;
		}
		$this->notices[ $args['notice_id'] ] = $args;
	}

	public function get_notices() {
		return $this->notices;
	}

	public function is_dismissed( $id ) {
		if ( 'yes' === get_option( $id ) || 'yes' === get_option( '_transient_' . $id ) ) {
			return true;
		}
		return false;
	}

	public function should_display( $notice ) {
		if ( ( $notice['notice_id'] && $this->is_dismissed( $notice['notice_id'] ) ) || ( $notice['capability'] && ! current_user_can( $notice['capability'] ) ) ) {
			return false;
		}
		return true;
	}

	public function dismiss( $id ) {
		update_option( $id, 'yes' );
		delete_transient( $id );
	}

	public function snooze( $id, $time = null ) {
		set_transient( $id, 'yes', absint( $time ) );
	}
}

Notices::instance();
