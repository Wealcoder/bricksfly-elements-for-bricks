<?php

namespace wealcoder\bricksfly\Admin\Base;

defined( 'ABSPATH' ) || die();

class Downloader {

	private $download_directory_path = '';

	public function __construct( $download_directory_path = '' ) {
		$this->set_download_directory_path( $download_directory_path );
	}

	public function download_file( $url, $filename ) {
		if ( empty( $url ) ) {
			return new \WP_Error(
				'missing_url',
				__( 'Missing URL for downloading a file!', 'the-bricksfly' )
			);
		}

		$destination = $this->download_directory_path . sanitize_file_name( $filename );
		$timeout     = max( 45, (int) Helpers::apply_filters('thebrbre/timeout_for_downloading_import_file', 180 ) );
		$attempts    = max( 1, min( 3, (int) Helpers::apply_filters('thebrbre/import_file_download_attempts', 2 ) ) );
		$response    = null;

		for ( $attempt = 1; $attempt <= $attempts; $attempt++ ) {
			if ( file_exists( $destination ) ) {
				wp_delete_file( $destination );
			}

			$response = wp_safe_remote_get(
				$url,
				array(
					'timeout'     => $timeout,
					'redirection' => 5,
					'stream'      => true,
					'filename'    => $destination,
				)
			);

			$response_code = is_wp_error( $response ) ? 0 : (int) wp_remote_retrieve_response_code( $response );
			if ( 200 === $response_code && file_exists( $destination ) && filesize( $destination ) > 0 ) {
				return $destination;
			}

			// Retry transport errors, timeouts, rate limiting, and server errors.
			if ( ! is_wp_error( $response ) && 429 !== $response_code && $response_code < 500 ) {
				break;
			}
		}

		if ( file_exists( $destination ) ) {
			wp_delete_file( $destination );
		}

		if ( ! is_wp_error( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
			$response = new \WP_Error( 'empty_download', __( 'The downloaded file is empty.', 'the-bricksfly' ) );
		}

		$response_error = $this->get_error_from_response( $response );
		$error_message  = $this->get_actionable_error_message( $response_error );

		return new \WP_Error(
			'download_error',
			$error_message . Helpers::apply_filters('thebrbre/message_after_file_fetching_error', '' )
		);
	}

	private function get_actionable_error_message( $response_error ) {
		$error_code    = sanitize_text_field( (string) $response_error['error_code'] );
		$error_message = sanitize_text_field( (string) $response_error['error_message'] );
		$is_timeout    = false !== stripos( $error_message, 'timed out' ) || false !== stripos( $error_message, 'cURL error 28' );

		if ( $is_timeout ) {
			return sprintf(
				/* translators: 1: technical error code, 2: technical error message. */
				__( 'The template file download timed out. Check this server\'s internet connection, then click Retry. If it happens again, ask your hosting provider to allow longer outbound HTTPS requests. Technical details: %1$s - %2$s.', 'the-bricksfly' ),
				$error_code,
				$error_message
			);
		}

		return sprintf(
			/* translators: 1: technical error code, 2: technical error message. */
			__( 'The template file could not be downloaded. Check this server\'s internet connection, then click Retry. If the problem continues, contact your hosting provider. Technical details: %1$s - %2$s.', 'the-bricksfly' ),
			$error_code,
			$error_message
		);
	}

	private function get_error_from_response( $response ) {
		$response_error = array();

		if ( is_array( $response ) ) {
			$response_error['error_code']    = $response['response']['code'];
			$response_error['error_message'] = $response['response']['message'];
		} else {
			$response_error['error_code']    = $response->get_error_code();
			$response_error['error_message'] = $response->get_error_message();
		}

		return $response_error;
	}

	public function get_download_directory_path() {
		return $this->download_directory_path;
	}

	public function set_download_directory_path( $download_directory_path ) {
		if ( file_exists( $download_directory_path ) ) {
			$this->download_directory_path = $download_directory_path;
		} else {
			$upload_dir = wp_upload_dir();
			$this->download_directory_path = Helpers::apply_filters('thebrbre/upload_file_path', trailingslashit( $upload_dir['path'] ) );
		}
	}
}
