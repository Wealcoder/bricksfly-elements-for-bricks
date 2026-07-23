<?php

namespace wealcoder\bricksfly\Admin\Base;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class OneClickImport {

	public $file_path = 'thebrbre_tpl_file.xml';
	private static $instance;
	public $importer;

	public $import_files;
	public $log_file_path;

	private $selected_index;
	private $selected_import_files;

	public $frontend_error_messages = array();
	private $before_import_executed = false;
	private $plugin_page_setup = array();
	private $imported_terms = array();

	/** Stable batch id for the current page-import run (see save_wp_page_import_track). */
	private $page_import_batch_id = '';

	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	protected function __construct() {
		add_action( 'wp_ajax_thebrbre_upload_manual_import_file', [ $this, 'import_demo_data_ajax_callback' ] );
		add_action( 'admin_init', [ $this, 'setup_st_importer' ] );
		add_action( 'admin_init', [ $this, 'migrate_import_tracking_keys' ], 5 );
		add_action( 'set_object_terms', array( $this, 'add_imported_terms' ), 10, 6 );
		add_filter( 'thebrbre_importer.pre_process.post', [ $this, 'skip_failed_attachment_import' ] );
		add_action( 'thebrbre_importer.process_failed.post', [ $this, 'handle_failed_attachment_import' ], 10, 5 );
		add_action( 'wp_import_insert_post', [ $this, 'save_wp_navigation_import_mapping' ], 10, 4 );
		add_action( 'wp_import_insert_post', [ $this, 'save_wp_page_import_track' ], 10, 4 );
		add_action('thebrbre_import_existing_post', [ $this, 'save_wp_page_import_track' ], 10, 4 );
		add_action('thebrbre/after_import', [ $this, 'fix_imported_wp_navigation' ] );
		add_action( 'wp_ajax_thebrbre_get_latest_imported_pages', [ $this, 'thebrbre_get_latest_imported_pages' ] );
	}

	/**
	 * Migrate import tracking data written by releases that used the old prefix.
	 */
	public function migrate_import_tracking_keys() {
		if ( ! current_user_can( 'edit_pages' ) || get_option( 'thebrbre_import_tracking_migrated' ) ) {
			return;
		}

		$legacy_batch_id = get_option( 'aae_last_import_batch' );
		if ( $legacy_batch_id && ! get_option( 'thebrbre_last_import_batch' ) ) {
			update_option( 'thebrbre_last_import_batch', $legacy_batch_id, false );
		}
		delete_option( 'aae_last_import_batch' );

		global $wpdb;
		$wpdb->update(
			$wpdb->postmeta,
			[ 'meta_key' => 'thebrbre_import_batch' ],
			[ 'meta_key' => 'aae_import_batch' ],
			[ '%s' ],
			[ '%s' ]
		);
		$wpdb->update(
			$wpdb->postmeta,
			[ 'meta_key' => 'thebrbre_imported' ],
			[ 'meta_key' => 'aae_imported' ],
			[ '%s' ],
			[ '%s' ]
		);

		update_option( 'thebrbre_import_tracking_migrated', 1, false );
	}

	private function __clone() {}
	public function __wakeup() {}

	public function save_wp_page_import_track( $post_id, $original_id, $postdata, $data ) {
		// wp_insert_post() can return a WP_Error (and the WXR importer fires this
		// hook before its own is_wp_error() check). Bail on any non-positive /
		// error id, otherwise we'd point 'thebrbre_last_import_batch' at a batch that
		// has no real post — which makes "Go to page" resolve to nothing and the
		// latest imported page never show.
		if ( is_wp_error( $post_id ) || ! ( (int) $post_id > 0 ) ) {
			return;
		}

		if ( isset( $postdata['post_type'] ) && $postdata['post_type'] === 'page' ) {
			// One batch id for the whole import run, computed once per request so
			// every page imported together shares it (gmdate() alone would split
			// a multi-second import across batches). The option is updated only
			// for valid pages, so it always references a real, imported page.
			if ( empty( $this->page_import_batch_id ) ) {
				$this->page_import_batch_id = 'thebrbre_' . gmdate( 'Ymd_His' ) . '_' . wp_generate_password( 6, false );
			}
			$batch_id = $this->page_import_batch_id;

			update_option( 'thebrbre_last_import_batch', $batch_id );
			add_post_meta( $post_id, 'thebrbre_import_batch', $batch_id, true );
			add_post_meta( $post_id, 'thebrbre_imported', 1, true );
		}
	}

	function thebrbre_get_latest_imported_pages() {
		if (
			! isset( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'thebrbre_admin_nonce' )
		) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid or missing nonce', 'the-bricksfly' ) ], 403 );
		}

		if ( ! current_user_can( 'edit_pages' ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You are not allowed to perform this action.', 'the-bricksfly' ) ], 403 );
		}

		$this->migrate_import_tracking_keys();

		$per_page = isset( $_POST['per_page'] ) ? max( 1, (int) $_POST['per_page'] ) : 1;
		$batch_id = get_option( 'thebrbre_last_import_batch' );

		// Order by ID (insertion order), NOT date: imported pages keep the
		// template's original post_date, so "date DESC" surfaces the wrong page.
		// ID DESC reliably returns the most recently created (= just-imported) one.
		$base_args = [
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'orderby'        => 'ID',
			'order'          => 'DESC',
			'no_found_rows'  => true,
			'fields'         => 'ids',
		];

		$post_ids = [];

		// 1) Preferred: the pages from the most recent import batch.
		if ( $batch_id ) {
			$q = new \WP_Query( $base_args + [
				'meta_query' => [
					[
						'key'     => 'thebrbre_import_batch',
						'value'   => $batch_id,
						'compare' => '=',
					],
				],
			] );
			$post_ids = $q->posts;
		}

		// 2) Fallback: any imported page. Covers a stale/empty batch option so
		//    "Go to page" and the latest-page view still resolve to a real page.
		if ( empty( $post_ids ) ) {
			$q = new \WP_Query( $base_args + [
				'meta_query' => [
					[
						'key'     => 'thebrbre_imported',
						'value'   => 1,
						'compare' => '=',
					],
				],
			] );
			$post_ids = $q->posts;
		}

		if ( empty( $post_ids ) ) {
			wp_send_json_success( [
				'batch_id' => $batch_id,
				'pages'    => [],
			] );
		}

		$pages = array_map( function ( $id ) {
			return [
				'id'        => $id,
				'title'     => get_the_title( $id ),
				'permalink' => get_permalink( $id ),
				'date'      => get_post_time( 'c', true, $id ),
			];
		}, $post_ids );

		wp_send_json_success( [
			'batch_id' => $batch_id,
			'pages'    => $pages,
		] );
	}

	public function import_demo_data_ajax_callback() {
		ini_set( 'memory_limit', Helpers::apply_filters('thebrbre/st/import_memory_limit', '1024M' ) );

		Helpers::verify_ajax_call();

		// License limitation gate. This callback performs the actual content
		// download + import for both page import (import_type=page →
		// `starter_page_import`) and full-demo / starter template import
		// (→ `starter_tpl_import`). Enforced server-side so a forged request
		// cannot bypass the UI lock. `guard_import_feature()` halts with a
		// `limited:true` JSON envelope when the feature isn't in the plan.
		if ( class_exists( '\wealcoder\bricksfly\Admin\Pages\THEBRBRE_Template_Importer' ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce is verified above by Helpers::verify_ajax_call().
			$import_type = isset( $_POST['import_type'] ) ? sanitize_text_field( wp_unslash( $_POST['import_type'] ) ) : 'full-demo';
			$feature     = ( 'page' === $import_type ) ? 'starter_page_import' : 'starter_tpl_import';
			\wealcoder\bricksfly\Admin\Pages\THEBRBRE_Template_Importer::guard_import_feature( $feature );
		}

		$use_existing_importer_data = $this->use_existing_importer_data();

		if ( ! $use_existing_importer_data ) {
			Helpers::set_demo_import_start_time();
			$this->log_file_path  = Helpers::get_log_path();
			$this->selected_index = 0;
			$template_data        = [];

			check_ajax_referer( 'thebrbre_admin_nonce', 'nonce' );
			if ( isset( $_POST['template_data'] ) ) {
				$json_data     = sanitize_text_field( wp_unslash( $_POST['template_data'] ) );
				$template_data = json_decode( $json_data, true );

				if ( json_last_error() === JSON_ERROR_NONE ) {
					array_walk_recursive( $template_data, function ( &$value ) {
						if ( is_string( $value ) ) {
							$value = sanitize_text_field( $value );
						}
					} );
				}
			}

			if ( isset( $template_data['file']['content_url'] ) ) {
				$file_path                   = $template_data['file']['content_url'];
				$this->selected_import_files = Helpers::download_import_files( [ 'import_file_url' => $file_path ] );

				if ( is_wp_error( $this->selected_import_files ) ) {
					Helpers::log_error_and_send_ajax_response(
						$this->selected_import_files->get_error_message(),
						$this->log_file_path,
						esc_html__( 'Downloaded files', 'the-bricksfly' )
					);
				}
			} else {
				$response                   = [];
				$template_data['next_step'] = 'fail';
				$response['msg']            = esc_html__( 'No import files specified!', 'the-bricksfly' );
				$response['progress']       = 0;
				$response['template']       = wp_unslash( $template_data );
				wp_send_json( $response );
			}
		}

		Helpers::set_st_import_data_transient( $this->get_current_importer_data() );

		if ( ! $this->before_import_executed ) {
			$this->before_import_executed = true;
			Helpers::do_action('thebrbre/before_content_import_execution', $this->selected_import_files, $this->import_files, $this->selected_index );
		}

		if ( ! empty( $this->selected_import_files['content'] ) ) {
			$this->append_to_frontend_error_messages( $this->importer->import_content( $this->selected_import_files['content'] ) );
		}

		Helpers::do_action('thebrbre/after_content_import_execution', $this->selected_import_files, $this->import_files, $this->selected_index );

		Helpers::set_st_import_data_transient( $this->get_current_importer_data() );

		if ( false !== Helpers::has_action('thebrbre/after_all_import_execution' ) ) {
			wp_send_json( array( 'status' => 'afterAllImportAJAX' ) );
		}

		$this->update_terms_count();
		$this->final_response();
	}

	public function after_all_import_data_ajax_callback() {
		Helpers::verify_ajax_call();
		if ( $this->use_existing_importer_data() ) {
			Helpers::do_action('thebrbre/after_all_import_execution', $this->selected_import_files, $this->import_files, $this->selected_index );
		}
		$this->update_terms_count();
		$this->final_response();
	}

	private function final_response() {
		delete_transient( 'thebrbre_st_importer_data' );
		delete_transient( 'thebrbre_st_mporter_data_failed_attachment_imports' );
		delete_transient( 'thebrbre_import_menu_mapping' );
		delete_transient( 'thebrbre_import_posts_with_nav_block' );

		$response['msg']      = esc_html__( 'Congrats, your demo has been imported.', 'the-bricksfly' );
		$response['progress'] = 80;

		check_ajax_referer( 'thebrbre_admin_nonce', 'nonce' );
		if ( isset( $_POST['template_data'] ) ) {
			if ( isset( $template_data['local_path'] ) ) {
				unset( $template_data['local_path'] );
			}
			$json_data                  = sanitize_text_field( wp_unslash( $_POST['template_data'] ) );
			$template_data              = json_decode( $json_data, true );
			$template_data['next_step'] = 'check-theme';
			$response['template']       = wp_unslash( $template_data );
		}

		wp_send_json( $response );
	}

	private function use_existing_importer_data() {
		if ( $data = get_transient( 'thebrbre_st_importer_data' ) ) {
			$this->frontend_error_messages = empty( $data['frontend_error_messages'] ) ? array() : $data['frontend_error_messages'];
			$this->log_file_path           = empty( $data['log_file_path'] ) ? '' : $data['log_file_path'];
			$this->selected_index          = empty( $data['selected_index'] ) ? 0 : $data['selected_index'];
			$this->selected_import_files   = empty( $data['selected_import_files'] ) ? array() : $data['selected_import_files'];
			$this->import_files            = empty( $data['import_files'] ) ? array() : $data['import_files'];
			$this->before_import_executed  = empty( $data['before_import_executed'] ) ? false : $data['before_import_executed'];
			$this->imported_terms          = empty( $data['imported_terms'] ) ? [] : $data['imported_terms'];
			$this->importer->set_importer_data( $data );
			return true;
		}
		return false;
	}

	public function get_current_importer_data() {
		return array(
			'frontend_error_messages' => $this->frontend_error_messages,
			'log_file_path'           => $this->log_file_path,
			'selected_index'          => $this->selected_index,
			'selected_import_files'   => $this->selected_import_files,
			'import_files'            => $this->import_files,
			'before_import_executed'  => $this->before_import_executed,
			'imported_terms'          => $this->imported_terms,
		);
	}

	public function get_log_file_path() {
		return $this->log_file_path;
	}

	public function append_to_frontend_error_messages( $text ) {
		$lines = array();
		if ( ! empty( $text ) ) {
			$text  = str_replace( '<br>', PHP_EOL, $text );
			$lines = explode( PHP_EOL, $text );
		}
		foreach ( $lines as $line ) {
			if ( ! empty( $line ) && ! in_array( $line, $this->frontend_error_messages ) ) {
				$this->frontend_error_messages[] = $line;
			}
		}
	}

	public function frontend_error_messages_display() {
		$output = '';
		if ( ! empty( $this->frontend_error_messages ) ) {
			foreach ( $this->frontend_error_messages as $line ) {
				$output .= esc_html( $line );
				$output .= '<br>';
			}
		}
		return $output;
	}

	public function setup_st_importer() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification happens at the AJAX callback level; this runs on admin_init for importer setup.
		// Reject every POST request that does not carry a valid importer nonce.
		if ( ! empty( $_POST ) ) {
			$nonce = isset( $_POST['nonce'] )
				? sanitize_text_field( wp_unslash( $_POST['nonce'] ) )
				: '';

			if ( ! wp_verify_nonce( $nonce, 'thebrbre_admin_nonce' ) ) {
				return;
			}
		}
		$this->import_files = array();
		$attachment_status  = array_key_exists( 'attachment', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['attachment'] ) ) : true;

		$importer_options = array(
			'fetch_attachments' => $attachment_status,
		);

		$logger_options = array(
			'logger_min_level' => 'warning',
		);

		$logger            = new Logger();
		$logger->min_level = $logger_options['logger_min_level'];

		$this->importer = new Importer( $importer_options, $logger );
	}

	public function add_imported_terms( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
		if ( ! isset( $this->imported_terms[ $taxonomy ] ) ) {
			$this->imported_terms[ $taxonomy ] = array();
		}
		$this->imported_terms[ $taxonomy ] = array_unique( array_merge( $this->imported_terms[ $taxonomy ], $tt_ids ) );
	}

	public function skip_failed_attachment_import( $data ) {
		if (
			! empty( $data ) &&
			! empty( $data['post_type'] ) &&
			$data['post_type'] === 'attachment' &&
			! empty( $data['attachment_url'] )
		) {
			$failed_media_imports = Helpers::get_failed_attachment_imports();
			if ( ! empty( $failed_media_imports ) && in_array( $data['attachment_url'], $failed_media_imports, true ) ) {
				return [];
			}
		}
		return $data;
	}

	public function handle_failed_attachment_import( $post_id, $data, $meta, $comments, $terms ) {
		if ( empty( $data ) || empty( $data['post_type'] ) || $data['post_type'] !== 'attachment' ) {
			return;
		}
		Helpers::set_failed_attachment_import( $data['attachment_url'] );
	}

	public function save_wp_navigation_import_mapping( $post_id, $original_id, $postdata, $data ) {
		if ( empty( $postdata['post_content'] ) ) {
			return;
		}

		if ( $postdata['post_type'] !== 'wp_navigation' ) {
			if ( ! empty( $postdata['post_content'] ) && strpos( $postdata['post_content'], '<!-- wp:navigation' ) !== false ) {
				$wcfio_post_nav_block = get_transient( 'thebrbre_import_posts_with_nav_block' );
				if ( empty( $wcfio_post_nav_block ) ) {
					$wcfio_post_nav_block = [];
				}
				$wcfio_post_nav_block[] = $post_id;
				set_transient( 'thebrbre_import_posts_with_nav_block', $wcfio_post_nav_block, HOUR_IN_SECONDS );
			}
		} else {
			$wcfio_menu_mapping = get_transient( 'thebrbre_import_menu_mapping' );
			if ( empty( $wcfio_menu_mapping ) ) {
				$wcfio_menu_mapping = [];
			}
			$wcfio_menu_mapping[] = [
				'original_menu_id' => $original_id,
				'new_menu_id'      => $post_id,
			];
			set_transient( 'thebrbre_import_menu_mapping', $wcfio_menu_mapping, HOUR_IN_SECONDS );
		}
	}

	public function fix_imported_wp_navigation() {
		$nav_import_mapping = get_transient( 'thebrbre_import_menu_mapping' );
		$posts_nav_block    = get_transient( 'thebrbre_import_posts_with_nav_block' );

		if ( empty( $nav_import_mapping ) || empty( $posts_nav_block ) ) {
			return;
		}

		$replace_pairs = [];
		foreach ( $nav_import_mapping as $mapping ) {
			$replace_pairs[ '<!-- wp:navigation {"ref":' . $mapping['original_menu_id'] . '} /-->' ] = '<!-- wp:navigation {"ref":' . $mapping['new_menu_id'] . '} /-->';
		}

		foreach ( $posts_nav_block as $post_id ) {
			$post_nav_block = get_post( $post_id );
			if ( empty( $post_nav_block ) || empty( $post_nav_block->post_content ) ) {
				return;
			}
			wp_update_post( [
				'ID'           => $post_id,
				'post_content' => strtr( $post_nav_block->post_content, $replace_pairs ),
			] );
		}
	}

	private function update_terms_count() {
		foreach ( $this->imported_terms as $tax => $terms ) {
			wp_update_term_count_now( $terms, $tax );
		}
	}
}
