<?php

namespace wealcoder\bricksfly\Admin\Pages;

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
class THEBRBRE_Builder_Template_Library {

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
			THEBRBRE_URL . 'public/build/admin/aab-template-library.css',
			[],
			THEBRBRE_VERSION
		);

		wp_enqueue_script(
			self::SCRIPT_HANDLE,
			THEBRBRE_URL . 'public/build/admin/aab-template-library.js',
			[ 'jquery' ],
			THEBRBRE_VERSION,
			true
		);

		$pro_installed = function_exists( 'thebrbre_is_pro_installed' ) ? thebrbre_is_pro_installed() : false;
		$pro_active    = function_exists( 'thebrbre_is_pro_active' ) ? thebrbre_is_pro_active() : false;
		$license_valid = function_exists( 'thebrbre_is_license_valid' ) ? thebrbre_is_license_valid() : false;

		// In the Bricks builder context, `get_the_ID()` resolves to the post
		// being edited (Bricks loads the front-end template chain just like
		// a normal page view). Fall back to common URL params for edge
		// cases (custom routing, REST previews, etc.).
		$post_id = (int) get_the_ID();
		if ( ! $post_id ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameters for context only, not processing form data.
			$post_id = isset( $_GET['post_id'] ) ? absint( wp_unslash( $_GET['post_id'] ) ) : (
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameters for context only, not processing form data.
				isset( $_GET['p'] ) ? absint( wp_unslash( $_GET['p'] ) ) : 0
			);
		}

		wp_localize_script(
			self::SCRIPT_HANDLE,
			'AAB_TEMPLATE_LIBRARY',
			[
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( 'aab-builder-template-library' ),
				'post_id'         => $post_id,
				// BricksFly brand mark shown inside the toolbar "Import Section"
				// button. Uses the same canonical logo the rest of the admin uses.
				'logo_url'        => esc_url( THEBRBRE_URL . 'public/images/plugin_logo.png' ),
				'template_types'  => self::get_template_types(),
				'remote_api'      => apply_filters(
					'aabaddons_builder_template_library_remote_api',
					'https://www.themecrowdy.com/wp-json/wp/v2/bricks-sections'
				),
				'remote_category' => apply_filters(
					'aabaddons_builder_template_library_remote_category_api',
					'https://www.themecrowdy.com/wp-json/wp/v2/bricks-sections-category',
				),

			   'remote_download' => apply_filters(
					'aabaddons_builder_template_library_remote_section_download_api',
					'https://www.themecrowdy.com/wp-json/bricks-sections/v1/download?id=',
				),
				'default_type'    => apply_filters( 'aabaddons_builder_template_library_default_type', 'block' ),
				'dashboard_link'  => admin_url( 'admin.php?page=bf_addons_settings' ),
				'pro_installed'   => $pro_installed,
				'pro_active'      => $pro_active,
				'config'          => apply_filters(
					'aabaddons_builder_template_library_config',
					[
						'wcf_valid'      => $license_valid,
						// Section import is gated by this flag (also enforced
						// server-side in ajax_insert_template()). The JS uses it
						// to show an upsell popup before the request is sent.
						'section_import' => function_exists( 'thebrbre_is_feature_allowed' ) && thebrbre_is_feature_allowed( 'section_import' ),
						'limitations'    => function_exists( 'thebrbre_get_license_limitations' ) ? thebrbre_get_license_limitations() : [],
					]
				),
				'i18n'            => [
					'modal_title'     => esc_html__( 'BrickFly Addons — Section Library', 'the-bricksfly' ),
					'button_label'    => esc_html__( 'Import Section', 'the-bricksfly' ),
					'insert'          => esc_html__( 'Insert', 'the-bricksfly' ),
					'inserting'       => esc_html__( 'Inserting…', 'the-bricksfly' ),
					'preview'         => esc_html__( 'Preview', 'the-bricksfly' ),
					'go_premium'      => esc_html__( 'Go Premium', 'the-bricksfly' ),
					'activate'        => esc_html__( 'Activate License', 'the-bricksfly' ),
					'install_pro'     => esc_html__( 'Install Pro', 'the-bricksfly' ),
					'upgrade_plan'    => esc_html__( 'Upgrade Plan', 'the-bricksfly' ),
					'section_locked'  => esc_html__( 'Section import is not included in your current license plan. Please upgrade your plan to import sections.', 'the-bricksfly' ),
					'search'          => esc_html__( 'Search', 'the-bricksfly' ),
					'category'        => esc_html__( 'Category', 'the-bricksfly' ),
					'all_colors'      => esc_html__( 'All', 'the-bricksfly' ),
					'light'           => esc_html__( 'Light', 'the-bricksfly' ),
					'dark'            => esc_html__( 'Dark', 'the-bricksfly' ),
					'close'           => esc_html__( 'Close', 'the-bricksfly' ),
					'loading'         => esc_html__( 'Loading', 'the-bricksfly' ),
					'empty'           => esc_html__( 'No templates found.', 'the-bricksfly' ),
					'fetch_failed'    => esc_html__( 'Failed to load templates. Check your connection and try again.', 'the-bricksfly' ),
					'insert_success'  => esc_html__( 'Section imported. Reloading builder…', 'the-bricksfly' ),
					'insert_failed'   => esc_html__( 'Could not import this section. Please try again.', 'the-bricksfly' ),
					'unsaved_warning' => esc_html__( 'Importing will reload the builder. Save your unsaved changes first?', 'the-bricksfly' ),
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
			'aabaddons_builder_template_library_types',
			[
				'block' => [
					'label' => esc_html__( 'Section Block', 'the-bricksfly' ),
				],
				// 'page'  => [
				// 	'label' => esc_html__( 'Page', 'the-bricksfly' ),
				// ],
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
	 *
	 * PREVIEW-BEFORE-COMMIT: this endpoint NO LONGER writes the post content or
	 * reloads the builder. It only RESOLVES the template (elements + global
	 * classes/variables) and returns it. The client feeds the result into
	 * Bricks' native paste pipeline so the section lands in the in-memory canvas
	 * as an unsaved, undoable change — committed to the DB only when the user
	 * clicks Bricks' own Save button. (Global classes/variables also ride along
	 * in the returned payload and are imported by the native paste, so they too
	 * persist only on Save.)
	 */
	public function ajax_insert_template() {
		check_ajax_referer( 'aab-builder-template-library', 'nonce' );

		$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;

		if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied for this post.', 'the-bricksfly' ) ], 403 );
		}

		// License limitation gate — Section import requires the `section_import`
		// flag on the active license. Enforced server-side so the client lock
		// (AAB_TEMPLATE_LIBRARY.config.section_import) can't be bypassed by a
		// forged AJAX call. `limited:true` lets the JS show the upsell popup.
		if ( function_exists( 'thebrbre_is_feature_allowed' ) && ! thebrbre_is_feature_allowed( 'section_import' ) ) {
			$message = function_exists( 'thebrbre_feature_denied_message' )
				? thebrbre_feature_denied_message( 'section_import' )
				: __( 'Section import is not included in your current license plan.', 'the-bricksfly' );

			wp_send_json_error( [
				'limited' => true,
				'feature' => 'section_import',
				'message' => $message,
			], 403 );
		}

		$template_id = isset( $_POST['template_id'] ) ? absint( $_POST['template_id'] ) : 0;

		if ( ! $template_id ) {
			wp_send_json_error( [ 'message' => __( 'No template id provided.', 'the-bricksfly' ) ], 400 );
		}

		$resolved = $this->resolve_template_payload( $template_id );

		if ( is_wp_error( $resolved ) ) {
			wp_send_json_error( [ 'message' => $resolved->get_error_message() ], 502 );
		}

		$elements = $resolved['content'];

		if ( empty( $elements ) || ! is_array( $elements ) ) {
			wp_send_json_error( [ 'message' => __( 'Template content is empty or in an unsupported format.', 'the-bricksfly' ) ], 422 );
		}

		/**
		 * Fires after a template has been resolved for preview insert.
		 *
		 * @param int   $post_id   The post being edited.
		 * @param array $elements  The resolved element array (not yet saved).
		 */
		do_action( 'aabaddons_builder_template_library_inserted', $post_id, $elements );

		// Return the full Bricks export shape so the client can build the native
		// paste envelope. Ids are NOT remapped here — Bricks' paste regenerates
		// element ids itself, so remapping server-side would be redundant work.
		wp_send_json_success( [
			'content'         => $elements,
			'global_classes'  => $resolved['global_classes'],
			'globalVariables' => $resolved['globalVariables'],
			'inserted_count'  => count( $elements ),
			'message'         => __( 'Template resolved.', 'the-bricksfly' ),
		] );
	}

	/**
	 * Resolve the full Bricks export payload for a remote template id.
	 *
	 * Two hops, both server-to-server so the download URL is never
	 * exposed to the client:
	 *   1. GET the section metadata from `/bricks-sections/v1/list/<id>`
	 *      to learn the signed `json_file.url`.
	 *   2. GET that URL and decode the Bricks copy/paste JSON.
	 *
	 * Returns the resolved elements together with any global classes / variables
	 * the section carries. Nothing is persisted — the client hands this to
	 * Bricks' native paste, which registers ids + globals in the in-memory store
	 * and saves them only when the user clicks Save.
	 *
	 * @param int $template_id Remote section id.
	 * @return array|\WP_Error { content, global_classes, globalVariables }
	 */
	private function resolve_template_payload( $template_id ) {
		$meta_endpoint = apply_filters(
			'aabaddons_builder_template_library_remote_single_api',
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
			return new \WP_Error( 'aab_no_template_source', __( 'Could not resolve template source.', 'the-bricksfly' ) );
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
			return new \WP_Error( 'aab_empty_template', __( 'Empty template response.', 'the-bricksfly' ) );
		}

		$decoded = json_decode( $body, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new \WP_Error( 'aab_invalid_template_json', __( 'Invalid template JSON.', 'the-bricksfly' ) );
		}

		// Return elements + globals untouched. The client builds Bricks' paste
		// envelope from this; Bricks' native paste regenerates element ids and
		// imports the globals into the in-memory store, persisting only on Save.
		return [
			'content'         => $this->extract_elements( $decoded ),
			'global_classes'  => $this->extract_globals( $decoded, 'classes' ),
			'globalVariables' => $this->extract_globals( $decoded, 'variables' ),
		];
	}

	/**
	 * Pull the global classes or variables out of a section payload.
	 *
	 * Accepts both the Bricks export key and its camel/snake variant:
	 *   - classes:   `global_classes` | `globalClasses`
	 *   - variables: `globalVariables` | `global_variables`
	 *
	 * @param mixed  $payload Decoded section JSON.
	 * @param string $which   'classes' or 'variables'.
	 * @return array
	 */
	private function extract_globals( $payload, $which ) {
		if ( ! is_array( $payload ) ) {
			return [];
		}

		$keys = ( 'variables' === $which )
			? [ 'globalVariables', 'global_variables' ]
			: [ 'global_classes', 'globalClasses' ];

		foreach ( $keys as $key ) {
			if ( isset( $payload[ $key ] ) && is_array( $payload[ $key ] ) ) {
				return $payload[ $key ];
			}
		}

		return [];
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
}

THEBRBRE_Builder_Template_Library::instance();
