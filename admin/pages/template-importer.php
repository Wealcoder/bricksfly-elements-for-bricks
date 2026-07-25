<?php

namespace wealcoder\thebricksfly\Admin\Pages;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class THEBRBRE_Template_Importer {

	public $file_path = 'thebrbre_tpl_file.xml';
	public $full_path = null;
	public $wishlist_key = 'thebrbre_user_wishlists';

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action( 'wp_ajax_thebrbre_template_installer', [ $this, 'template_installer' ] );
		add_action( 'wp_ajax_thebrbre_heartbeat_data', [ $this, 'heartbeat_data' ] );
		add_action( 'wp_ajax_thebrbre_wishlist_option', [ $this, 'wishlist' ] );
		add_action( 'wp_ajax_thebrbre_upload_manual_import_file', [ $this, 'template_installer' ] );
		add_action( 'wp_ajax_thebrbre_template_dependency_status', [ $this, 'template_dependency_status' ] );
		// NOTE: the 'thebrbre_get_latest_imported_pages' AJAX action is handled by
		// OneClickImport::aae_get_latest_imported_pages() (admin/st-init.php).
		// That handler is batch-aware — it returns the page(s) from the most
		// recent import via the 'thebrbre_last_import_batch' option, which is what the
		// "Go to page" button on the Complete Import step needs. A second callback
		// here on the same action raced the correct one (whichever fired first
		// won and called wp_die) and queried by date DESC — which returns the
		// wrong page because WXR preserves each page's original post_date. Removed.
		add_filter('thebrbre_dashboard_config', [ $this, 'include_user_wishlist' ] );
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
		check_ajax_referer( 'thebrbre_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action.', 'the-bricksfly' ) );
		}

		$return_data = apply_filters('thebrbre_heartbeat_data', [
			'import_state'   => get_option( 'thebrbre_template_import_state' ),
			'import_porgress' => get_option( 'thebrbre_template_import_progress' ),
		] );
		wp_send_json( $return_data );
	}

	public function wishlist() {
		check_ajax_referer( 'thebrbre_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action.', 'the-bricksfly' ) );
		}

		if ( ! isset( $_POST['wishlist'] ) ) {
			wp_send_json_error( __( 'Provide wishlist data.', 'the-bricksfly' ) );
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
		check_ajax_referer( 'thebrbre_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action.', 'the-bricksfly' ) );
		}

		$dependencies = [];
		if ( isset( $_POST['dependencies'] ) ) {
			$json_data    = sanitize_text_field( wp_unslash( $_POST['dependencies'] ) );
			$dependencies = json_decode( $json_data, true );
		}

		if ( empty( $dependencies ) ) {
			wp_send_json_error( __( 'No dependencies provided.', 'the-bricksfly' ) );
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		// Check plugin statuses
		if ( ! empty( $dependencies['plugins'] ) && is_array( $dependencies['plugins'] ) ) {
			foreach ( $dependencies['plugins'] as &$plugin ) {
				$base_slug = $plugin['Base_Slug'] ?? '';
				if ( $base_slug && file_exists( WP_PLUGIN_DIR . '/' . $base_slug ) ) {
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
						? __( 'Active', 'the-bricksfly' )
						: __( 'Installed', 'the-bricksfly' );
				} else {
					$theme['status'] = __( 'Not Installed', 'the-bricksfly' );
				}
			}
		}

		wp_send_json_success( [ 'dependencies' => $dependencies ] );
	}

	/**
	 * Enforce a license limitation on an import AJAX request.
	 *
	 * Sends a JSON error and halts (wp_send_json) when the feature is not
	 * allowed for the active license. The response shape lets the client
	 * distinguish a license block from a generic failure:
	 *   { success:false, limited:true, feature:<key>, message:<string> }
	 *
	 * Fail-open only if the free plugin's helper is somehow missing (should
	 * never happen — it's loaded before the admin pages), to avoid hard-
	 * breaking imports on a partial deploy.
	 *
	 * @param string $feature Feature key (e.g. 'starter_tpl_import').
	 * @return void
	 */
	public static function guard_import_feature( $feature ) {
		if ( ! function_exists( 'thebrbre_is_feature_allowed' ) ) {
			return;
		}

		if ( thebrbre_is_feature_allowed( $feature ) ) {
			return;
		}

		$message = function_exists( 'thebrbre_feature_denied_message' )
			? thebrbre_feature_denied_message( $feature )
			: __( 'This feature is not included in your current license plan.', 'the-bricksfly' );

		wp_send_json( array(
			'success'  => false,
			'limited'  => true,
			'feature'  => $feature,
			'message'  => $message,
			// Mirror the importer's own failure envelope so any client path
			// that only checks `next_step`/`progress` still stops cleanly.
			'progress' => 0,
			'template' => array( 'next_step' => 'fail' ),
		) );
	}

	public function template_installer() {
		check_ajax_referer( 'thebrbre_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( __( 'You are not allowed to do this action', 'the-bricksfly' ) );
		}

		// License limitation gate. Page import (import_type=page) requires the
		// `starter_page_import` flag; every other import type (full-demo /
		// starter template) requires `starter_tpl_import`. Enforced here so a
		// forged AJAX request can't bypass the React UI's lock. The gate returns
		// `limited:true` + the feature key so the client can show the upsell
		// popup instead of a generic failure.
		$import_type = isset( $_POST['import_type'] ) ? sanitize_text_field( wp_unslash( $_POST['import_type'] ) ) : 'full-demo';
		$feature     = ( 'page' === $import_type ) ? 'starter_page_import' : 'starter_tpl_import';
		self::guard_import_feature( $feature );

		$progress      = '25';
		$msg           = '';
		$template_data = [];
		$user_plugins  = null;

		if ( isset( $_POST['user_plugins'] ) ) {
			$user_plugins = sanitize_text_field( wp_unslash( $_POST['user_plugins'] ) );
			$user_plugins = explode( ',', $user_plugins );
		}

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

			$next_step = $template_data['next_step'] ?? '';

			if ( $next_step === 'plugins-importer' ) {
				$progress = '20';
				require_once ABSPATH . 'wp-admin/includes/plugin.php';

				if ( is_array( $user_plugins ) && $user_plugins ) {
					if ( isset( $template_data['dependencies']['plugins'] ) && is_array( $template_data['dependencies']['plugins'] ) ) {
						if ( current_user_can( 'activate_plugins' ) ) {
							foreach ( $template_data['dependencies']['plugins'] as $item ) {
								if ( file_exists( WP_PLUGIN_DIR . '/' . $item['Base_Slug'] ) ) {
									// Only activate dependency plugins that are already
									// installed. Automatic installation was removed; a
									// required plugin that is not present must be
									// installed manually by the administrator.
									activate_plugin( $item['Base_Slug'], '', false, false );
								} else {
									if ( in_array( $item['slug'], $user_plugins ) ) {
										update_option(
											'thebrbre_template_import_state',
											/* translators: %s: plugin name being installed. */
											sprintf( __( 'Installing %s', 'the-bricksfly' ), $item['name'] )
										);
										if ( isset( $item['host'] ) && isset( $item['slug'] ) ) {
											$this->install_plugin_from_wp( $item['slug'] );
										}
									}
								}
							}
						}
						update_option( 'thebrbre_template_import_state', __( 'Plugin Installation Done', 'the-bricksfly' ) );
					}
				}
				$template_data['next_step'] = 'install-wp-options';

			} elseif ( $next_step === 'check-template-status' ) {
				$tpl = $this->validate_download_file( $template_data );
				if ( $tpl ) {
					update_option( 'thebrbre_template_import_state', __( 'Content file Downloading', 'the-bricksfly' ) );
					$template_data['next_step'] = 'download-xml-file';
					$template_data['file']      = json_decode( $tpl );
				} else {
					update_option( 'thebrbre_template_import_state', __( 'Invalid file', 'the-bricksfly' ) );
					$template_data['next_step'] = 'fail';
				}
				$progress = '37';

			} elseif ( $next_step === 'download-xml-file' ) {
				if ( isset( $template_data['file']['content_url'] ) ) {
					update_option( 'thebrbre_template_import_state', __( 'Content installing', 'the-bricksfly' ) );
					$template_data['next_step']  = 'install-template';
					$template_data['local_path'] = $this->full_path;
				} else {
					$template_data['next_step'] = 'fail';
					update_option( 'thebrbre_template_import_state', __( 'Missing Content file, contact author', 'the-bricksfly' ) );
				}
				$progress = '40';

			} elseif ( $next_step === 'install-template' ) {
				$template_data['next_step'] = 'check-theme';
				$progress                   = '50';
				$msg                        = __( 'Verifying Content Import', 'the-bricksfly' );
				update_option( 'thebrbre_template_import_state', __( 'Checking Theme', 'the-bricksfly' ) );

			} elseif ( $next_step === 'check-theme' ) {
				if ( $theme_slug ) {
					$template_data['next_step'] = 'install-theme';
					$progress                   = '75';
					update_option( 'thebrbre_template_import_state', __( 'Installing Theme', 'the-bricksfly' ) );
				} else {
					$template_data['next_step'] = 'install-bricks-settings';
				}

			} elseif ( $next_step === 'install-theme' ) {
				$template_data['next_step'] = 'install-bricks-settings';
				$progress                   = '75';
				$msg                        = __( 'Verifying Content Import', 'the-bricksfly' );
				update_option( 'thebrbre_template_import_state', __( 'Verifying Content Import', 'the-bricksfly' ) );

			} elseif ( $next_step === 'install-bricks-settings' ) {
				$template_data['next_step'] = 'done';
				$progress                   = '100';						

				$this->update_blog_and_homepage_options( $template_data );
				do_action( 'thebrbre/starter-template/import/step/metasettings' );

			} elseif ( $next_step === 'install-wp-options' ) {
				$template_data['next_step'] = 'check-template-status';
				$progress                   = '30';
				$msg                        = __( 'Downloading Template', 'the-bricksfly' );

				if ( isset( $template_data['wp_options'] ) && is_array( $template_data['wp_options'] ) ) {
					$this->install_options( $template_data['wp_options'] );
				}

				$import_type = isset( $_POST['import_type'] ) ? sanitize_text_field( wp_unslash( $_POST['import_type'] ) ) : 'full-demo';

				if ( $import_type !== 'page' ) {
					do_action( 'thebrbre/starter-template/import/step/wp_options' );
				}

				update_option( 'thebrbre_template_import_state', $msg );

			} elseif ( $next_step === 'fail' ) {
				$msg = __( 'Template Demo Import fail', 'the-bricksfly' );

			} else {
				$template_data['next_step'] = 'plugins-importer';
				$progress                   = '10';
				update_option( 'thebrbre_template_import_state', __( 'Checking Setup requirement', 'the-bricksfly' ) );
			}
		}

		wp_send_json( [
			'template' => wp_unslash( $template_data ),
			'msg'      => $msg,
			'progress' => $progress,
		] );
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

	private function install_options( $settings ) {
		foreach ( $settings as $item ) {
			if ( empty( $item['xml_file'] ) ) {
				continue;
			}

			$response = wp_remote_get( $item['xml_file'], [ 'timeout' => 60 ] );

			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				continue;
			}

			$body = wp_remote_retrieve_body( $response );
			if ( empty( $body ) ) {
				continue;
			}

			// Some option sources are Bricks export JSON files rather than WP
			// options XML. The template entry flags these with an option_name like
			// `page_global_class`, and the file is a Bricks export:
			//   { "global_classes": [ { id, name, settings }, … ], "type":"bricks", … }
			// In that case we don't have <name>/<value> nodes — instead we take the
			// export's global_classes and MERGE them into `bricks_global_classes`
			// (dedup by id, keep existing on conflict), the same merge used for the
			// XML path. Detect by the declared option_name OR by the JSON shape.
			$declared_option = isset( $item['option_name'] ) ? sanitize_key( (string) $item['option_name'] ) : '';
			if ( $this->maybe_install_global_class_settings_json( $declared_option, $body ) ) {
				continue;
			}

			$xml_data = $body;

			$prev_errors = libxml_use_internal_errors( true );
			$xml         = simplexml_load_string( $xml_data, 'SimpleXMLElement', LIBXML_NOCDATA );
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

				// Use the raw XML text as-is. sanitize_text_field() strips newlines/tabs and
				// breaks the byte-length prefixes in PHP-serialized data (e.g. s:15:"…"),
				// which silently corrupts ACF repeater rows on unserialize.
				$raw_value = (string) $opt->value;
				$value     = maybe_unserialize( $raw_value );

				// For Bricks global data (classes, variables, color palettes, theme
				// styles, global settings), merge with existing values instead of
				// replacing. Existing pages reference these by ID — a plain
				// update_option() would wipe out the user's classes/variables and
				// break every page that referenced them.
				if ( $this->is_bricks_mergeable_option( $option_name ) ) {
					$existing = get_option( $option_name );
					$value    = $this->merge_bricks_option( $option_name, $existing, $value );
				}

				// update_option() re-serializes arrays/objects and inserts the row if
				// missing — required for fresh imports where ACF repeater sub-rows
				// (name_0_subfield, _name_0_subfield, …) do not yet exist.
				update_option( $option_name, $value );
			}
		}
	}

	/**
	 * Handle an option source that is a Bricks export JSON (e.g. the
	 * `page_global_class` entry), merging its global classes into the site's
	 * `bricks_global_classes` option.
	 *
	 * The remote file looks like a standard Bricks export:
	 *   { "global_classes": [ { id, name, settings }, … ], "type": "bricks", … }
	 *
	 * We extract `global_classes` and union it into `bricks_global_classes` with
	 * the same id-keyed merge used for the XML option path (existing entries win
	 * on conflict, new classes are appended) so pages that reference these class
	 * ids resolve their CSS, without clobbering the user's own classes.
	 *
	 * @param string $declared_option The option_name declared in the template item.
	 * @param string $body            The downloaded file body.
	 * @return bool   True if the body was handled as global-settings JSON (caller
	 *                should skip the XML path); false to fall through to XML.
	 */
	private function maybe_install_global_class_settings_json( $declared_option, $body ) {
		// Only treat this as JSON global-settings when the template flags it, OR
		// when the body is clearly a Bricks export carrying global_classes. This
		// keeps every existing XML options file on the untouched XML path.
		$is_flagged = ( 'page_global_class' === $declared_option );

		$trimmed = ltrim( $body );
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

		// If it's JSON but has no global classes, there's nothing to merge — but
		// it's still not an XML options file, so consider it handled (skip XML).
		if ( empty( $incoming_classes ) ) {
			return $is_flagged || $looks_json;
		}

		$existing = get_option( 'bricks_global_classes' );
		$merged   = $this->merge_bricks_option( 'bricks_global_classes', $existing, $incoming_classes );
		update_option( 'bricks_global_classes', $merged );

		return true;
	}

	/**
	 * True if the option holds Bricks Builder global data that should be
	 * merged (not replaced) on template import, so existing pages keep
	 * resolving the classes / variables / colors / theme styles they
	 * reference by ID.
	 *
	 * @param string $option_name The option name.
	 * @return bool
	 */
	private function is_bricks_mergeable_option( $option_name ) {
		$mergeable = array(
			'bricks_global_classes',
			'bricks_global_variables',
			'bricks_color_palette',
			'bricks_theme_styles',
			'bricks_global_settings',
			'bricks_global_pseudo_classes',
		);

		/**
		 * Filter the list of Bricks options that should be merged on import.
		 * Useful if a future Bricks version adds another global option or a
		 * site uses a custom one with the same merge semantics.
		 */
		$mergeable = apply_filters('thebrbre_bricks_mergeable_options', $mergeable );

		return in_array( $option_name, $mergeable, true );
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
		// First-time import (option missing or wrong shape) — nothing to merge.
		if ( ! is_array( $existing ) || empty( $existing ) ) {
			return $incoming;
		}
		if ( ! is_array( $incoming ) ) {
			return $existing;
		}

		// Associative settings maps — preserve user values, add missing keys
		// from the import.
		if ( in_array( $option_name, array( 'bricks_global_settings', 'bricks_theme_styles' ), true ) ) {
			return array_replace_recursive( $incoming, $existing );
		}

		// Lists keyed by `id` — union by id, keep existing on conflict.
		$by_id = array();
		foreach ( $existing as $item ) {
			if ( is_array( $item ) && isset( $item['id'] ) ) {
				$by_id[ $item['id'] ] = $item;
			}
		}
		foreach ( $incoming as $item ) {
			if ( is_array( $item ) && isset( $item['id'] ) && ! isset( $by_id[ $item['id'] ] ) ) {
				$by_id[ $item['id'] ] = $item;
			}
		}

		return array_values( $by_id );
	}


	private function validate_download_file( $template ) {
		if ( empty( $template ) ) {
			update_option( 'thebrbre_template_import_state', __( 'Template Required', 'the-bricksfly' ) );
			return false;
		}

		$remote_url = THEBRBRE_TEMPLATE_STARTER_BASE_URL . 'wp-json/brk-starter-templates/download';

		if ( ! empty( $template['base_path'] ) ) {
			$remote_url = $template['base_path'] . 'wp-json/brk-starter-templates/download';
		}

		$args = [
			'timeout'   => 90,
			'body'      => [ 'template' => $template ],
			'sslverify' => true,
		];

		$response = wp_remote_get( $remote_url, $args );

		if ( is_wp_error( $response ) ) {
			update_option( 'thebrbre_template_import_state', __( 'Failed to validate file from remote URL.', 'the-bricksfly' ) );
			return false;
		}

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			update_option( 'thebrbre_template_import_state', __( 'Invalid file arguments.', 'the-bricksfly' ) );
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		if ( empty( $body ) ) {
			update_option( 'thebrbre_template_import_state', __( 'The downloadable file is empty.', 'the-bricksfly' ) );
			return false;
		}

		return $body;
	}

}

THEBRBRE_Template_Importer::instance();
