<?php

namespace wealcoder\bricksfly\Admin\Base;

defined( 'ABSPATH' ) || die();

class Helpers {

	public static $demo_import_start_time = '';

	public static function validate_import_file_info( $import_files ) {
		$filtered_import_file_info = array();

		foreach ( $import_files as $import_file ) {
			if ( self::is_import_file_info_format_correct( $import_file ) ) {
				$filtered_import_file_info[] = $import_file;
			}
		}

		return $filtered_import_file_info;
	}

	private static function is_import_file_info_format_correct( $import_file_info ) {
		if ( empty( $import_file_info['import_file_name'] ) ) {
			return false;
		}
		return true;
	}

	public static function download_import_files( $import_file_info ) {
		$downloaded_files = array(
			'content' => '',
		);

		$downloader = new Downloader();

		$import_file_info = self::apply_filters( 'aabaddons/pre_download_import_files', $import_file_info );

		if ( empty( $import_file_info['import_file_url'] ) ) {
			if ( file_exists( $import_file_info['local_import_file'] ) ) {
				$downloaded_files['content'] = $import_file_info['local_import_file'];
			}
		} else {
			$content_filename = self::apply_filters( 'aabaddons/downloaded_content_file_prefix', 'demo-content-import-file_' ) . self::$demo_import_start_time . self::apply_filters( 'aabaddons/downloaded_content_file_suffix_and_file_extension', '.xml' );

			$downloaded_files['content'] = $downloader->download_file( $import_file_info['import_file_url'], $content_filename );

			if ( is_wp_error( $downloaded_files['content'] ) ) {
				return $downloaded_files['content'];
			}
		}

		return $downloaded_files;
	}

	public static function write_to_file( $content, $file_path ) {
		$verified_credentials = self::check_wp_filesystem_credentials();

		if ( is_wp_error( $verified_credentials ) ) {
			return $verified_credentials;
		}

		global $wp_filesystem;

		if ( ! $wp_filesystem->put_contents( $file_path, $content ) ) {
			return new \WP_Error(
				'failed_writing_file_to_server',
				sprintf(
					/* translators: 1: line break, 2: full file path the plugin tried to write to. */
					__( 'An error occurred while writing file to your server! Tried to write a file to: %1$s%2$s.', 'the-bricksfly' ),
					'<br>',
					$file_path
				)
			);
		}

		return $file_path;
	}

	public static function append_to_file( $content, $file_path, $separator_text = '' ) {
		$verified_credentials = self::check_wp_filesystem_credentials();

		if ( is_wp_error( $verified_credentials ) ) {
			return $verified_credentials;
		}
		update_option( 'aab_template_import_state', $content );

		global $wp_filesystem;

		$existing_data = '';
		if ( file_exists( $file_path ) ) {
			$existing_data = $wp_filesystem->get_contents( $file_path );
		}

		$separator = PHP_EOL . '---' . $separator_text . '---' . PHP_EOL;

		if ( ! $wp_filesystem->put_contents( $file_path, $existing_data . $separator . $content . PHP_EOL ) ) {
			return new \WP_Error(
				'failed_writing_file_to_server',
				sprintf(
					/* translators: 1: line break, 2: full file path the plugin tried to write to. */
					__( 'An error occurred while writing file to your server! Tried to write a file to: %1$s%2$s.', 'the-bricksfly' ),
					'<br>',
					$file_path
				)
			);
		}

		return true;
	}

	public static function data_from_file( $file_path ) {
		$verified_credentials = self::check_wp_filesystem_credentials();

		if ( is_wp_error( $verified_credentials ) ) {
			return $verified_credentials;
		}

		global $wp_filesystem;

		$data = $wp_filesystem->get_contents( $file_path );

		if ( ! $data ) {
			return new \WP_Error(
				'failed_reading_file_from_server',
				sprintf(
					/* translators: 1: line break, 2: full file path the plugin tried to read from. */
					__( 'An error occurred while reading a file from your server! Tried reading file from path: %1$s%2$s.', 'the-bricksfly' ),
					'<br>',
					$file_path
				)
			);
		}

		return $data;
	}

	public static function get_plugin_page_setup_data() {
		return Helpers::apply_filters( 'aabaddons/plugin_page_setup', array(
			'parent_slug' => 'bf_addons_settings',
			'capability'  => 'import',
			'menu_slug'   => 'bf_addons_settings',
		) );
	}

	private static function check_wp_filesystem_credentials() {
		if ( ! ( 'direct' === get_filesystem_method() ) ) {
			return new \WP_Error(
				'no_direct_file_access',
				sprintf(
					/* translators: 1: opening <strong> tag, 2: closing </strong> tag, 3: link to instructions on enabling the direct filesystem method. */
					__( 'This WordPress page does not have %1$sdirect%2$s write file access. This plugin needs it in order to save the demo import xml file to the upload directory of your site. You can change this setting with these instructions: %3$s.', 'the-bricksfly' ),
					'<strong>',
					'</strong>',
					sprintf(
						/* translators: 1: opening <strong> tag, 2: closing </strong> tag (around the word "direct"). */
						'<a href="http://gregorcapuder.com/wordpress-how-to-set-direct-filesystem-method/" target="_blank">' . esc_html__( 'How to set %1$sdirect%2$s filesystem method', 'the-bricksfly' ) . '</a>',
						'<strong>',
						'</strong>'
					)
				)
			);
		}

		$plugin_page_setup = self::get_plugin_page_setup_data();

		$demo_import_page_url = wp_nonce_url( $plugin_page_setup['parent_slug'] . '?page=' . $plugin_page_setup['menu_slug'], $plugin_page_setup['menu_slug'] );

		if ( false === ( $creds = request_filesystem_credentials( $demo_import_page_url, '', false, false, null ) ) ) {
			return new \WP_error(
				'filesystem_credentials_could_not_be_retrieved',
				__( 'An error occurred while retrieving reading/writing permissions to your server (could not retrieve WP filesystem credentials)!', 'the-bricksfly' )
			);
		}

		if ( ! WP_Filesystem( $creds ) ) {
			return new \WP_Error(
				'wrong_login_credentials',
				__( 'Your WordPress login credentials don\'t allow to use WP_Filesystem!', 'the-bricksfly' )
			);
		}

		return true;
	}

	public static function get_log_path() {
		$upload_dir  = wp_upload_dir();
		$upload_path = self::apply_filters( 'aabaddons/upload_file_path', trailingslashit( $upload_dir['path'] ) );

		$log_path = $upload_path . self::apply_filters( 'aabaddons/log_file_prefix', 'log_file_' ) . self::$demo_import_start_time . self::apply_filters( 'aabaddons/log_file_suffix_and_file_extension', '.txt' );

		self::register_file_as_media_attachment( $log_path );

		return $log_path;
	}

	public static function register_file_as_media_attachment( $log_path ) {
		$log_mimes = array( 'txt' => 'text/plain' );
		$filetype  = wp_check_filetype( basename( $log_path ), self::apply_filters( 'aabaddons/file_mimes', $log_mimes ) );

		$attachment = array(
			'guid'           => self::get_log_url( $log_path ),
			'post_mime_type' => $filetype['type'],
			'post_title'     => self::apply_filters( 'aabaddons/attachment_prefix', esc_html__( 'Starter Template Import - ', 'the-bricksfly' ) ) . preg_replace( '/\.[^.]+$/', '', basename( $log_path ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attach_id = wp_insert_attachment( $attachment, $log_path );
	}

	public static function get_log_url( $log_path ) {
		$upload_dir = wp_upload_dir();
		$upload_url = self::apply_filters( 'aabaddons/upload_file_url', trailingslashit( $upload_dir['url'] ) );

		return $upload_url . basename( $log_path );
	}

	public static function verify_ajax_call() {
		check_ajax_referer( 'aab_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'import' ) ) {
			wp_die(
				esc_html__( 'Your user role isn\'t high enough. You don\'t have permission to import demo data.', 'the-bricksfly' )
			);
		}
	}

	public static function process_uploaded_files( $uploaded_files, $log_file_path ) {
		$selected_import_files = array(
			'content' => '',
			'widgets' => '',
		);

		$upload_overrides = array(
			'test_form' => false,
		);

		add_filter( 'upload_mimes', function ( $defaults ) {
			$custom = [
				'xml'  => 'text/xml',
				'json' => 'application/json',
			];
			return array_merge( $custom, $defaults );
		} );

		$file_not_provided_error = array(
			'error' => esc_html__( 'No file provided.', 'the-bricksfly' ),
		);

		$content_file_info = isset( $uploaded_files['content_file'] ) ?
			wp_handle_upload( $uploaded_files['content_file'], $upload_overrides ) :
			$file_not_provided_error;

		if ( $content_file_info && ! isset( $content_file_info['error'] ) ) {
			$selected_import_files['content'] = $content_file_info['file'];
		} else {
			$log_added = self::append_to_file(
				sprintf(
					/* translators: %s: upload error message returned by wp_handle_upload(). */
					__( 'Content file was not uploaded. Error: %s', 'the-bricksfly' ),
					$content_file_info['error']
				),
				$log_file_path,
				esc_html__( 'Upload files', 'the-bricksfly' )
			);
		}

		$log_added = self::append_to_file(
			__( 'The import files were successfully uploaded!', 'the-bricksfly' ) . self::import_file_info( $selected_import_files ),
			$log_file_path,
			esc_html__( 'Upload files', 'the-bricksfly' )
		);

		return $selected_import_files;
	}

	public static function import_file_info( $selected_import_files ) {
		return PHP_EOL .
		sprintf(
			/* translators: %s: PHP `max_execution_time` value in seconds. */
			__( 'Initial max execution time = %s', 'the-bricksfly' ),
			ini_get( 'max_execution_time' )
		) . PHP_EOL .
		sprintf(
			/* translators: 1: line break (PHP_EOL), 2: site URL, 3: full path to the import data file (or a localized "not defined!" placeholder). */
			__( 'Files info:%1$sSite URL = %2$s%1$sData file = %3$s%1$s', 'the-bricksfly' ),
			PHP_EOL,
			get_site_url(),
			empty( $selected_import_files['content'] ) ? esc_html__( 'not defined!', 'the-bricksfly' ) : $selected_import_files['content'],
		);
	}

	public static function log_error_and_send_ajax_response( $error_text, $log_file_path, $separator = '' ) {
		$log_added = self::append_to_file(
			$error_text,
			$log_file_path,
			$separator
		);
		$template_data              = [];
		$response                   = [];
		$template_data['next_step'] = 'fail';
		$response['msg']            = $error_text;
		$response['progress']       = 0;
		$response['template']       = wp_unslash( $template_data );
		wp_send_json( $response );
	}

	public static function set_demo_import_start_time() {
		self::$demo_import_start_time = gmdate( self::apply_filters( 'aabaddons/date_format_for_file_names', 'Y-m-d__H-i-s' ) );
	}

	public static function set_st_import_data_transient( $data ) {
		set_transient( 'aab_st_importer_data', $data, 0.1 * HOUR_IN_SECONDS );
	}

	public static function apply_filters( $hook, $default_data ) {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- Central wrapper; callers provide prefixed hook names.
		$new_data = apply_filters( $hook, $default_data );
		return $new_data;
	}

	public static function do_action( $hook, ...$arg ) {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- Central wrapper; callers provide prefixed hook names.
		do_action( $hook, ...$arg );
	}

	public static function has_action( $hook, $function_to_check = false ) {
		if ( has_action( $hook ) ) {
			return has_action( $hook, $function_to_check );
		}

		return false;
	}

	public static function get_failed_attachment_imports() {
		return get_transient( 'aab_st_importer_data_failed_attachment_imports' );
	}

	public static function set_failed_attachment_import( $attachment_url ) {
		$failed_media_imports = self::get_failed_attachment_imports();

		if ( empty( $failed_media_imports ) || ! is_array( $failed_media_imports ) ) {
			$failed_media_imports = [];
		}

		$failed_media_imports[] = $attachment_url;

		set_transient( 'aab_st_importer_data_failed_attachment_imports', $failed_media_imports, HOUR_IN_SECONDS );
	}
}
