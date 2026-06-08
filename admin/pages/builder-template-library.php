<?php

namespace AABAddons\Admin\Pages;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Builder Template Library — Bricks Builder Import Section.
 *
 * Ports the Elementor template-library import-section UX to Bricks Builder:
 * adds an "Import Section" button to the Bricks main toolbar that opens a
 * remote-template browser. On insert, the selected template JSON is
 * appended to the post's `_bricks_page_content_2` meta and the builder
 * window is reloaded so the new section becomes part of the canvas.
 *
 * Mirrors the API and config surface of the Elementor counterpart at:
 *   animation-addons-for-elementor/assets/js/wcf-template-library.js
 *   animation-addons-for-elementor/class-plugin.php
 *
 * @since 1.0.0
 */
class AAB_Builder_Template_Library {

	const SCRIPT_HANDLE = 'aab-builder-template-library';
	const STYLE_HANDLE  = 'aab-builder-template-library';

	private static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		// Builder enqueue — Bricks main builder window only.
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_builder_assets' ], 100 );

		// AJAX endpoint — insert the selected template into the post being
		// edited. The remote API now exposes `json_file.url` directly, so
		// we no longer need a separate "fetch JSON content" proxy.
		add_action( 'wp_ajax_aab_builder_insert_template', [ $this, 'ajax_insert_template' ] );
	}

	/**
	 * Is the request currently inside the Bricks Builder main window?
	 *
	 * The builder fires `wp_enqueue_scripts` like a normal page. We only want
	 * to load this UI inside the main builder chrome (not the iframe canvas,
	 * not the frontend).
	 *
	 * @return bool
	 */
	private function is_bricks_builder_main() {
		if ( ! function_exists( 'bricks_is_builder_main' ) ) {
			return false;
		}
		return (bool) bricks_is_builder_main();
	}

	/**
	 * Enqueue the import-section JS + CSS inside the Bricks Builder main window.
	 */
	public function enqueue_builder_assets() {
		if ( ! $this->is_bricks_builder_main() ) {
			return;
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		wp_enqueue_style(
			self::STYLE_HANDLE,
			AAB_ADDONS_URL . 'public/build/admin/aab-template-library.css',
			[],
			AAB_ADDONS_VERSION
		);

		wp_enqueue_script(
			self::SCRIPT_HANDLE,
			AAB_ADDONS_URL . 'public/build/admin/aab-template-library.js',
			[ 'jquery' ],
			AAB_ADDONS_VERSION,
			true
		);

		$pro_installed = function_exists( 'aab_is_pro_installed' ) ? aab_is_pro_installed() : false;
		$pro_active    = function_exists( 'aab_is_pro_active' ) ? aab_is_pro_active() : false;
		$license_valid = function_exists( 'aab_is_license_valid' ) ? aab_is_license_valid() : false;

		// In the Bricks builder context, `get_the_ID()` resolves to the post
		// being edited (Bricks loads the front-end template chain just like
		// a normal page view). Fall back to common URL params for edge
		// cases (custom routing, REST previews, etc.).
		$post_id = (int) get_the_ID();
		if ( ! $post_id ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameters for context only, not processing form data.
			$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : (
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameters for context only, not processing form data.
				isset( $_GET['p'] ) ? absint( $_GET['p'] ) : 0
			);
		}

		wp_localize_script(
			self::SCRIPT_HANDLE,
			'AAB_TEMPLATE_LIBRARY',
			[
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( 'aab-builder-template-library' ),
				'post_id'         => $post_id,
				'template_types'  => self::get_template_types(),
				'remote_api'      => apply_filters(
					'aab_builder_template_library_remote_api',
					'https://www.themecrowdy.com/wp-json/wp/v2/bricks-sections'
				),
				'remote_category' => apply_filters(
					'aab_builder_template_library_remote_category_api',
					'https://www.themecrowdy.com/wp-json/wp/v2/bricks-sections-category',
				),

			   'remote_download' => apply_filters(
					'aab_builder_template_library_remote_section_download_api',
					'https://www.themecrowdy.com/wp-json/bricks-sections/v1/download?id=',
				),
				'default_type'    => apply_filters( 'aab_builder_template_library_default_type', 'block' ),
				'dashboard_link'  => admin_url( 'admin.php?page=bf_addons_settings' ),
				'pro_installed'   => $pro_installed,
				'pro_active'      => $pro_active,
				'config'          => apply_filters(
					'aab_builder_template_library_config',
					[
						'wcf_valid' => $license_valid,
					]
				),
				'i18n'            => [
					'modal_title'     => esc_html__( 'Animation Addons — Section Library', 'bricksfly' ),
					'button_label'    => esc_html__( 'Import Section', 'bricksfly' ),
					'insert'          => esc_html__( 'Insert', 'bricksfly' ),
					'inserting'       => esc_html__( 'Inserting…', 'bricksfly' ),
					'go_premium'      => esc_html__( 'Go Premium', 'bricksfly' ),
					'activate'        => esc_html__( 'Activate License', 'bricksfly' ),
					'install_pro'     => esc_html__( 'Install Pro', 'bricksfly' ),
					'search'          => esc_html__( 'Search', 'bricksfly' ),
					'category'        => esc_html__( 'Category', 'bricksfly' ),
					'all_colors'      => esc_html__( 'All', 'bricksfly' ),
					'light'           => esc_html__( 'Light', 'bricksfly' ),
					'dark'            => esc_html__( 'Dark', 'bricksfly' ),
					'close'           => esc_html__( 'Close', 'bricksfly' ),
					'loading'         => esc_html__( 'Loading', 'bricksfly' ),
					'empty'           => esc_html__( 'No templates found.', 'bricksfly' ),
					'fetch_failed'    => esc_html__( 'Failed to load templates. Check your connection and try again.', 'bricksfly' ),
					'insert_success'  => esc_html__( 'Section imported. Reloading builder…', 'bricksfly' ),
					'insert_failed'   => esc_html__( 'Could not import this section. Please try again.', 'bricksfly' ),
					'unsaved_warning' => esc_html__( 'Importing will reload the builder. Save your unsaved changes first?', 'bricksfly' ),
				],
			]
		);
	}

	/**
	 * Template-type tabs shown in the modal header (mirrors the Elementor variant).
	 *
	 * @return array<string,array{label:string}>
	 */
	public static function get_template_types() {
		return apply_filters(
			'aab_builder_template_library_types',
			[
				'block' => [
					'label' => esc_html__( 'Block', 'bricksfly' ),
				],
				'page'  => [
					'label' => esc_html__( 'Page', 'bricksfly' ),
				],
			]
		);
	}

	/**
	 * Append a template's Bricks element array to a post's
	 * `_bricks_page_content_2` meta. The builder is reloaded after a
	 * successful insert so Vue picks up the new state.
	 *
	 * Expected POST:
	 *   - nonce
	 *   - post_id     — the post being edited in the builder
	 *   - template_id — remote section id; the server resolves the
	 *                   download URL itself so the URL never crosses
	 *                   the trust boundary.
	 *
	 * The Bricks data shape we expect is the same array that Bricks
	 * serializes into `_bricks_page_content_2`: a list of element objects
	 * with `id`, `name`, `parent`, `children`, `settings`, etc.
	 */
	public function ajax_insert_template() {
		check_ajax_referer( 'aab-builder-template-library', 'nonce' );

		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied for this post.', 'bricksfly' ) ], 403 );
		}

		$template_id = isset( $_POST['template_id'] ) ? absint( $_POST['template_id'] ) : 0;

		if ( ! $template_id ) {
			wp_send_json_error( [ 'message' => __( 'No template id provided.', 'bricksfly' ) ], 400 );
		}

		$elements = $this->resolve_template_elements( $template_id );

		if ( is_wp_error( $elements ) ) {
			wp_send_json_error( [ 'message' => $elements->get_error_message() ], 502 );
		}

		if ( empty( $elements ) || ! is_array( $elements ) ) {
			wp_send_json_error( [ 'message' => __( 'Template content is empty or in an unsupported format.', 'bricksfly' ) ], 422 );
		}

		// Regenerate every element id so we don't collide with existing
		// elements on the canvas. Also remap parent/children references.
		$elements = $this->remap_element_ids( $elements );

		$meta_key = defined( 'BRICKS_DB_PAGE_CONTENT' ) ? BRICKS_DB_PAGE_CONTENT : '_bricks_page_content_2';
		$existing = get_post_meta( $post_id, $meta_key, true );

		if ( ! is_array( $existing ) ) {
			$existing = [];
		}

		$merged = array_merge( $existing, $elements );

		update_post_meta( $post_id, $meta_key, $merged );

		/**
		 * Fires after a template has been imported into a Bricks post.
		 *
		 * @param int   $post_id   The post that was edited.
		 * @param array $elements  The newly inserted element array.
		 */
		do_action( 'aab_builder_template_library_inserted', $post_id, $elements );

		wp_send_json_success( [
			'inserted_count' => count( $elements ),
			'message'        => __( 'Template imported.', 'bricksfly' ),
		] );
	}

	/**
	 * Resolve the Bricks element array for a remote template id.
	 *
	 * Two hops, both server-to-server so the download URL is never
	 * exposed to the client:
	 *   1. GET the section metadata from `/bricks-sections/v1/list/<id>`
	 *      to learn the signed `json_file.url`.
	 *   2. GET that URL and decode the Bricks copy/paste JSON.
	 *
	 * @param int $template_id Remote section id.
	 * @return array|\WP_Error
	 */
	private function resolve_template_elements( $template_id ) {
		$meta_endpoint = apply_filters(
			'aab_builder_template_library_remote_single_api',
			'https://www.themecrowdy.com/wp-json/bricks-sections/v1/list/' . $template_id,
			$template_id
		);

		$meta_response = wp_remote_get(
			$meta_endpoint,
			[ 'timeout' => 30, 'sslverify' => false ]
		);

		if ( is_wp_error( $meta_response ) ) {
			return $meta_response;
		}

		$meta_body = wp_remote_retrieve_body( $meta_response );
		$meta      = json_decode( $meta_body, true );

		if ( empty( $meta['json_file']['url'] ) ) {
			return new \WP_Error( 'aab_no_template_source', __( 'Could not resolve template source.', 'bricksfly' ) );
		}

		$json_url = esc_url_raw( $meta['json_file']['url'] );

		$response = wp_remote_get(
			$json_url,
			[ 'timeout' => 30, 'sslverify' => false ]
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );

		if ( $code !== 200 || empty( $body ) ) {
			return new \WP_Error( 'aab_empty_template', __( 'Empty template response.', 'bricksfly' ) );
		}

		$decoded = json_decode( $body, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new \WP_Error( 'aab_invalid_template_json', __( 'Invalid template JSON.', 'bricksfly' ) );
		}

		return $this->extract_elements( $decoded );
	}

	/**
	 * Pull a Bricks element array out of varied remote payload shapes.
	 *
	 * The remote API may return:
	 *   - { content: [ …elements ] }            (preferred)
	 *   - { data: { content: [ …elements ] } }  (some templates)
	 *   - [ …elements ]                          (raw array)
	 *   - { source: 'bricksCopiedElements', content: [...] }  (Bricks copy/paste format)
	 *
	 * @param mixed $payload
	 * @return array
	 */
	private function extract_elements( $payload ) {
		if ( ! is_array( $payload ) ) {
			return [];
		}

		// Direct element array.
		if ( isset( $payload[0] ) && is_array( $payload[0] ) && isset( $payload[0]['id'] ) ) {
			return $payload;
		}

		if ( isset( $payload['content'] ) && is_array( $payload['content'] ) ) {
			return $payload['content'];
		}

		if ( isset( $payload['data']['content'] ) && is_array( $payload['data']['content'] ) ) {
			return $payload['data']['content'];
		}

		if ( isset( $payload['elements'] ) && is_array( $payload['elements'] ) ) {
			return $payload['elements'];
		}

		return [];
	}

	/**
	 * Regenerate Bricks element ids and remap parent/children references so
	 * the inserted section doesn't collide with elements already on the
	 * canvas. Bricks element ids are short alphanumeric strings — we use
	 * the same shape so the post-meta stays valid.
	 *
	 * @param array $elements
	 * @return array
	 */
	private function remap_element_ids( $elements ) {
		$id_map = [];

		// First pass: build old → new id map.
		foreach ( $elements as $element ) {
			if ( isset( $element['id'] ) ) {
				$id_map[ $element['id'] ] = $this->generate_element_id();
			}
		}

		// Second pass: rewrite ids + parent/children references.
		foreach ( $elements as &$element ) {
			if ( isset( $element['id'] ) && isset( $id_map[ $element['id'] ] ) ) {
				$element['id'] = $id_map[ $element['id'] ];
			}

			if ( isset( $element['parent'] ) && isset( $id_map[ $element['parent'] ] ) ) {
				$element['parent'] = $id_map[ $element['parent'] ];
			}

			if ( isset( $element['children'] ) && is_array( $element['children'] ) ) {
				$element['children'] = array_map(
					function ( $child_id ) use ( $id_map ) {
						return $id_map[ $child_id ] ?? $child_id;
					},
					$element['children']
				);
			}
		}
		unset( $element );

		return $elements;
	}

	/**
	 * Mimic Bricks' 6-char alphanumeric element id (uses the same alphabet
	 * the Bricks builder generates: `[a-z0-9]`).
	 */
	private function generate_element_id() {
		return strtolower( wp_generate_password( 6, false ) );
	}
}

AAB_Builder_Template_Library::instance();
