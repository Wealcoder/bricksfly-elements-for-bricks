<?php

namespace wealcoder\thebricksfly\Admin\Notices;

defined( 'ABSPATH' ) || exit();

class ShowNotices {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	protected $messages = array();

	public function __construct() {
		add_action( 'admin_init', array( $this, 'load_messages' ), 1 );
		add_filter( 'wp_redirect', array( $this, 'save_messages' ), 1 );
		add_action( 'admin_notices', array( $this, 'display_messages' ) );
	}

	public function load_messages() {
		$flash = filter_input( INPUT_GET, '_flash', FILTER_VALIDATE_BOOLEAN );
		if ( true === $flash ) {
			$messages = get_option( 'thebrbre_notice_flash_messages', array() );
			if ( ! empty( $messages ) && is_array( $messages ) ) {
				foreach ( $messages as $message ) {
					$this->message( $message['type'], $message['message'] );
				}
			}
			update_option( 'thebrbre_notice_flash_messages', array() );
		}
	}

	public function save_messages( $location ) {
		if ( ! empty( $this->messages ) ) {
			update_option( 'thebrbre_notice_flash_messages', $this->messages );
			$location = add_query_arg( '_flash', 'yes', $location );
		}
		return $location;
	}

	public function display_messages() {
		if ( empty( $this->messages ) ) {
			return;
		}
		foreach ( $this->messages as $message_id => $message ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Literal format string; values escaped per-arg.
			printf( '<div class="notice notice-%1$s is-dismissible">%2$s</div>', esc_attr( $message['type'] ), wp_kses_post( wpautop( $message['message'] ) ) );
			unset( $this->messages[ $message_id ] );
		}
	}

	public function message( $type, $message ) {
		if ( empty( $message ) && ! in_array( $type, array( 'success', 'info', 'warning', 'error' ), true ) ) {
			return;
		}
		$id                    = substr( md5( $message . $type ), 0, 8 );
		$this->messages[ $id ] = array(
			'message' => $message,
			'type'    => $type,
		);
	}

	public function error( $message )   { $this->message( 'error', $message ); }
	public function warning( $message ) { $this->message( 'warning', $message ); }
	public function info( $message )    { $this->message( 'info', $message ); }
	public function success( $message ) { $this->message( 'success', $message ); }

	public function get_messages()   { return $this->messages; }

	public function clear_messages() {
		$this->messages = array();
		update_option( 'thebrbre_notice_flash_messages', array() );
	}
}

ShowNotices::instance();
