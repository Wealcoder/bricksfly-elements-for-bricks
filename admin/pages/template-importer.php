<?php

namespace wealcoder\bricksfly\Admin\Pages;

use wealcoder\bricksfly\Admin\BRICKSFLY_Library_Client;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class BRICKSFLY_Template_Importer {

	public $file_path = 'bricksfly_tpl_file.xml';
	public $full_path = null;
	public $wishlist_key = 'bricksfly_user_wishlists';

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action( 'wp_ajax_bricksfly_template_installer', [ $this, 'template_installer' ] );
		add_action( 'wp_ajax_bricksfly_heartbeat_data', [ $this, 'heartbeat_data' ] );
		add_action( 'wp_ajax_bricksfly_wishlist_option', [ $this, 'wishlist' ] );
		add_action( 'wp_ajax_bricksfly_template_dependency_status', [ $this, 'template_dependency_status' ] );
		// NOTE: the 'bricksfly_get_latest_imported_pages' AJAX action is handled by
	
		// That handler is batch-aware — it returns the page(s) from the most
		// recent import via the 'bricksfly_last_import_batch' option, which is what the
		// "Go to page" button on the Complete Import step needs. A second callback
		// here on the same action raced the correct one (whichever fired first
		// won and called wp_die) and queried by date DESC — which returns the
		// wrong page because WXR preserves each page's original post_date. Removed.
		add_filter('bricksfly_dashboard_config', [ $this, 'include_user_wishlist' ] );
	}

	public function include_user_wishlist( $config ) {
		$user_id           = get_current_user_id();
		$config['wishlist'] = get_user_meta( $user_id, $this->wishlist_key, true );
		if ( ! is_array( $config['wishlist'] ) ) {
			$config['wishlist'] = [];
		}
		return $config;
	}

	public function heartbeat_data() {
		check_ajax_referer( 'bricksfly_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action.', 'bricksfly-elements-for-bricks' ) );
		}

		$return_data = apply_filters('bricksfly_heartbeat_data', [
			'import_state'   => get_option( 'bricksfly_template_import_state' ),
			'import_porgress' => get_option( 'bricksfly_template_import_progress' ),
		] );
		wp_send_json( $return_data );
	}

	public function wishlist() {
		check_ajax_referer( 'bricksfly_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action.', 'bricksfly-elements-for-bricks' ) );
		}

		if ( ! isset( $_POST['wishlist'] ) ) {
			wp_send_json_error( __( 'Provide wishlist data.', 'bricksfly-elements-for-bricks' ) );
		}

		$wishlist    = sanitize_text_field( wp_unslash( $_POST['wishlist'] ) );
		$user_id     = get_current_user_id();
		$wishlist_db = get_user_meta( $user_id, $this->wishlist_key, true );
		$wishlist_db = is_array( $wishlist_db ) ? $wishlist_db : [];

		if ( in_array( $wishlist, $wishlist_db, true ) ) {
			$wishlist_db = array_values( array_filter( $wishlist_db, fn( $v ) => $v !== $wishlist ) );
		} else {
			$wishlist_db[] = $wishlist;
		}

		update_user_meta( $user_id, $this->wishlist_key, $wishlist_db );
		wp_send_json_success( $wishlist_db );
	}

	public function template_dependency_status() {
		check_ajax_referer( 'bricksfly_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action.', 'bricksfly-elements-for-bricks' ) );
		}

		$dependencies = [];
		if ( isset( $_POST['dependencies'] ) ) {
			$json_data    = sanitize_text_field( wp_unslash( $_POST['dependencies'] ) );
			$dependencies = json_decode( $json_data, true );
		}

		if ( empty( $dependencies ) ) {
			wp_send_json_error( __( 'No dependencies provided.', 'bricksfly-elements-for-bricks' ) );
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		// Check plugin statuses (read-only; nothing is installed or activated here).
		if ( ! empty( $dependencies['plugins'] ) && is_array( $dependencies['plugins'] ) ) {
			$installed = get_plugins();

			foreach ( $dependencies['plugins'] as &$plugin ) {
				$base_slug = isset( $plugin['Base_Slug'] ) ? plugin_basename( sanitize_text_field( (string) $plugin['Base_Slug'] ) ) : '';
				if ( $base_slug && isset( $installed[ $base_slug ] ) ) {
					if ( is_plugin_active( $base_slug ) ) {
						$plugin['status'] = 'Active';
					} else {
						$plugin['status'] = 'Installed';
					}
				} else {
					$plugin['status'] = 'Not Installed';
				}
			}
		}

		// Check theme statuses
		if ( ! empty( $dependencies['themes'] ) && is_array( $dependencies['themes'] ) ) {
			foreach ( $dependencies['themes'] as &$theme ) {
				$theme_data = wp_get_theme( $theme['slug'] ?? '' );
				if ( $theme_data->exists() ) {
					$theme['status'] = ( get_stylesheet() === $theme['slug'] )
						? __( 'Active', 'bricksfly-elements-for-bricks' )
						: __( 'Installed', 'bricksfly-elements-for-bricks' );
				} else {
					$theme['status'] = __( 'Not Installed', 'bricksfly-elements-for-bricks' );
				}
			}
		}

		wp_send_json_success( [ 'dependencies' => $dependencies ] );
	}

	/**
	 * Per-user key for the server-side state of the running import.
	 *
	 * @return string
	 */
	private static function state_key() {
		return 'bricksfly_import_state_' . get_current_user_id();
	}

	/**
	 * Content file the library released for the running import.
	 *
	 * Used by the content-import step (admin/st-init.php) so the file URL
	 * always comes from the library, never from the browser.
	 *
	 * @param int $template_id Library item id the caller is importing.
	 * @return array|null { type, content_url, id } or null when unknown.
	 */
	public static function get_import_file( $template_id ) {
		$state = get_transient( self::state_key() );

		if ( ! is_array( $state ) || empty( $state['file']['content_url'] ) || absint( $template_id ) !== (int) $state['id'] ) {
			return null;
		}

		return $state['file'];
	}

	/**
	 * Read the fields the import step machine needs from the posted
	 * template_data. Everything else about the template is read from the
	 * library by id.
	 *
	 * @return array{id:int,next_step:string}
	 */
	public static function read_posted_step() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Callers verify the nonce first; the JSON is decoded and only `id` (absint) and `next_step` (sanitize_key) are read.
		$raw  = isset( $_POST['template_data'] ) ? wp_unslash( $_POST['template_data'] ) : '';
		$data = is_string( $raw ) ? json_decode( $raw, true ) : null;

		return array(
			'id'        => is_array( $data ) && isset( $data['id'] ) ? absint( $data['id'] ) : 0,
			'next_step' => is_array( $data ) && isset( $data['next_step'] ) ? sanitize_key( (string) $data['next_step'] ) : '',
		);
	}

	/**
	 * Stop the import with a message.
	 *
	 * @param array  $template_data Current template data.
	 * @param string $message       Message for the user.
	 * @return void
	 */
	private function send_failure( $template_data, $message ) {
		delete_transient( self::state_key() );
		delete_option( 'bricksfly_template_import_progress' );
		update_option( 'bricksfly_template_import_state', $message );

		$template_data['next_step'] = 'fail';

		wp_send_json( array(
			'template' => $template_data,
			'msg'      => $message,
			'progress' => 0,
		) );
	}

	public function template_installer() {
		check_ajax_referer( 'bricksfly_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( __( 'You are not allowed to do this action', 'bricksfly-elements-for-bricks' ) );
		}

		$import_type  = isset( $_POST['import_type'] ) ? sanitize_key( wp_unslash( $_POST['import_type'] ) ) : 'full-demo';
		$item_type    = ( 'page' === $import_type ) ? 'page' : 'template';
		$posted       = self::read_posted_step();
		$next_step    = $posted['next_step'];
		$progress     = '25';
		$msg          = '';
		$user_plugins = array();

		if ( isset( $_POST['user_plugins'] ) ) {
			$user_plugins = array_filter( array_map( 'sanitize_key', explode( ',', sanitize_text_field( wp_unslash( $_POST['user_plugins'] ) ) ) ) );
		}

		// The template itself always comes from the library, by id.
		$template_data = BRICKSFLY_Library_Client::get_template_item( $item_type, $posted['id'] );

		if ( is_wp_error( $template_data ) ) {
			$this->send_failure( array( 'id' => $posted['id'] ), $template_data->get_error_message() );
		}

		$template_data['next_step'] = $next_step;

		if ( '' === $next_step ) {
			// Ask the library for the content file before touching the site,
			// so an item the library does not release stops here.
			$file = BRICKSFLY_Library_Client::get_content_file( $template_data['id'] );

			if ( is_wp_error( $file ) ) {
				$this->send_failure( $template_data, $file->get_error_message() );
			}

			// Content progress of an earlier import must not show for this one.
			delete_option( 'bricksfly_template_import_progress' );

			set_transient(
				self::state_key(),
				array(
					'id'   => (int) $template_data['id'],
					'type' => $item_type,
					'file' => $file,
				),
				DAY_IN_SECONDS
			);

			$template_data['next_step'] = 'plugins-importer';
			$progress                   = '10';
			update_option( 'bricksfly_template_import_state', __( 'Checking Setup requirement', 'bricksfly-elements-for-bricks' ) );

		} elseif ( 'plugins-importer' === $next_step ) {
			$progress = '20';

			/**
			 * Required plugins step.
			 *
			 * The free plugin does not install or activate plugins; the import
			 * screen only shows each required plugin's status. An add-on may
			 * handle the plugins the user selected here.
			 *
			 * @param array    $template_data Template read from the library (includes `dependencies`).
			 * @param string[] $user_plugins  Plugin slugs the user selected in the import screen.
			 * @param string   $item_type     'template' or 'page'.
			 */
			do_action( 'bricksfly/starter-template/import/step/plugins', $template_data, $user_plugins, $item_type );

			$template_data['next_step'] = 'install-wp-options';

		} elseif ( 'install-wp-options' === $next_step ) {
			$template_data['next_step'] = 'check-template-status';
			$progress                   = '30';
			$msg                        = __( 'Downloading Template', 'bricksfly-elements-for-bricks' );

			if ( isset( $template_data['wp_options'] ) && is_array( $template_data['wp_options'] ) ) {
				$this->install_options( $template_data['wp_options'], $template_data, $item_type );
			}

			if ( 'page' !== $import_type ) {
				do_action( 'bricksfly/starter-template/import/step/wp_options' );
			}

			update_option( 'bricksfly_template_import_state', $msg );

		} elseif ( 'check-template-status' === $next_step ) {
			$file = self::get_import_file( $template_data['id'] );

			if ( ! $file ) {
				$this->send_failure( $template_data, __( 'Missing Content file, contact author', 'bricksfly-elements-for-bricks' ) );
			}

			update_option( 'bricksfly_template_import_state', __( 'Content file Downloading', 'bricksfly-elements-for-bricks' ) );
			$template_data['next_step'] = 'download-xml-file';
			$template_data['file']      = $file;
			$progress                   = '37';

		} elseif ( 'install-template' === $next_step ) {
			$template_data['next_step'] = 'check-theme';
			$progress                   = '50';
			$msg                        = __( 'Verifying Content Import', 'bricksfly-elements-for-bricks' );
			update_option( 'bricksfly_template_import_state', __( 'Checking Theme', 'bricksfly-elements-for-bricks' ) );

		} elseif ( 'check-theme' === $next_step || 'install-theme' === $next_step ) {
			$template_data['next_step'] = 'install-bricks-settings';
			$progress                   = '75';

		} elseif ( 'install-bricks-settings' === $next_step ) {
			$template_data['next_step'] = 'done';
			$progress                   = '100';

			$this->update_blog_and_homepage_options( $template_data );

			if ( 'page' !== $item_type ) {
				$this->enable_free_features();
			}

			do_action( 'bricksfly/starter-template/import/step/metasettings' );
			delete_transient( self::state_key() );

		} else {
			$this->send_failure( $template_data, __( 'Template Demo Import fail', 'bricksfly-elements-for-bricks' ) );
		}

		wp_send_json( array(
			'template' => $template_data,
			'msg'      => $msg,
			'progress' => $progress,
		) );
	}

	private function update_blog_and_homepage_options( $template_data ) {
		if ( ! empty( $template_data['home_page'] ) ) {
			$front_page = get_posts( [
				'post_type'   => 'page',
				'title'       => $template_data['home_page'],
				'post_status' => 'all',
				'numberposts' => 1,
			] );

			if ( ! empty( $front_page ) ) {
				update_option( 'page_on_front', $front_page[0]->ID );
			}
		}

		if ( ! empty( $template_data['blog_page'] ) ) {
			$blog_page = get_posts( [
				'post_type'   => 'page',
				'title'       => $template_data['blog_page'],
				'post_status' => 'all',
				'numberposts' => 1,
			] );

			if ( ! empty( $blog_page ) ) {
				update_option( 'page_for_posts', $blog_page[0]->ID );
			}

			if ( ! empty( $blog_page ) || ! empty( $front_page ) ) {
				update_option( 'show_on_front', 'page' );
			}
		}
	}

	/**
	 * Apply a template's options files.
	 *
	 * The free plugin only merges the template's Bricks design data (global
	 * classes, variables, colour palette, theme styles) into the site, so the
	 * imported pages render as designed. It only ever ADDS entries; the
	 * site's own classes, variables and styles are never replaced. No other
	 * option is written here.
	 *
	 * Every other option in the file is handed, unapplied, to the
	 * `bricksfly/starter-template/import/options` action for an add-on.
	 *
	 * @param array  $settings      `wp_options` entries of the template (library data).
	 * @param array  $template_data Template read from the library.
	 * @param string $item_type     'template' or 'page'.
	 * @return void
	 */
	private function install_options( $settings, $template_data, $item_type ) {
		$addon_options      = array();
		$custom_breakpoints = false;
		$breakpoints_added  = false;

		foreach ( $settings as $item ) {
			if ( empty( $item['xml_file'] ) ) {
				continue;
			}

			// Options files are only ever read from the template library.
			$response = BRICKSFLY_Library_Client::get( (string) $item['xml_file'] );

			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				continue;
			}

			$body = wp_remote_retrieve_body( $response );
			if ( empty( $body ) ) {
				continue;
			}

			// Some option sources are Bricks export JSON files rather than WP
			// options XML (flagged `page_global_class`, or JSON-shaped). Their
			// global_classes are merged into `bricks_global_classes`.
			$declared_option = isset( $item['option_name'] ) ? sanitize_key( (string) $item['option_name'] ) : '';
			if ( $this->maybe_install_global_class_settings_json( $declared_option, $body ) ) {
				continue;
			}

			$prev_errors = libxml_use_internal_errors( true );
			$xml         = simplexml_load_string( $body, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NONET );
			libxml_clear_errors();
			libxml_use_internal_errors( $prev_errors );

			if ( ! $xml ) {
				continue;
			}

			// Support both <options><option>…</option></options> and a single <option>…</option>.
			$nodes = isset( $xml->option ) ? $xml->option : [ $xml ];

			foreach ( $nodes as $opt ) {
				$option_name = sanitize_key( (string) $opt->name );
				if ( '' === $option_name ) {
					continue;
				}

				$option_name = $this->map_legacy_option_name( $option_name );

				// Raw XML text: sanitize_text_field() would break the byte-length
				// prefixes of serialized data.
				$raw_value = (string) $opt->value;

				// The template's responsive CSS targets its breakpoints, which
				// Bricks only uses while "customBreakpoints" is on. Only that one
				// flag is read from the global settings here.
				if ( 'bricks_global_settings' === $option_name ) {
					$global = self::decode_option_value( $raw_value );
					if ( is_array( $global ) && ! empty( $global['customBreakpoints'] ) ) {
						$custom_breakpoints = true;
					}
				}

				if ( 'bricks_font_face_rules' === $option_name ) {
					// Custom font CSS: added only when the site has none of its own.
					$css = self::decode_option_value( $raw_value );
					if ( is_string( $css ) && '' !== trim( $css ) && ! get_option( $option_name ) ) {
						update_option( $option_name, wp_strip_all_tags( $css ) );
					}
					continue;
				}

				if ( ! $this->is_design_option( $option_name ) ) {
					$addon_options[] = array(
						'name'  => $option_name,
						'value' => $raw_value,
					);
					continue;
				}

				$value = self::decode_option_value( $raw_value );

				// Design data is always an array; anything else is skipped.
				if ( ! is_array( $value ) ) {
					continue;
				}

				$existing = get_option( $option_name );
				$merged   = $this->merge_bricks_option( $option_name, $existing, $value );

				if ( $merged !== $existing ) {
					update_option( $option_name, $merged );

					if ( 'bricks_breakpoints' === $option_name ) {
						$breakpoints_added = true;
					}
				}
			}
		}

		if ( $breakpoints_added && $custom_breakpoints ) {
			$global = get_option( 'bricks_global_settings', array() );
			$global = is_array( $global ) ? $global : array();

			if ( empty( $global['customBreakpoints'] ) ) {
				$global['customBreakpoints'] = true;
				update_option( 'bricks_global_settings', $global );
			}

			// Bricks regenerates its breakpoint CSS files when this no longer
			// matches the installed version.
			delete_option( 'bricks_breakpoints_last_generated' );
		}

		/**
		 * The template's other options (not Bricks design data), for an add-on.
		 *
		 * The free plugin does not write these. Values are the raw strings from
		 * the options file; an add-on must decide what it allows and decode
		 * them safely.
		 *
		 * @param array<int,array{name:string,value:string}> $addon_options Options from the file.
		 * @param array                                      $template_data Template read from the library.
		 * @param string                                     $item_type     'template' or 'page'.
		 */
		do_action( 'bricksfly/starter-template/import/options', $addon_options, $template_data, $item_type );
	}

	/**
	 * Decode an option value from an options file without ever creating
	 * PHP objects.
	 *
	 * @param string $raw Raw value text.
	 * @return mixed Decoded value, the raw string when not serialized, or null when invalid.
	 */
	public static function decode_option_value( $raw ) {
		if ( ! is_serialized( $raw ) ) {
			return $raw;
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize -- allowed_classes=false: arrays and scalars only, no objects.
		$value = @unserialize( trim( $raw ), array( 'allowed_classes' => false ) );

		if ( false === $value && 'b:0;' !== trim( $raw ) ) {
			return null;
		}

		// Objects (even incomplete ones) are never accepted.
		return self::contains_object( $value ) ? null : $value;
	}

	/**
	 * Whether a decoded value is or contains an object.
	 *
	 * @param mixed $value Decoded value.
	 * @return bool
	 */
	private static function contains_object( $value ) {
		if ( is_object( $value ) ) {
			return true;
		}

		if ( is_array( $value ) ) {
			foreach ( $value as $item ) {
				if ( self::contains_object( $item ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Handle an option source that is a Bricks export JSON (e.g. the
	 * `page_global_class` entry), merging its global classes into the site's
	 * `bricks_global_classes` option.
	 *
	 * The remote file looks like a standard Bricks export:
	 *   { "global_classes": [ { id, name, settings }, … ], "type": "bricks", … }
	 *
	 * @param string $declared_option The option_name declared in the template item.
	 * @param string $body            The downloaded file body.
	 * @return bool   True if the body was handled as global-settings JSON (caller
	 *                should skip the XML path); false to fall through to XML.
	 */
	private function maybe_install_global_class_settings_json( $declared_option, $body ) {
		$is_flagged = ( 'page_global_class' === $declared_option );

		$trimmed    = ltrim( $body );
		$looks_json = ( '' !== $trimmed && ( '{' === $trimmed[0] || '[' === $trimmed[0] ) );

		if ( ! $is_flagged && ! $looks_json ) {
			return false;
		}

		$data = json_decode( $body, true );
		if ( ! is_array( $data ) ) {
			return false; // not JSON — let the XML path try.
		}

		// Accept both the Bricks export key `global_classes` and the camelCase
		// `globalClasses` some payloads use.
		$incoming_classes = array();
		if ( isset( $data['global_classes'] ) && is_array( $data['global_classes'] ) ) {
			$incoming_classes = $data['global_classes'];
		} elseif ( isset( $data['globalClasses'] ) && is_array( $data['globalClasses'] ) ) {
			$incoming_classes = $data['globalClasses'];
		}

		// JSON without global classes: nothing to merge, but not XML either.
		if ( empty( $incoming_classes ) ) {
			return true;
		}

		$merged = $this->merge_bricks_option( 'bricks_global_classes', get_option( 'bricks_global_classes' ), $incoming_classes );
		update_option( 'bricks_global_classes', $merged );
		do_action( 'bricksfly/starter-template/import/step/global_classes', $merged );

		return true;
	}

	/**
	 * Bricks design data the free importer merges into the site: global
	 * classes, variables, colours, theme styles, style manager and
	 * breakpoints (the imported pages' CSS depends on all of them).
	 *
	 * A fixed list. Besides these, only the custom font CSS (when the site has
	 * none) and the `customBreakpoints` flag are written by the free plugin.
	 *
	 * @param string $option_name The option name.
	 * @return bool
	 */
	private function is_design_option( $option_name ) {
		return in_array(
			$option_name,
			array(
				'bricks_global_classes',
				'bricks_global_pseudo_classes',
				'bricks_global_variables',
				'bricks_global_variables_categories',
				'bricks_color_palette',
				'bricks_theme_styles',
				'bricks_style_manager',
				'bricks_breakpoints',
			),
			true
		);
	}

	/**
	 * Translate a pre-rebrand option name in an export to the name this plugin
	 * reads today (`thebrbre_*` → `bricksfly_*`), so add-ons receive the
	 * current names.
	 *
	 * @param string $option_name Option name as it appears in the export.
	 * @return string Current name.
	 */
	private function map_legacy_option_name( $option_name ) {
		$renamed = array(
			'thebrbre_save_extensions' => 'bricksfly_save_extensions',
			'thebrbre_save_widgets'    => 'bricksfly_save_widgets',
			'thebrbre_smooth_scroller' => 'bricksfly_smooth_scroller',
		);

		return isset( $renamed[ $option_name ] ) ? $renamed[ $option_name ] : $option_name;
	}

	/**
	 * Switch on every free widget and extension after a starter template
	 * import, when the user left "Enable all Widgets / Extensions" ticked.
	 *
	 * Only BricksFly's own toggles, only free items, and only ever ON: a
	 * widget or extension the user already enabled or disabled elsewhere
	 * keeps every other setting.
	 *
	 * @return void
	 */
	private function enable_free_features() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Called from template_installer() after check_ajax_referer().
		$widgets    = ! isset( $_POST['enable_widgets'] ) || filter_var( wp_unslash( $_POST['enable_widgets'] ), FILTER_VALIDATE_BOOLEAN );
		$extensions = ! isset( $_POST['enable_extensions'] ) || filter_var( wp_unslash( $_POST['enable_extensions'] ), FILTER_VALIDATE_BOOLEAN );
		// phpcs:enable

		$config = isset( $GLOBALS['bricksfly_config'] ) && is_array( $GLOBALS['bricksfly_config'] ) ? $GLOBALS['bricksfly_config'] : array();

		if ( $widgets && ! empty( $config['widgets'] ) ) {
			$this->enable_free_slugs( $config['widgets'], 'bricksfly_save_widgets' );
		}

		if ( $extensions && ! empty( $config['extensions'] ) ) {
			$this->enable_free_slugs( $config['extensions'], 'bricksfly_save_extensions' );
		}
	}

	/**
	 * Set every free, released slug of a config section to ON in its option.
	 *
	 * @param array  $section     Config section, e.g. `['elements' => [...]]`.
	 * @param string $option_name Toggle option (slug => bool).
	 * @return void
	 */
	private function enable_free_slugs( $section, $option_name ) {
		$slugs = array();

		$walk = function ( $elements ) use ( &$walk, &$slugs ) {
			foreach ( (array) $elements as $slug => $node ) {
				if ( ! is_array( $node ) ) {
					continue;
				}

				if ( empty( $node['is_upcoming'] ) && empty( $node['is_pro'] ) ) {
					$slugs[ $slug ] = true;
				}

				if ( isset( $node['elements'] ) && is_array( $node['elements'] ) ) {
					$walk( $node['elements'] );
				}
			}
		};

		$walk( isset( $section['elements'] ) ? $section['elements'] : array() );

		if ( empty( $slugs ) ) {
			return;
		}

		$existing = get_option( $option_name, array() );
		$existing = is_array( $existing ) ? $existing : array();

		update_option( $option_name, array_merge( $existing, $slugs ) );
	}

	/**
	 * Merge a Bricks global option with the value coming from the template
	 * import, preserving user-defined data on conflict.
	 *
	 * - List-shaped options (`bricks_global_classes`, `bricks_global_variables`,
	 *   `bricks_color_palette`): array of items each keyed by an `id` field.
	 *   We union by id, keeping existing items when ids collide so the user's
	 *   tweaks survive, and append new items from the template at the end.
	 *
	 * - Associative-shaped options (`bricks_theme_styles`,
	 *   `bricks_global_settings`): nested keyed maps. We deep-merge with
	 *   existing values winning on conflict (the import only adds keys the
	 *   user doesn't already have).
	 *
	 * @param string $option_name The option name.
	 * @param mixed  $existing    Current option value (may be empty / non-array).
	 * @param mixed  $incoming    Imported value parsed from the template XML.
	 * @return mixed Merged value to pass to update_option().
	 */
	private function merge_bricks_option( $option_name, $existing, $incoming ) {
		// Nothing usable came in — keep what the site already has. Checked BEFORE
		// the $existing test on purpose: an empty <value> in the export arrives
		// here as '' (a string), and on a fresh site $existing is `false`, so the
		// old order returned that '' straight back to update_option().
		if ( ! is_array( $incoming ) ) {
			return is_array( $existing ) ? $existing : array();
		}

		// First-time import (option missing or wrong shape) — nothing to merge.
		if ( ! is_array( $existing ) || empty( $existing ) ) {
			return $incoming;
		}

		// Associative settings maps — preserve user values, add missing keys
		// from the import.
		if ( in_array( $option_name, array( 'bricks_theme_styles', 'bricks_style_manager' ), true ) ) {
			return array_replace_recursive( $incoming, $existing );
		}

		// Lists keyed by `id` (breakpoints by `key`) — union, keep existing on conflict.
		$id_key = 'bricks_breakpoints' === $option_name ? 'key' : 'id';
		$by_id  = array();
		foreach ( $existing as $item ) {
			if ( is_array( $item ) && isset( $item[ $id_key ] ) ) {
				$by_id[ $item[ $id_key ] ] = $item;
			}
		}
		foreach ( $incoming as $item ) {
			if ( is_array( $item ) && isset( $item[ $id_key ] ) && ! isset( $by_id[ $item[ $id_key ] ] ) ) {
				$by_id[ $item[ $id_key ] ] = $item;
			}
		}

		return array_values( $by_id );
	}

}

BRICKSFLY_Template_Importer::instance();
