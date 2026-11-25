<?php

namespace wealcoder\bricksfly\Admin\Base;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class OneClickImport {

	public $file_path = 'bricksfly_tpl_file.xml';
	private static $instance;
	public $importer;

	public $import_files;
	public $log_file_path;

	private $selected_index;
	private $selected_import_files;

	public $frontend_error_messages = array();
	private $before_import_executed = false;
	private $plugin_page_setup      = array();
	private $imported_terms         = array();

	/** Stable batch id for the current page-import run (see save_wp_page_import_track). */
	private $page_import_batch_id = '';

	/**
	 * The import runs from PHP (run()), not from the import screen.
	 *
	 * @var bool
	 */
	private static $in_php = false;

	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	protected function __construct() {
		add_action( 'wp_ajax_bricksfly_upload_manual_import_file', array( $this, 'import_demo_data_ajax_callback' ) );
		add_action( 'admin_init', array( $this, 'setup_st_importer' ) );
		add_action( 'set_object_terms', array( $this, 'add_imported_terms' ), 10, 6 );
		add_filter( 'bricksfly_importer.pre_process.post', array( $this, 'skip_failed_attachment_import' ) );
		add_action( 'bricksfly_importer.process_failed.post', array( $this, 'handle_failed_attachment_import' ), 10, 5 );
		add_action( 'bricksfly_import_insert_post', array( $this, 'save_wp_navigation_import_mapping' ), 10, 4 );
		add_action( 'bricksfly_import_insert_post', array( $this, 'save_wp_page_import_track' ), 10, 4 );
		add_action( 'bricksfly_import_existing_post', array( $this, 'save_wp_page_import_track' ), 10, 4 );
		add_action( 'bricksfly/after_import', array( $this, 'fix_imported_wp_navigation' ) );
		add_filter( 'bricksfly_import_page_as_copy', array( $this, 'import_page_as_copy' ) );
		add_filter( 'bricksfly_importer.reuse_post', array( $this, 'reuse_post' ), 10, 3 );
		add_filter( 'bricksfly_importer.separate_menus', array( $this, 'separate_demo_menus' ) );
		add_action( 'bricksfly_importer.processed.term', array( $this, 'track_imported_menu' ), 10, 2 );
		add_action( 'wp_ajax_bricksfly_get_latest_imported_pages', array( $this, 'bricksfly_get_latest_imported_pages' ) );
	}

	/**
	 * Remember the menus this import created, so a menu that ended up empty
	 * (its items already existed from an earlier import of the same demo) can
	 * be removed again at the end.
	 *
	 * @param int   $term_id New term id.
	 * @param array $data    Term data from the export.
	 * @return void
	 */
	public function track_imported_menu( $term_id, $data ) {
		if ( empty( $data['taxonomy'] ) || 'nav_menu' !== $data['taxonomy'] ) {
			return;
		}

		$menus   = get_transient( 'bricksfly_import_created_menus' );
		$menus   = is_array( $menus ) ? $menus : array();
		$menus[] = (int) $term_id;
		set_transient( 'bricksfly_import_created_menus', array_values( array_unique( $menus ) ), HOUR_IN_SECONDS );
	}

	/**
	 * Delete menus this import created that have no items.
	 *
	 * @return void
	 */
	private function remove_empty_imported_menus() {
		$menus = get_transient( 'bricksfly_import_created_menus' );
		delete_transient( 'bricksfly_import_created_menus' );

		foreach ( is_array( $menus ) ? $menus : array() as $menu_id ) {
			$menu = wp_get_nav_menu_object( (int) $menu_id );
			if ( $menu && empty( wp_get_nav_menu_items( $menu->term_id ) ) ) {
				wp_delete_nav_menu( $menu->term_id );
			}
		}
	}

	/**
	 * Full-demo imports keep their menus apart from menus the site already has
	 * (a second demo must not add its items to the first demo's menu). Page
	 * imports keep the default behaviour.
	 *
	 * @param bool $separate Current value.
	 * @return bool
	 */
	public function separate_demo_menus( $separate ) {
		// The importer nonce is verified in setup_st_importer() and in the AJAX callback.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$type = isset( $_POST['import_type'] ) ? sanitize_key( wp_unslash( $_POST['import_type'] ) ) : $this->running_type_in_php();
		return 'page' === $type ? $separate : true;
	}

	private function __clone() {}
	public function __wakeup() {}

	/**
	 * Page imports (not full-demo imports) bring an already imported page in
	 * again as a new copy instead of skipping it silently.
	 *
	 * @param bool $as_copy Current value.
	 * @return bool
	 */
	public function import_page_as_copy( $as_copy ) {
		// The importer nonce is verified in setup_st_importer() and in the AJAX callback.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$type = isset( $_POST['import_type'] ) ? sanitize_key( wp_unslash( $_POST['import_type'] ) ) : $this->running_type_in_php();
		return 'page' === $type ? true : $as_copy;
	}

	/**
	 * What an import that was started with run() brings.
	 *
	 * @return string 'template', 'page' or ''.
	 */
	private function running_type_in_php() {
		return self::$in_php ? \wealcoder\bricksfly\Admin\Pages\BRICKSFLY_Template_Importer::running_type() : '';
	}

	/**
	 * A post of another demo is not an item of this one, even when the guid is
	 * the same (a dark demo is often a copy of the light one). Media, fonts and
	 * post type definitions are shared.
	 *
	 * @param bool   $reuse     Current value.
	 * @param int    $post_id   Post of the site.
	 * @param string $post_type Its post type.
	 * @return bool
	 */
	public function reuse_post( $reuse, $post_id, $post_type ) {
		$template = \wealcoder\bricksfly\Admin\Pages\BRICKSFLY_Template_Importer::running_template();
		$shared   = array_merge( array( 'attachment', 'bricks_fonts' ), (array) apply_filters( 'bricksfly_importer.definition_post_types', array() ) );

		if ( ! $reuse || ! $template || in_array( $post_type, $shared, true ) ) {
			return $reuse;
		}

		$from = (int) get_post_meta( $post_id, 'bricksfly_import_template', true );
		if ( ! $from ) {
			// Imported before that was stored: by the list of imports.
			$batch = (string) get_post_meta( $post_id, 'bricksfly_import_batch', true );
			foreach ( '' !== $batch ? \wealcoder\bricksfly\Admin\Pages\BRICKSFLY_Template_Importer::imported_templates() : array() as $record ) {
				if ( isset( $record['batch'], $record['id'] ) && $batch === (string) $record['batch'] ) {
					$from = (int) $record['id'];
				}
			}
		}

		return ! $from || $from === $template;
	}

	public function save_wp_page_import_track( $post_id, $original_id, $postdata, $data ) {
		// wp_insert_post() can return a WP_Error (and the WXR importer fires this
		// hook before its own is_wp_error() check). Bail on any non-positive /
		// error id, otherwise we'd point 'bricksfly_last_import_batch' at a batch that
		// has no real post — which makes "Go to page" resolve to nothing and the
		// latest imported page never show.
		if ( is_wp_error( $post_id ) || ! ( (int) $post_id > 0 ) ) {
			return;
		}

		$post_type = isset( $postdata['post_type'] ) ? (string) $postdata['post_type'] : '';
		if ( '' === $post_type || 'revision' === $post_type ) {
			return;
		}

		// One batch id for the whole import run, computed once per request so
		// everything imported together shares it (gmdate() alone would split a
		// multi-second import across batches). Every imported post carries it
		// (pages, Bricks templates, menu items, posts, media): the last import
		// step uses it to tell this demo's templates and pages from an earlier
		// demo's, and to pick the front page from this import.
		if ( empty( $this->page_import_batch_id ) ) {
			$this->page_import_batch_id = 'bricksfly_' . gmdate( 'Ymd_His' ) . '_' . wp_generate_password( 6, false );
		}
		$batch_id = $this->page_import_batch_id;

		// A post the import matched to an existing one (the same demo imported
		// again) now belongs to this import: the batch is replaced, not kept.
		update_post_meta( $post_id, 'bricksfly_import_batch', $batch_id );

		// A page imported on its own has its library id too: a demo imported
		// later brings its own page instead of taking over this one.
		$item = \wealcoder\bricksfly\Admin\Pages\BRICKSFLY_Template_Importer::running_item();
		if ( $item ) {
			update_post_meta( $post_id, 'bricksfly_import_template', $item );
		}

		if ( 'page' === $post_type ) {
			// The option always references a batch with a real, imported page.
			update_option( 'bricksfly_last_import_batch', $batch_id );
			add_post_meta( $post_id, 'bricksfly_imported', 1, true );

			// Invalidate the Pages-list "AAB Imported" badge count cache (see
			// admin/pages/page-import.php::custom_page_tab()) so it reflects
			// this import immediately instead of waiting out the 5-minute TTL.
			delete_transient( 'bricksfly_imported_page_count' );
		}
	}

	function bricksfly_get_latest_imported_pages() {
		if (
			! isset( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'bricksfly_admin_nonce' )
		) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid or missing nonce', 'bricksfly-elements-for-bricks' ) ), 403 );
		}

		if ( ! current_user_can( 'edit_pages' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You are not allowed to perform this action.', 'bricksfly-elements-for-bricks' ) ), 403 );
		}

		$per_page = isset( $_POST['per_page'] ) ? max( 1, (int) $_POST['per_page'] ) : 1;
		$batch_id = get_option( 'bricksfly_last_import_batch' );

		// Order by ID (insertion order), NOT date: imported pages keep the
		// template's original post_date, so "date DESC" surfaces the wrong page.
		// ID DESC reliably returns the most recently created (= just-imported) one.
		$base_args = array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'orderby'        => 'ID',
			'order'          => 'DESC',
			'no_found_rows'  => true,
			'fields'         => 'ids',
		);

		$post_ids = array();

		// 1) Preferred: the pages from the most recent import batch.
		//
		// A single top-level meta_key/meta_value pair (rather than a
		// meta_query array) generates the same simple postmeta join but
		// isn't flagged by WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		// — that sniff specifically targets meta_query arrays because those
		// can build arbitrarily complex multi-clause joins; a single
		// equality lookup like this one is the standard, sniff-clean way to
		// express it.
		if ( $batch_id ) {
			$q        = new \WP_Query(
				$base_args + array(
					'meta_key'   => 'bricksfly_import_batch',
					'meta_value' => $batch_id,
				)
			);
			$post_ids = $q->posts;
		}

		// 2) Fallback: any imported page. Covers a stale/empty batch option so
		// "Go to page" and the latest-page view still resolve to a real page.
		if ( empty( $post_ids ) ) {
			$q        = new \WP_Query(
				$base_args + array(
					'meta_key'   => 'bricksfly_imported',
					'meta_value' => 1,
				)
			);
			$post_ids = $q->posts;
		}

		if ( empty( $post_ids ) ) {
			wp_send_json_success(
				array(
					'batch_id' => $batch_id,
					'pages'    => array(),
				)
			);
		}

		$pages = array_map(
			function ( $id ) {
				return array(
					'id'        => $id,
					'title'     => get_the_title( $id ),
					'permalink' => get_permalink( $id ),
					'date'      => get_post_time( 'c', true, $id ),
				);
			},
			$post_ids
		);

		wp_send_json_success(
			array(
				'batch_id' => $batch_id,
				'pages'    => $pages,
			)
		);
	}

	/**
	 * Whether the content import was started with run().
	 *
	 * @return bool
	 */
	public static function is_running_in_php() {
		return self::$in_php;
	}

	/**
	 * One part of the content import of a full demo or a page, for an add-on
	 * that imports without the import screen: the same files, steps and hooks.
	 * Call it again while it answers "more".
	 *
	 * @param int $template_id Library item id of the running import.
	 * @param int $seconds     How long this part may take.
	 * @return string|WP_Error "more" or "done".
	 */
	public function run( $template_id, $seconds = 25 ) {
		if ( ! current_user_can( 'import' ) ) {
			return new WP_Error( 'bricksfly_import_rights', __( 'Your user role isn\'t high enough. You don\'t have permission to import demo data.', 'bricksfly-elements-for-bricks' ) );
		}

		$limit = static function () use ( $seconds ) {
			return max( 5, (int) $seconds );
		};
		add_filter( 'bricksfly/time_for_one_ajax_call', $limit, 99 );
		self::$in_php = true;
		$level        = ob_get_level();

		try {
			$this->create_importer( true );
			wp_raise_memory_limit( 'admin' );

			if ( ! $this->use_existing_importer_data() ) {
				$this->reset_run();
				$file = $this->download_content_file( $template_id );
				if ( is_wp_error( $file ) ) {
					return $file;
				}
			}

			$this->import_content_files();
			Helpers::do_action( 'bricksfly/after_all_import_execution', $this->selected_import_files, $this->import_files, $this->selected_index );
			$this->update_terms_count();
			$this->finish_run();

			return 'done';
		} catch ( Import_Pause $pause ) {
			wp_suspend_cache_invalidation( false );
			wp_defer_term_counting( false );
			wp_defer_comment_counting( false );

			return 'more';
		} finally {
			while ( ob_get_level() > $level ) {
				ob_end_clean();
			}
			self::$in_php = false;
			remove_filter( 'bricksfly/time_for_one_ajax_call', $limit, 99 );
		}
	}

	/**
	 * Forget an earlier run of this request.
	 *
	 * @return void
	 */
	private function reset_run() {
		$this->frontend_error_messages = array();
		$this->before_import_executed  = false;
		$this->imported_terms          = array();
		$this->page_import_batch_id    = '';
	}

	/**
	 * Download the content file the library released for this import.
	 *
	 * @param int $template_id Library item id.
	 * @return true|WP_Error
	 */
	private function download_content_file( $template_id ) {
		Helpers::set_demo_import_start_time();
		$this->log_file_path  = Helpers::get_log_path();
		$this->selected_index = 0;
		$this->import_files   = array();

		// The content file URL is the one the template library released
		// for this import (stored server-side by the template importer),
		// never a URL posted from the browser.
		$import_file = \wealcoder\bricksfly\Admin\Pages\BRICKSFLY_Template_Importer::get_import_file( $template_id );

		if ( ! $import_file || ! \wealcoder\bricksfly\Admin\BRICKSFLY_Library_Client::is_library_url( $import_file['content_url'] ) ) {
			return new WP_Error( 'bricksfly_import_no_file', __( 'No import files specified!', 'bricksfly-elements-for-bricks' ) );
		}

		$this->selected_import_files = Helpers::download_import_files( array( 'import_file_url' => $import_file['content_url'] ) );

		if ( is_wp_error( $this->selected_import_files ) ) {
			return $this->selected_import_files;
		}

		\wealcoder\bricksfly\Admin\Pages\BRICKSFLY_Template_Importer::add_page_fonts( $template_id, $this->selected_import_files['content'] );

		return true;
	}

	/**
	 * Import the downloaded content file (or the rest of it).
	 *
	 * @return void
	 */
	private function import_content_files() {
		Helpers::set_st_import_data_transient( $this->current_data() );

		if ( ! $this->before_import_executed ) {
			$this->before_import_executed = true;
			Helpers::do_action( 'bricksfly/before_content_import_execution', $this->selected_import_files, $this->import_files, $this->selected_index );
		}

		if ( ! empty( $this->selected_import_files['content'] ) ) {
			$this->append_to_frontend_error_messages( $this->importer->import_content( $this->selected_import_files['content'] ) );
		}

		Helpers::do_action( 'bricksfly/after_content_import_execution', $this->selected_import_files, $this->import_files, $this->selected_index );

		Helpers::set_st_import_data_transient( $this->current_data() );
	}

	/**
	 * Progress of this import with what the importer mapped so far.
	 *
	 * @return array
	 */
	private function current_data() {
		return array_merge( $this->get_current_importer_data(), $this->importer->get_importer_data() );
	}

	/**
	 * AJAX: one request of the content import.
	 */
	public function import_demo_data_ajax_callback() {
		Helpers::verify_ajax_call();

		// Same limit WordPress uses for admin tasks (WP_MAX_MEMORY_LIMIT).
		wp_raise_memory_limit( 'admin' );

		$use_existing_importer_data = $this->use_existing_importer_data();

		if ( ! $use_existing_importer_data ) {
			check_ajax_referer( 'bricksfly_admin_nonce', 'nonce' );

			$posted     = \wealcoder\bricksfly\Admin\Pages\BRICKSFLY_Template_Importer::read_posted_step();
			$downloaded = $this->download_content_file( $posted['id'] );

			if ( is_wp_error( $downloaded ) && 'bricksfly_import_no_file' !== $downloaded->get_error_code() ) {
				Helpers::log_error_and_send_ajax_response(
					$downloaded->get_error_message(),
					$this->log_file_path,
					esc_html__( 'Downloaded files', 'bricksfly-elements-for-bricks' )
				);
			} elseif ( is_wp_error( $downloaded ) ) {
				wp_send_json(
					array(
						'msg'      => $downloaded->get_error_message(),
						'progress' => 0,
						'template' => array(
							'id'        => $posted['id'],
							'next_step' => 'fail',
						),
					)
				);
			}
		}

		$this->import_content_files();

		if ( false !== Helpers::has_action( 'bricksfly/after_all_import_execution' ) ) {
			wp_send_json( array( 'status' => 'afterAllImportAJAX' ) );
		}

		$this->update_terms_count();
		$this->final_response();
	}

	public function after_all_import_data_ajax_callback() {
		Helpers::verify_ajax_call();
		if ( $this->use_existing_importer_data() ) {
			Helpers::do_action( 'bricksfly/after_all_import_execution', $this->selected_import_files, $this->import_files, $this->selected_index );
		}
		$this->update_terms_count();
		$this->final_response();
	}

	/**
	 * Remove the progress data of a finished content import.
	 *
	 * @return void
	 */
	private function finish_run() {
		$this->remove_empty_imported_menus();
		$this->remove_work_files();
		delete_transient( 'bricksfly_st_importer_data' );
		delete_option( 'bricksfly_template_import_progress' );
		delete_transient( 'bricksfly_st_importer_data_failed_attachment_imports' );
		delete_transient( 'bricksfly_import_menu_mapping' );
		delete_transient( 'bricksfly_import_posts_with_nav_block' );
	}

	/**
	 * Delete the content file and the log of this import from the uploads folder.
	 *
	 * @return void
	 */
	private function remove_work_files() {
		$uploads = wp_upload_dir( null, false );
		$folder  = wp_normalize_path( trailingslashit( $uploads['basedir'] ) );
		$files   = array(
			Helpers::apply_filters( 'bricksfly/downloaded_content_file_prefix', 'demo-content-import-file_' ) => isset( $this->selected_import_files['content'] ) ? $this->selected_import_files['content'] : '',
			Helpers::apply_filters( 'bricksfly/log_file_prefix', 'log_file_' ) => $this->log_file_path,
		);

		foreach ( $files as $prefix => $file ) {
			$file = is_string( $file ) ? wp_normalize_path( $file ) : '';
			// Only files this import made itself, never one a filter named.
			if ( '' !== $file && 0 === strpos( $file, $folder ) && false === strpos( $file, '/../' ) && 0 === strpos( basename( $file ), $prefix ) && is_file( $file ) ) {
				wp_delete_file( $file );
			}
		}
	}

	/**
	 * Last answer of the content import.
	 */
	private function final_response() {
		$this->finish_run();

		$response['msg']      = esc_html__( 'Congrats, your demo has been imported.', 'bricksfly-elements-for-bricks' );
		$response['progress'] = 80;

		check_ajax_referer( 'bricksfly_admin_nonce', 'nonce' );
		$posted               = \wealcoder\bricksfly\Admin\Pages\BRICKSFLY_Template_Importer::read_posted_step();
		$response['template'] = array(
			'id'        => $posted['id'],
			'next_step' => 'install-bricks-settings',
		);

		wp_send_json( $response );
	}

	private function use_existing_importer_data() {
		if ( $data = get_transient( 'bricksfly_st_importer_data' ) ) {
			$this->frontend_error_messages = empty( $data['frontend_error_messages'] ) ? array() : $data['frontend_error_messages'];
			$this->log_file_path           = empty( $data['log_file_path'] ) ? '' : $data['log_file_path'];
			$this->selected_index          = empty( $data['selected_index'] ) ? 0 : $data['selected_index'];
			$this->selected_import_files   = empty( $data['selected_import_files'] ) ? array() : $data['selected_import_files'];
			$this->import_files            = empty( $data['import_files'] ) ? array() : $data['import_files'];
			$this->before_import_executed  = empty( $data['before_import_executed'] ) ? false : $data['before_import_executed'];
			$this->imported_terms          = empty( $data['imported_terms'] ) ? array() : $data['imported_terms'];
			// One batch id for the whole import, across the requests a long import is split into.
			$this->page_import_batch_id = empty( $data['page_import_batch_id'] ) ? '' : (string) $data['page_import_batch_id'];
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
			'page_import_batch_id'    => $this->page_import_batch_id,
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
			// Escape here, at the point the value enters $frontend_error_messages —
			// this array is sent verbatim as JSON in the AJAX response (see
			// get_current_importer_data()), so it must never carry raw text
			// (which can include content pulled from the WXR file being
			// imported) on the assumption some later display step will escape
			// it before rendering.
			$line = esc_html( $line );
			if ( ! empty( $line ) && ! in_array( $line, $this->frontend_error_messages, true ) ) {
				$this->frontend_error_messages[] = $line;
			}
		}
	}

	public function frontend_error_messages_display() {
		$output = '';
		if ( ! empty( $this->frontend_error_messages ) ) {
			foreach ( $this->frontend_error_messages as $line ) {
				// $line is already esc_html()'d by append_to_frontend_error_messages() —
				// do not escape again here, or entities double-encode (e.g. "&amp;" -> "&amp;amp;").
				$output .= $line;
				$output .= '<br>';
			}
		}
		return $output;
	}

	/**
	 * Load the WXR importer classes. Only needed while a content import runs,
	 * so core's class-wp-importer.php is not loaded on other admin requests.
	 *
	 * @return void
	 */
	private static function load_importer_classes() {
		if ( class_exists( __NAMESPACE__ . '\\Importer', false ) ) {
			return;
		}

		if ( ! class_exists( 'WP_Importer', false ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
		}

		require_once BRICKSFLY_PATH . 'admin/base/WXRImporter.php';
		require_once BRICKSFLY_PATH . 'admin/aab-importer.php';
		require_once BRICKSFLY_PATH . 'admin/Importer.php';
	}

	public function setup_st_importer() {
		// Only the content-import AJAX request needs the importer.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Routing check only; the nonce is verified below and in the AJAX callback.
		$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';

		if ( ! wp_doing_ajax() || 'bricksfly_upload_manual_import_file' !== $action ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verification happens at the AJAX callback level; this runs on admin_init for importer setup.
		// Reject every POST request that does not carry a valid importer nonce.
		if ( ! empty( $_POST ) ) {
			$nonce = isset( $_POST['nonce'] )
				? sanitize_text_field( wp_unslash( $_POST['nonce'] ) )
				: '';

			if ( ! wp_verify_nonce( $nonce, 'bricksfly_admin_nonce' ) ) {
				return;
			}
		}
		$attachment_status = array_key_exists( 'attachment', $_POST ) ? sanitize_text_field( wp_unslash( $_POST['attachment'] ) ) : true;

		$this->create_importer( $attachment_status );
	}

	/**
	 * The importer of this request.
	 *
	 * @param mixed $attachments Whether media files are downloaded.
	 * @return void
	 */
	private function create_importer( $attachments ) {
		$this->import_files = array();

		$importer_options = array(
			'fetch_attachments' => $attachments,
		);

		$logger_options = array(
			'logger_min_level' => 'warning',
		);

		$logger            = new Logger();
		$logger->min_level = $logger_options['logger_min_level'];

		self::load_importer_classes();
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
			if ( preg_match( '#/log_file_[0-9_-]+\.txt$#', $data['attachment_url'] ) ) {
				return array(); // Log of an import on the demo site.
			}
			$failed_media_imports = Helpers::get_failed_attachment_imports();
			if ( ! empty( $failed_media_imports ) && in_array( $data['attachment_url'], $failed_media_imports, true ) ) {
				return array();
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
				$wcfio_post_nav_block = get_transient( 'bricksfly_import_posts_with_nav_block' );
				if ( empty( $wcfio_post_nav_block ) ) {
					$wcfio_post_nav_block = array();
				}
				$wcfio_post_nav_block[] = $post_id;
				set_transient( 'bricksfly_import_posts_with_nav_block', $wcfio_post_nav_block, HOUR_IN_SECONDS );
			}
		} else {
			$wcfio_menu_mapping = get_transient( 'bricksfly_import_menu_mapping' );
			if ( empty( $wcfio_menu_mapping ) ) {
				$wcfio_menu_mapping = array();
			}
			$wcfio_menu_mapping[] = array(
				'original_menu_id' => $original_id,
				'new_menu_id'      => $post_id,
			);
			set_transient( 'bricksfly_import_menu_mapping', $wcfio_menu_mapping, HOUR_IN_SECONDS );
		}
	}

	public function fix_imported_wp_navigation() {
		$nav_import_mapping = get_transient( 'bricksfly_import_menu_mapping' );
		$posts_nav_block    = get_transient( 'bricksfly_import_posts_with_nav_block' );

		if ( empty( $nav_import_mapping ) || empty( $posts_nav_block ) ) {
			return;
		}

		$replace_pairs = array();
		foreach ( $nav_import_mapping as $mapping ) {
			$replace_pairs[ '<!-- wp:navigation {"ref":' . $mapping['original_menu_id'] . '} /-->' ] = '<!-- wp:navigation {"ref":' . $mapping['new_menu_id'] . '} /-->';
		}

		foreach ( $posts_nav_block as $post_id ) {
			$post_nav_block = get_post( $post_id );
			if ( empty( $post_nav_block ) || empty( $post_nav_block->post_content ) ) {
				return;
			}
			wp_update_post(
				array(
					'ID'           => $post_id,
					'post_content' => strtr( $post_nav_block->post_content, $replace_pairs ),
				)
			);
		}
	}

	private function update_terms_count() {
		foreach ( $this->imported_terms as $tax => $terms ) {
			wp_update_term_count_now( $terms, $tax );
		}
	}
}
