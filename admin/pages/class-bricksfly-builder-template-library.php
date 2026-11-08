<?php
/**
 * Builder template library: the "Import Section" button and AJAX insert for the Bricks builder.
 *
 * @package BricksFly
 */

namespace wealcoder\bricksfly\Admin\Pages;

use wealcoder\bricksfly\Admin\BRICKSFLY_Library_Client;
use wealcoder\bricksfly\Admin\BRICKSFLY_Section_Media;

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
class BRICKSFLY_Builder_Template_Library {

	const SCRIPT_HANDLE = 'bricksfly-builder-template-library';
	const STYLE_HANDLE  = 'bricksfly-builder-template-library';

	/**
	 * Single instance.
	 *
	 * @var BRICKSFLY_Builder_Template_Library|null
	 */
	private static $instance = null;

	/**
	 * Get the single instance.
	 *
	 * @return BRICKSFLY_Builder_Template_Library
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register the builder assets and the insert AJAX endpoint.
	 */
	public function __construct() {
		// Builder enqueue — Bricks main builder window only.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_builder_assets' ), 100 );

		// AJAX endpoint — insert the selected template into the post being
		// edited. The remote API now exposes `json_file.url` directly, so
		// we no longer need a separate "fetch JSON content" proxy.
		add_action( 'wp_ajax_bricksfly_builder_insert_template', array( $this, 'ajax_insert_template' ) );
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
			BRICKSFLY_URL . 'public/build/admin/aab-template-library.css',
			array(),
			BRICKSFLY_VERSION
		);

		wp_enqueue_script(
			self::SCRIPT_HANDLE,
			BRICKSFLY_URL . 'public/build/admin/aab-template-library.js',
			array( 'jquery' ),
			BRICKSFLY_VERSION,
			true
		);

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
			'BRICKSFLY_TEMPLATE_LIBRARY',
			array(
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( 'bricksfly-builder-template-library' ),
				'post_id'         => $post_id,
				// BricksFly brand mark shown inside the toolbar "Import Section"
				// button. Uses the same canonical logo the rest of the admin uses.
				'logo_url'        => esc_url( BRICKSFLY_URL . 'public/images/plugin_logo.png' ),
				'template_types'  => self::get_template_types(),
				'remote_api'      => apply_filters(
					'bricksfly_builder_template_library_remote_api',
					BRICKSFLY_Library_Client::api_url( 'wp/v2/bricks-sections' )
				),
				'remote_category' => apply_filters(
					'bricksfly_builder_template_library_remote_category_api',
					BRICKSFLY_Library_Client::api_url( 'wp/v2/bricks-sections-category' )
				),
				'default_type'    => apply_filters( 'bricksfly_builder_template_library_default_type', 'block' ),
				'dashboard_link'  => admin_url( 'admin.php?page=bricksfly_addons_settings' ),
				// Pro sections are shown with a "Pro" badge and this link. The
				// separate BricksFly Pro plugin sets `pro_library` so its users
				// get an Insert button instead; the library itself decides
				// whether it releases the section.
				'pro_url'         => 'https://bricksfly.com/pricing/',
				'pro_library'     => (bool) apply_filters( 'bricksfly_pro_library_enabled', false ),
				// What the button on Pro cards does (get / activate Pro, or Pro's own action).
				'pro_action'      => function_exists( 'bricksfly_pro_action' ) ? bricksfly_pro_action() : array(),
				'i18n'            => array(
					'modal_title'               => esc_html__( 'BricksFly Addons — Section Library', 'bricksfly-elements-for-bricks' ),
					'button_label'              => esc_html__( 'Import Section', 'bricksfly-elements-for-bricks' ),
					'insert'                    => esc_html__( 'Insert', 'bricksfly-elements-for-bricks' ),
					'options_title'             => esc_html__( 'Import settings', 'bricksfly-elements-for-bricks' ),
					'opt_summary_link'          => esc_html__( 'This section will look like the preview. Its photos keep loading from the BricksFly library.', 'bricksfly-elements-for-bricks' ),
					'opt_summary'               => esc_html__( 'This section will look like the preview. Its images are copied to your site.', 'bricksfly-elements-for-bricks' ),
					'opt_change'                => esc_html__( 'Change settings', 'bricksfly-elements-for-bricks' ),
					'opt_hide'                  => esc_html__( 'Hide settings', 'bricksfly-elements-for-bricks' ),
					'opt_design'                => esc_html__( 'Design', 'bricksfly-elements-for-bricks' ),
					'des_keep'                  => esc_html__( 'Keep original design', 'bricksfly-elements-for-bricks' ),
					'des_keep_desc'             => esc_html__( 'The section looks like the preview. Its global classes are added to your site.', 'bricksfly-elements-for-bricks' ),
					'des_site'                  => esc_html__( 'Use this site\'s style', 'bricksfly-elements-for-bricks' ),
					'des_site_desc'             => esc_html__( 'The section follows your site\'s styles. The template\'s global classes are not added.', 'bricksfly-elements-for-bricks' ),
					'opt_images'                => esc_html__( 'Images', 'bricksfly-elements-for-bricks' ),
					'img_copy'                  => esc_html__( 'Copy images to my site', 'bricksfly-elements-for-bricks' ),
					'img_copy_desc'             => esc_html__( 'Download the images into your Media Library.', 'bricksfly-elements-for-bricks' ),
					'img_link'                  => esc_html__( 'Keep demo image links', 'bricksfly-elements-for-bricks' ),
					'img_link_desc'             => esc_html__( 'Photos keep loading from the library server and nothing is downloaded. SVG icons are still copied so they display.', 'bricksfly-elements-for-bricks' ),
					'cancel'                    => esc_html__( 'Cancel', 'bricksfly-elements-for-bricks' ),
					'insert_section'            => esc_html__( 'Insert Section', 'bricksfly-elements-for-bricks' ),
					'inserting'                 => esc_html__( 'Inserting…', 'bricksfly-elements-for-bricks' ),
					'importing_title'           => esc_html__( 'Importing section…', 'bricksfly-elements-for-bricks' ),
					'importing_hint'            => esc_html__( 'Please wait. Do not close this window.', 'bricksfly-elements-for-bricks' ),
					'source_title'              => esc_html__( 'Import Section', 'bricksfly-elements-for-bricks' ),
					'source_sub'                => esc_html__( 'Where do you want to import from?', 'bricksfly-elements-for-bricks' ),
					'source_cloud'              => esc_html__( 'Cloud', 'bricksfly-elements-for-bricks' ),
					'source_cloud_desc'         => esc_html__( 'Browse ready-made sections from the BricksFly library.', 'bricksfly-elements-for-bricks' ),
					'source_live'               => esc_html__( 'Live Paste', 'bricksfly-elements-for-bricks' ),
					/* translators: %d: number of copied elements. */
					'source_live_desc'          => esc_html__( 'Paste the section you copied with Live Copy (%d elements).', 'bricksfly-elements-for-bricks' ),
					'source_live_desc_one'      => esc_html__( 'Paste the section you copied with Live Copy (1 element).', 'bricksfly-elements-for-bricks' ),
					'preview'                   => esc_html__( 'Preview', 'bricksfly-elements-for-bricks' ),
					'back_to_library'           => esc_html__( 'Back to library', 'bricksfly-elements-for-bricks' ),
					'open_new_tab'              => esc_html__( 'Open in new tab', 'bricksfly-elements-for-bricks' ),
					'free'                      => esc_html__( 'Free', 'bricksfly-elements-for-bricks' ),
					'pro'                       => esc_html__( 'Pro', 'bricksfly-elements-for-bricks' ),
					'get_pro'                   => esc_html__( 'Get BricksFly Pro', 'bricksfly-elements-for-bricks' ),
					'all_items'                 => esc_html__( 'All', 'bricksfly-elements-for-bricks' ),
					'search'                    => esc_html__( 'Search', 'bricksfly-elements-for-bricks' ),
					'category'                  => esc_html__( 'Category', 'bricksfly-elements-for-bricks' ),
					'all_colors'                => esc_html__( 'All', 'bricksfly-elements-for-bricks' ),
					'light'                     => esc_html__( 'Light', 'bricksfly-elements-for-bricks' ),
					'dark'                      => esc_html__( 'Dark', 'bricksfly-elements-for-bricks' ),
					'close'                     => esc_html__( 'Close', 'bricksfly-elements-for-bricks' ),
					'loading'                   => esc_html__( 'Loading', 'bricksfly-elements-for-bricks' ),
					'empty'                     => esc_html__( 'No templates found.', 'bricksfly-elements-for-bricks' ),
					'fetch_failed'              => esc_html__( 'Failed to load templates. Check your connection and try again.', 'bricksfly-elements-for-bricks' ),
					'insert_success'            => esc_html__( 'Section imported. Reloading builder…', 'bricksfly-elements-for-bricks' ),
					'insert_failed'             => esc_html__( 'Could not import this section. Please try again.', 'bricksfly-elements-for-bricks' ),
					'err_title'                 => esc_html__( 'This section could not be imported', 'bricksfly-elements-for-bricks' ),
					'err_what_to_do'            => esc_html__( 'What you can do', 'bricksfly-elements-for-bricks' ),
					'err_retry'                 => esc_html__( 'Try again', 'bricksfly-elements-for-bricks' ),
					'err_network'               => esc_html__( 'The request did not reach your site.', 'bricksfly-elements-for-bricks' ),
					'err_net_hint'              => esc_html__( 'Check your internet connection, then try again.', 'bricksfly-elements-for-bricks' ),
					'err_session'               => esc_html__( 'Your builder session has expired.', 'bricksfly-elements-for-bricks' ),
					'err_sess_hint'             => esc_html__( 'Save your work, reload the builder and insert the section again.', 'bricksfly-elements-for-bricks' ),
					'err_server'                => esc_html__( 'Your server returned an unexpected response.', 'bricksfly-elements-for-bricks' ),
					'err_server_hint'           => esc_html__( 'This is usually a PHP timeout or memory limit while downloading images. Try again with "Import images" turned off, or ask your host to raise max_execution_time and memory_limit.', 'bricksfly-elements-for-bricks' ),
					'err_paste'                 => esc_html__( 'The builder did not accept the section.', 'bricksfly-elements-for-bricks' ),
					'err_paste_hint'            => esc_html__( 'Reload the builder and try again. If it keeps happening, update Bricks and BricksFly to the latest versions.', 'bricksfly-elements-for-bricks' ),
					'err_code'                  => esc_html__( 'Error code', 'bricksfly-elements-for-bricks' ),
					'warn_title'                => esc_html__( 'Section inserted, with notes', 'bricksfly-elements-for-bricks' ),
					'warn_classes_renamed'      => esc_html__( 'Your site already has classes with the same name.', 'bricksfly-elements-for-bricks' ),
					'warn_classes_renamed_hint' => esc_html__( 'Your existing classes were left untouched. The section uses its own copy, added under a new name (for example heading-2). You can tidy them up in Bricks > Global classes.', 'bricksfly-elements-for-bricks' ),
					'warn_vars'                 => esc_html__( 'The section\'s colour and spacing variables could not be added automatically, so some colours may look different. Add them under Variables in the builder.', 'bricksfly-elements-for-bricks' ),
					'warn_ok'                   => esc_html__( 'Got it', 'bricksfly-elements-for-bricks' ),
					'unsaved_warning'           => esc_html__( 'Importing will reload the builder. Save your unsaved changes first?', 'bricksfly-elements-for-bricks' ),
				),
			)
		);
	}

	/**
	 * Template-type tabs shown in the modal header (mirrors the Elementor variant).
	 *
	 * @return array<string,array{label:string}>
	 */
	public static function get_template_types() {
		return apply_filters(
			'bricksfly_builder_template_library_types',
			array(
				'block' => array(
					'label' => esc_html__( 'Section Block', 'bricksfly-elements-for-bricks' ),
				),
			)
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
		check_ajax_referer( 'bricksfly-builder-template-library', 'nonce' );

		$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;

		if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
			$this->send_insert_error( 'permission', __( 'Permission denied for this post.', 'bricksfly-elements-for-bricks' ), 403 );
		}

		$template_id = isset( $_POST['template_id'] ) ? absint( $_POST['template_id'] ) : 0;

		if ( ! $template_id ) {
			$this->send_insert_error( 'no_template', __( 'No template id provided.', 'bricksfly-elements-for-bricks' ), 400 );
		}

		$resolved = $this->resolve_template_payload( $template_id );

		if ( is_wp_error( $resolved ) ) {
			$this->send_insert_error( $this->error_code_for( $resolved->get_error_code() ), $resolved->get_error_message(), 502 );
		}

		$elements = $resolved['content'];

		if ( empty( $elements ) || ! is_array( $elements ) ) {
			$this->send_insert_error( 'bad_template', __( 'Template content is empty or in an unsupported format.', 'bricksfly-elements-for-bricks' ), 422 );
		}

		// A section that uses BricksFly Pro elements cannot be inserted without
		// Pro: those elements would not exist and would come out empty.
		$missing_pro = $this->missing_pro_elements( $elements );

		if ( $missing_pro ) {
			$this->send_insert_error(
				'pro_elements',
				sprintf(
					/* translators: %s: list of element names */
					__( 'This section uses BricksFly Pro elements: %s.', 'bricksfly-elements-for-bricks' ),
					implode( ', ', $missing_pro )
				),
				403
			);
		}

		/**
		 * Fires after a template has been resolved for preview insert.
		 *
		 * @param int   $post_id   The post being edited.
		 * @param array $elements  The resolved element array (not yet saved).
		 */
		do_action( 'bricksfly_builder_template_library_inserted', $post_id, $elements );

		// Return the full Bricks export shape so the client can build the native
		// paste envelope. Ids are NOT remapped here — Bricks' paste regenerates
		// element ids itself, so remapping server-side would be redundant work.

		// Options chosen in the Insert popup (images default to "keep demo links", design to "keep original"). The nonce was
		// verified at the top of this handler.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$import_images  = isset( $_POST['import_images'] ) && '1' === sanitize_text_field( wp_unslash( $_POST['import_images'] ) );
		$import_classes = ! isset( $_POST['import_classes'] ) || '1' === sanitize_text_field( wp_unslash( $_POST['import_classes'] ) );
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		// Bring the section's images into the Media Library and point the
		// settings at the local attachments (see admin/class-bricksfly-section-media.php).
		$media                             = new BRICKSFLY_Section_Media( $import_images );
		list( $elements, $global_classes ) = $media->localize( $elements, is_array( $resolved['global_classes'] ) ? $resolved['global_classes'] : array() );

		// The library's custom fonts are not part of the export (there are no font
		// files) and their ids could point at an unrelated font on this site. Drop the
		// reference so the section simply uses this site's own font.
		$elements       = $this->strip_custom_fonts( $elements );
		$global_classes = $this->strip_custom_fonts( $global_classes );

		$renamed_classes = array();

		if ( $import_classes ) {
			// Never override a class the site already has: same-name classes are
			// kept as they are (see reconcile_global_classes()).
			list( $elements, $global_classes, $renamed_classes ) = $this->reconcile_global_classes( $elements, $global_classes );
		}

		if ( ! $import_classes ) {
			// Without the class definitions, drop the references so the section
			// does not point at classes this site does not have.
			$global_classes = array();
			$elements       = $this->strip_class_references( $elements );

			// The site's body/heading fonts must win, so drop the fonts set on elements.
			$elements = $this->strip_custom_fonts( $elements, true );

			// "Use this site's style": the template's own variables are not added, and
			// colors/sizes that point at them are dropped so the site's theme styles apply.
			$template_vars = array_column( is_array( $resolved['globalVariables'] ) ? $resolved['globalVariables'] : array(), 'name' );
			$site_vars     = array_column( (array) get_option( 'bricks_global_variables', array() ), 'name' );
			$drop_vars     = array_diff( $template_vars, $site_vars );
			if ( $drop_vars ) {
				$elements = $this->strip_variable_references( $elements, $drop_vars );
			}
			$resolved['globalVariables'] = array_values(
				array_filter(
					is_array( $resolved['globalVariables'] ) ? $resolved['globalVariables'] : array(),
					function ( $v ) use ( $site_vars ) {
						return isset( $v['name'] ) && in_array( $v['name'], $site_vars, true );
					}
				)
			);
		}

		$message = __( 'Template resolved.', 'bricksfly-elements-for-bricks' );
		if ( $media->skipped_svgs ) {
			$message = __( 'Section inserted. Some SVG icons stay on the remote server because SVG uploads are disabled for your role in Bricks > Settings > General.', 'bricksfly-elements-for-bricks' );
		}

		wp_send_json_success(
			array(
				'content'         => $elements,
				'global_classes'  => $global_classes,
				'globalVariables' => $resolved['globalVariables'],
				'inserted_count'  => count( $elements ),
				'message'         => $message,
				'warnings'        => $this->insert_warnings( $media, $import_images, $renamed_classes ),
			)
		);
	}

	/**
	 * Send a failed-insert response with a machine code and a "what to do" hint.
	 *
	 * @param string $code    Error code (see insert_error_hint()).
	 * @param string $message What went wrong.
	 * @param int    $status  HTTP status.
	 */
	private function send_insert_error( $code, $message, $status ) {
		wp_send_json_error(
			array(
				'code'    => $code,
				'message' => $message,
				'hint'    => $this->insert_error_hint( $code ),
			),
			$status
		);
	}

	/**
	 * Map a library WP_Error code to an insert error code.
	 *
	 * @param string $wp_code WP_Error code.
	 * @return string
	 */
	private function error_code_for( $wp_code ) {
		switch ( $wp_code ) {
			case 'bricksfly_pro_item':
				return 'pro_item';
			case 'bricksfly_library_host':
			case 'bricksfly_library_response':
				return 'library_unreachable';
			case 'bricksfly_empty_template':
			case 'bricksfly_invalid_template_json':
			case 'bricksfly_no_template_source':
			case 'bricksfly_library_missing':
				return 'bad_template';
			default:
				return 'unknown';
		}
	}

	/**
	 * What the user can do about a failed insert.
	 *
	 * @param string $code Error code.
	 * @return string
	 */
	private function insert_error_hint( $code ) {
		switch ( $code ) {
			case 'permission':
				return __( 'Your user cannot edit this page. Ask an administrator for access, or open a page you are allowed to edit.', 'bricksfly-elements-for-bricks' );
			case 'pro_item':
				return __( 'This section is part of BricksFly Pro. Install and activate BricksFly Pro with a valid license, then insert it again.', 'bricksfly-elements-for-bricks' );
			case 'library_unreachable':
				return __( 'Your site could not reach the BricksFly library. Check that this server can make outgoing connections to themecrowdy.com (a firewall or security plugin may block it), then try again.', 'bricksfly-elements-for-bricks' );
			case 'bad_template':
				return __( 'The library returned a section file we cannot read. This is on our side: try another section and tell us which one failed.', 'bricksfly-elements-for-bricks' );
			case 'pro_elements':
				if ( function_exists( 'bricksfly_is_pro_installed' ) && ! bricksfly_is_pro_installed() ) {
					return __( 'Install and activate BricksFly Pro, then insert the section again.', 'bricksfly-elements-for-bricks' );
				}
				if ( function_exists( 'bricksfly_is_pro_active' ) && ! bricksfly_is_pro_active() ) {
					return __( 'BricksFly Pro is installed but not active. Activate it, then insert the section again.', 'bricksfly-elements-for-bricks' );
				}
				return __( 'BricksFly Pro is active but your license is not valid. Activate your license in BricksFly Pro, then insert the section again.', 'bricksfly-elements-for-bricks' );
			case 'no_template':
				return __( 'Close the library, open it again and pick the section again.', 'bricksfly-elements-for-bricks' );
			default:
				return __( 'Reload the builder and try again. If it keeps failing, turn off "Import images" in the Import settings and retry.', 'bricksfly-elements-for-bricks' );
		}
	}

	/**
	 * Non-fatal problems of a successful insert, each with what to do next.
	 *
	 * @param BRICKSFLY_Section_Media $media         The media importer that ran.
	 * @param bool                    $import_images Whether the user kept "Import images" on.
	 * @param string[]                $renamed       Names of classes added under a new name.
	 * @return array<int,array{code:string,message:string,hint:string}>
	 */
	private function insert_warnings( $media, $import_images, $renamed = array() ) {
		$warnings = array();

		if ( $import_images && $media->failed_files ) {
			$warnings[] = array(
				'code'    => 'images_failed',
				/* translators: %d: number of images */
				'message' => sprintf( _n( '%d image could not be downloaded.', '%d images could not be downloaded.', $media->failed_files, 'bricksfly-elements-for-bricks' ), $media->failed_files ),
				'hint'    => __( 'They still load from the library site, so the section looks right for now. Insert the section again later, or replace those images from your Media Library.', 'bricksfly-elements-for-bricks' ),
			);
		}

		if ( $import_images && $media->over_limit ) {
			$warnings[] = array(
				'code'    => 'images_limit',
				/* translators: %d: number of images */
				'message' => sprintf( _n( '%d image was not downloaded because the section has a lot of images.', '%d images were not downloaded because the section has a lot of images.', $media->over_limit, 'bricksfly-elements-for-bricks' ), $media->over_limit ),
				'hint'    => __( 'Insert the section a second time to download the rest.', 'bricksfly-elements-for-bricks' ),
			);
		}

		if ( $import_images && $media->no_upload_cap ) {
			$warnings[] = array(
				'code'    => 'no_upload_cap',
				'message' => __( 'Images were not downloaded because your user cannot upload files.', 'bricksfly-elements-for-bricks' ),
				'hint'    => __( 'They still load from the library site. Ask an administrator to insert the section, or to give your role the upload files permission.', 'bricksfly-elements-for-bricks' ),
			);
		}

		if ( $renamed ) {
			$warnings[] = array(
				'code'    => 'classes_renamed',
				'message' => __( 'Your site already has classes with the same name.', 'bricksfly-elements-for-bricks' ),
				'tags'    => array_slice( $renamed, 0, 5 ),
				'hint'    => __( 'Your existing classes were left untouched. The section uses its own copy, added under a new name (for example heading-2). You can tidy them up in Bricks > Global classes.', 'bricksfly-elements-for-bricks' ),
			);
		}

		if ( $media->skipped_svgs ) {
			$warnings[] = array(
				'code'    => 'svg_disabled',
				'message' => __( 'SVG icons were not imported.', 'bricksfly-elements-for-bricks' ),
				'hint'    => __( 'Turn on SVG uploads for your role in Bricks > Settings > General, then insert the section again.', 'bricksfly-elements-for-bricks' ),
			);
		}

		return $warnings;
	}

	/**
	 * Names of BricksFly Pro elements a section uses that this site cannot run.
	 *
	 * Empty when Pro is running (active + valid license) or the section only
	 * uses free elements.
	 *
	 * @param array $elements Section elements.
	 * @return string[] Element labels.
	 */
	private function missing_pro_elements( array $elements ) {
		if ( function_exists( 'bricksfly_is_pro_usable' ) && bricksfly_is_pro_usable() ) {
			return array();
		}

		$used = array();
		foreach ( $elements as $element ) {
			if ( is_array( $element ) && ! empty( $element['name'] ) && is_string( $element['name'] ) ) {
				$used[ $element['name'] ] = true;
			}
		}

		$missing = array();
		foreach ( self::pro_elements() as $bricks_name => $label ) {
			if ( isset( $used[ $bricks_name ] ) ) {
				$missing[] = $label;
			}
		}

		return $missing;
	}

	/**
	 * BricksFly Pro elements: Bricks element name => label.
	 *
	 * Built from the plugin config (every widget flagged Pro). Their code lives in
	 * the Pro plugin, so the Bricks name follows the "aab-<slug>" convention, with
	 * the exceptions below.
	 *
	 * @return array<string,string>
	 */
	private static function pro_elements() {
		$exceptions = array(
			'gallery-progress' => 'gallery-progress',
		);

		$config = function_exists( 'bricksfly_get_config' ) ? bricksfly_get_config() : array();
		$found  = array();

		$walk = function ( $nodes ) use ( &$walk, &$found, $exceptions ) {
			foreach ( (array) $nodes as $slug => $node ) {
				if ( ! is_array( $node ) ) {
					continue;
				}

				if ( isset( $node['is_active'], $node['is_extension'], $node['is_pro'] ) ) {
					if ( ! empty( $node['is_pro'] ) && empty( $node['is_extension'] ) && empty( $node['is_upcoming'] ) ) {
						$name           = isset( $exceptions[ $slug ] ) ? $exceptions[ $slug ] : 'aab-' . $slug;
						$found[ $name ] = isset( $node['label'] ) ? (string) $node['label'] : (string) $slug;
					}
					continue;
				}

				$walk( $node );
			}
		};

		$walk( isset( $config['widgets']['elements'] ) ? $config['widgets']['elements'] : array() );

		return $found;
	}

	/**
	 * Make the section's global classes safe to add to this site.
	 *
	 * A class the site already has is never overwritten:
	 *  - same name and identical settings: the site's class is reused;
	 *  - same name (or id) but different settings: the site's class stays as it
	 *    is and the section's class is added under a new, unique name and id.
	 * Element references are updated to match.
	 *
	 * @param array $elements Section elements.
	 * @param array $incoming The section's global classes.
	 * @return array{0:array,1:array,2:string[]} Elements, classes to add, names of classes added under a new name.
	 */
	private function reconcile_global_classes( array $elements, array $incoming ) {
		$existing = get_option( 'bricks_global_classes', array() );
		$existing = is_array( $existing ) ? $existing : array();

		$by_id   = array();
		$by_name = array();
		foreach ( $existing as $class ) {
			if ( ! is_array( $class ) ) {
				continue;
			}
			if ( isset( $class['id'] ) ) {
				$by_id[ (string) $class['id'] ] = $class;
			}
			if ( isset( $class['name'] ) ) {
				$by_name[ (string) $class['name'] ] = $class;
			}
		}

		$taken_ids   = array_keys( $by_id );
		$taken_names = array_keys( $by_name );
		$id_map      = array();
		$keep        = array();
		$renamed     = array();

		foreach ( $incoming as $class ) {
			if ( ! is_array( $class ) || ! isset( $class['id'], $class['name'] ) ) {
				$keep[] = $class;
				continue;
			}

			$id        = (string) $class['id'];
			$name      = (string) $class['name'];
			$same_name = isset( $by_name[ $name ] ) ? $by_name[ $name ] : null;
			$same_id   = isset( $by_id[ $id ] ) ? $by_id[ $id ] : null;

			if ( $same_name && $this->same_class_content( $same_name, $class ) ) {
				// Already on the site: reuse it.
				$id_map[ $id ] = (string) $same_name['id'];
				continue;
			}

			if ( $same_id && ! $same_name && $this->same_class_content( $same_id, $class ) ) {
				continue;
			}

			if ( $same_name || $same_id ) {
				// An earlier insert may already have added this very class under a
				// new name ("heading2-2"): reuse that copy instead of adding another.
				$copy = $this->find_renamed_copy( $name, $class, $by_name );
				if ( $copy ) {
					$id_map[ $id ] = (string) $copy['id'];
					continue;
				}

				$new_name = $this->unique_class_name( $name, $taken_names );
				$new_id   = $this->unique_class_id( $taken_ids );

				$class['name'] = $new_name;
				$class['id']   = $new_id;

				$id_map[ $id ] = $new_id;
				$taken_names[] = $new_name;
				$taken_ids[]   = $new_id;
				$renamed[]     = $name;
			}

			$keep[] = $class;
		}

		if ( $id_map ) {
			foreach ( $elements as $key => $element ) {
				if ( empty( $element['settings']['_cssGlobalClasses'] ) || ! is_array( $element['settings']['_cssGlobalClasses'] ) ) {
					continue;
				}

				foreach ( $element['settings']['_cssGlobalClasses'] as $i => $class_id ) {
					if ( is_string( $class_id ) && isset( $id_map[ $class_id ] ) ) {
						$elements[ $key ]['settings']['_cssGlobalClasses'][ $i ] = $id_map[ $class_id ];
					}
				}
			}
		}

		return array( $elements, $keep, $renamed );
	}

	/**
	 * A class the site already has under "name-2", "name-3", ... with the same styling.
	 *
	 * @param string $name    Original class name.
	 * @param array  $incoming_class The section's class.
	 * @param array  $by_name Site classes keyed by name.
	 * @return array|null
	 */
	private function find_renamed_copy( $name, array $incoming_class, array $by_name ) {
		foreach ( $by_name as $site_name => $site_class ) {
			if ( preg_match( '/^' . preg_quote( $name, '/' ) . '-\d+$/', (string) $site_name ) && $this->same_class_content( $site_class, $incoming_class ) ) {
				return $site_class;
			}
		}

		return null;
	}

	/**
	 * Whether two global classes carry the same styling (name and id ignored).
	 *
	 * @param array $a Class.
	 * @param array $b Class.
	 * @return bool
	 */
	private function same_class_content( $a, $b ) {
		$pick = function ( $item ) {
			return array(
				'settings'  => isset( $item['settings'] ) ? $item['settings'] : array(),
				'selectors' => isset( $item['selectors'] ) ? $item['selectors'] : array(),
			);
		};

		return $this->normalize_for_compare( $pick( $a ) ) === $this->normalize_for_compare( $pick( $b ) );
	}

	/**
	 * Sort keys recursively and treat empty values alike, so equal styling compares equal.
	 *
	 * @param mixed $value Value.
	 * @return mixed
	 */
	private function normalize_for_compare( $value ) {
		if ( ! is_array( $value ) ) {
			return is_scalar( $value ) ? (string) $value : $value;
		}

		foreach ( $value as $k => $v ) {
			$value[ $k ] = $this->normalize_for_compare( $v );
		}

		ksort( $value );

		return $value;
	}

	/**
	 * A class name that is not used yet: "name-2", "name-3", ...
	 *
	 * @param string   $name  Wanted name.
	 * @param string[] $taken Names in use.
	 * @return string
	 */
	private function unique_class_name( $name, array $taken ) {
		$i = 2;
		while ( in_array( $name . '-' . $i, $taken, true ) ) {
			++$i;
		}

		return $name . '-' . $i;
	}

	/**
	 * A Bricks-style class id (6 lowercase letters and digits) that is not used yet.
	 *
	 * @param string[] $taken Ids in use.
	 * @return string
	 */
	private function unique_class_id( array $taken ) {
		do {
			$id = strtolower( wp_generate_password( 6, false, false ) );
		} while ( in_array( $id, $taken, true ) );

		return $id;
	}

	/**
	 * Remove references to custom fonts ("custom_font_12") from settings, at any depth.
	 *
	 * @param mixed $value Elements, classes or any nested settings.
	 * @param bool  $all   True to drop every font family, not only custom fonts.
	 * @return mixed
	 */
	private function strip_custom_fonts( $value, $all = false ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		foreach ( $value as $key => $item ) {
			if ( 'font-family' === $key && is_string( $item ) && ( $all || preg_match( '/^custom_font_\d+$/', $item ) ) ) {
				unset( $value[ $key ] );
				continue;
			}

			if ( is_array( $item ) ) {
				$value[ $key ] = $this->strip_custom_fonts( $item, $all );
			}
		}

		return $value;
	}

	/**
	 * Remove settings whose value is exactly var(--name) for one of the given variables.
	 * Handles plain strings and Bricks color objects ({ raw: "var(--name)", ... }).
	 *
	 * @param mixed $value Elements or nested settings.
	 * @param array $names Variable names (without "--").
	 * @return mixed
	 */
	private function strip_variable_references( $value, array $names ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		$is_ref = function ( $v ) use ( $names ) {
			return is_string( $v ) && preg_match( '/^\s*var\(\s*--([A-Za-z0-9_-]+)\s*\)\s*$/', $v, $m ) && in_array( $m[1], $names, true );
		};

		foreach ( $value as $key => $item ) {
			if ( $is_ref( $item ) || ( is_array( $item ) && isset( $item['raw'] ) && $is_ref( $item['raw'] ) ) ) {
				unset( $value[ $key ] );
				continue;
			}

			if ( is_array( $item ) ) {
				$value[ $key ] = $this->strip_variable_references( $item, $names );
			}
		}

		return $value;
	}

	/**
	 * Remove global class references from every element's settings.
	 *
	 * @param array $elements Section elements.
	 * @return array
	 */
	private function strip_class_references( array $elements ) {
		foreach ( $elements as $key => $element ) {
			if ( is_array( $element ) && isset( $element['settings'] ) && is_array( $element['settings'] ) ) {
				unset( $elements[ $key ]['settings']['_cssGlobalClasses'] );
			}
		}

		return $elements;
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
		$meta = BRICKSFLY_Library_Client::get_section( $template_id );

		if ( is_wp_error( $meta ) ) {
			return $meta;
		}

		// The library withholds the file of a section it does not release.
		if ( empty( $meta['json_file']['url'] ) ) {
			if ( ! empty( $meta['is_pro'] ) ) {
				return new \WP_Error( 'bricksfly_pro_item', BRICKSFLY_Library_Client::pro_item_message() );
			}
			return new \WP_Error( 'bricksfly_no_template_source', __( 'Could not resolve template source.', 'bricksfly-elements-for-bricks' ) );
		}

		$response = BRICKSFLY_Library_Client::get(
			esc_url_raw( $meta['json_file']['url'] ),
			array( 'timeout' => 30 )
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );

		if ( 200 !== $code || empty( $body ) ) {
			return new \WP_Error( 'bricksfly_empty_template', __( 'Empty template response.', 'bricksfly-elements-for-bricks' ) );
		}

		$decoded = json_decode( $body, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new \WP_Error( 'bricksfly_invalid_template_json', __( 'Invalid template JSON.', 'bricksfly-elements-for-bricks' ) );
		}

		// Return elements + globals untouched. The client builds Bricks' paste
		// envelope from this; Bricks' native paste regenerates element ids and
		// imports the globals into the in-memory store, persisting only on Save.
		$content   = $this->extract_elements( $decoded );
		$classes   = $this->extract_globals( $decoded, 'classes' );
		$variables = $this->variables_from_palette( $decoded, $content, $classes, $this->extract_globals( $decoded, 'variables' ) );

		return array(
			'content'         => $content,
			'global_classes'  => $classes,
			'globalVariables' => $variables,
		);
	}

	/**
	 * Variables the section uses but did not export, taken from its colour palette.
	 *
	 * Some exports keep the design colours in the palette ("raw": "var(--pt-accent)",
	 * with a light and a dark value) and leave the Variables list empty. For every
	 * variable the section really uses and the site does not get from the export,
	 * add one with the palette's light value. Existing variables are never replaced.
	 *
	 * @param mixed $payload   Decoded section JSON.
	 * @param array $content   Section elements.
	 * @param array $classes   Section global classes.
	 * @param array $variables Variables exported with the section.
	 * @return array Variables to add.
	 */
	private function variables_from_palette( $payload, array $content, array $classes, array $variables ) {
		$palettes = ( is_array( $payload ) && isset( $payload['colorPalette'] ) && is_array( $payload['colorPalette'] ) ) ? $payload['colorPalette'] : array();
		if ( ! $palettes ) {
			return $variables;
		}

		$have = array();
		foreach ( $variables as $variable ) {
			if ( is_array( $variable ) && isset( $variable['name'] ) ) {
				$have[ (string) $variable['name'] ] = true;
			}
		}

		$used = wp_json_encode( array( $content, $classes ) );

		foreach ( $palettes as $palette ) {
			if ( ! is_array( $palette ) || empty( $palette['colors'] ) || ! is_array( $palette['colors'] ) ) {
				continue;
			}

			foreach ( $palette['colors'] as $color ) {
				if ( ! is_array( $color ) || empty( $color['raw'] ) || empty( $color['light'] ) || ! is_string( $color['raw'] ) || ! is_string( $color['light'] ) ) {
					continue;
				}

				if ( ! preg_match( '/^var\(--([a-z0-9_-]+)\)$/i', $color['raw'], $match ) ) {
					continue;
				}

				$name = $match[1];
				if ( isset( $have[ $name ] ) || false === strpos( $used, 'var(--' . $name ) ) {
					continue;
				}

				$variables[]   = array(
					'id'    => isset( $color['id'] ) ? (string) $color['id'] : strtolower( wp_generate_password( 6, false, false ) ),
					'name'  => $name,
					'value' => $color['light'],
				);
				$have[ $name ] = true;
			}
		}

		return $variables;
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
			return array();
		}

		$keys = ( 'variables' === $which )
			? array( 'globalVariables', 'global_variables' )
			: array( 'global_classes', 'globalClasses' );

		foreach ( $keys as $key ) {
			if ( isset( $payload[ $key ] ) && is_array( $payload[ $key ] ) ) {
				return $payload[ $key ];
			}
		}

		return array();
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
	 * @param mixed $payload Decoded remote payload.
	 * @return array
	 */
	private function extract_elements( $payload ) {
		if ( ! is_array( $payload ) ) {
			return array();
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

		return array();
	}
}

BRICKSFLY_Builder_Template_Library::instance();
