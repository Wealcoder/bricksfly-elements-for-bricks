<?php

namespace AABAddons\Admin\Pages;

use function WPML\PHP\Logger\error;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class AAB_Template_Importer {

	public $file_path = 'aab_tpl_file.xml';
	public $full_path = null;
	public $wishlist_key = 'aaeaddon_user_wishlists';

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action( 'wp_ajax_aaeaddon_template_installer', [ $this, 'template_installer' ] );
		add_action( 'wp_ajax_aaeaddon_heartbeat_data', [ $this, 'heartbeat_data' ] );
		add_action( 'wp_ajax_aaeaddon_wishlist_option', [ $this, 'wishlist' ] );
		add_action( 'wp_ajax_aaeaddon_upload_manual_import_file', [ $this, 'template_installer' ] );
		add_action( 'wp_ajax_aaeaddon_template_dependency_status', [ $this, 'template_dependency_status' ] );
		add_action( 'wp_ajax_aae_lite_get_latest_imported_pages', [ $this, 'get_latest_imported_pages' ] );
		add_filter( 'wcf_addons_dashboard_config', [ $this, 'include_user_wishlist' ] );
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
		check_ajax_referer( 'aab_admin_nonce', 'nonce' );
		$return_data = apply_filters( 'aaeaddon_heartbeat_data', [
			'import_state'   => get_option( 'aaeaddon_template_import_state' ),
			'import_porgress' => get_option( 'aaeaddon_template_import_progress' ),
		] );
		wp_send_json( $return_data );
	}

	public function wishlist() {
		check_ajax_referer( 'aab_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action.', 'bricksfly' ) );
		}

		if ( ! isset( $_POST['wishlist'] ) ) {
			wp_send_json_error( __( 'Provide wishlist data.', 'bricksfly' ) );
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
		check_ajax_referer( 'aab_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( __( 'You are not allowed to perform this action.', 'bricksfly' ) );
		}

		$dependencies = [];
		if ( isset( $_POST['dependencies'] ) ) {
			$json_data    = sanitize_text_field( wp_unslash( $_POST['dependencies'] ) );
			$dependencies = json_decode( $json_data, true );
		}

		if ( empty( $dependencies ) ) {
			wp_send_json_error( __( 'No dependencies provided.', 'bricksfly' ) );
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
						? __( 'Active', 'bricksfly' )
						: __( 'Installed', 'bricksfly' );
				} else {
					$theme['status'] = __( 'Not Installed', 'bricksfly' );
				}
			}
		}

		wp_send_json_success( [ 'dependencies' => $dependencies ] );
	}

	public function get_latest_imported_pages() {
		check_ajax_referer( 'aab_admin_nonce', 'nonce' );

		$per_page = isset( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 5;

		$pages = get_posts( [
			'post_type'   => 'page',
			'post_status' => 'publish',
			'numberposts' => $per_page,
			'orderby'     => 'date',
			'order'       => 'DESC',
			'meta_key'    => 'aae_imported',
			'meta_value'  => '1',
		] );

		$result = [];
		foreach ( $pages as $page ) {
			$result[] = [
				'id'        => $page->ID,
				'title'     => $page->post_title,
				'permalink' => get_permalink( $page->ID ),
			];
		}

		wp_send_json_success( [ 'pages' => $result ] );
	}

	public function template_installer() {
		check_ajax_referer( 'aab_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( __( 'You are not allowed to do this action', 'bricksfly' ) );
		}

		$progress      = '25';
		$msg           = '';
		$template_data = [];
		$theme_slug    = $user_plugins = null;

		if ( isset( $_POST['theme_slug'] ) ) {
			$theme_slug = sanitize_text_field( wp_unslash( $_POST['theme_slug'] ) );
		}
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
						if ( current_user_can( 'install_plugins' ) ) {
							foreach ( $template_data['dependencies']['plugins'] as $item ) {
								if ( file_exists( WP_PLUGIN_DIR . '/' . $item['Base_Slug'] ) ) {
									activate_plugin( $item['Base_Slug'], '', false, false );
								} else {
									if ( in_array( $item['slug'], $user_plugins ) ) {
										update_option(
											'aaeaddon_template_import_state',
											/* translators: %s: plugin name being installed. */
											sprintf( __( 'Installing %s', 'bricksfly' ), $item['name'] )
										);
										if ( isset( $item['host'] ) && isset( $item['slug'] ) ) {
											$this->install_plugin_from_wp( $item['slug'] );
										}
									}
								}
							}
						}
						update_option( 'aaeaddon_template_import_state', __( 'Plugin Installation Done', 'bricksfly' ) );
					}
				}
				$template_data['next_step'] = 'install-wp-options';

			} elseif ( $next_step === 'check-template-status' ) {
				$tpl = $this->validate_download_file( $template_data );
				if ( $tpl ) {
					update_option( 'aaeaddon_template_import_state', __( 'Content file Downloading', 'bricksfly' ) );
					$template_data['next_step'] = 'download-xml-file';
					$template_data['file']      = json_decode( $tpl );
				} else {
					update_option( 'aaeaddon_template_import_state', __( 'Invalid file', 'bricksfly' ) );
					$template_data['next_step'] = 'fail';
				}
				$progress = '37';

			} elseif ( $next_step === 'download-xml-file' ) {
				if ( isset( $template_data['file']['content_url'] ) ) {
					update_option( 'aaeaddon_template_import_state', __( 'Content installing', 'bricksfly' ) );
					$template_data['next_step']  = 'install-template';
					$template_data['local_path'] = $this->full_path;
				} else {
					$template_data['next_step'] = 'fail';
					update_option( 'aaeaddon_template_import_state', __( 'Missing Content file, contact author', 'bricksfly' ) );
				}
				$progress = '40';

			} elseif ( $next_step === 'install-template' ) {
				$template_data['next_step'] = 'check-theme';
				$progress                   = '50';
				$msg                        = __( 'Verifying Content Import', 'bricksfly' );
				update_option( 'aaeaddon_template_import_state', __( 'Checking Theme', 'bricksfly' ) );

			} elseif ( $next_step === 'check-theme' ) {
				if ( $theme_slug ) {
					$template_data['next_step'] = 'install-theme';
					$progress                   = '75';
					update_option( 'aaeaddon_template_import_state', __( 'Installing Theme', 'bricksfly' ) );
				} else {
					$template_data['next_step'] = 'install-bricks-settings';
				}

			} elseif ( $next_step === 'install-theme' ) {
				$template_data['next_step'] = 'install-bricks-settings';
				$progress                   = '80';	

			} elseif ( $next_step === 'install-bricks-settings' || $next_step === 'install-elementor-settings' ) {
				$template_data['next_step'] = 'done';
				$progress                   = '100';						

				$this->update_blog_and_homepage_options( $template_data );
				do_action( 'aab/starter-template/import/step/metasettings' );

			} elseif ( $next_step === 'install-wp-options' ) {
				$template_data['next_step'] = 'check-template-status';
				$progress                   = '30';
				$msg                        = __( 'Downloading Template', 'bricksfly' );

				if ( isset( $template_data['wp_options'] ) && is_array( $template_data['wp_options'] ) ) {
					$this->install_options( $template_data['wp_options'] );
				}

				$import_type = isset( $_POST['import_type'] ) ? sanitize_text_field( wp_unslash( $_POST['import_type'] ) ) : 'full-demo';

				if ( $import_type !== 'page' ) {
					do_action( 'aab/starter-template/import/step/wp_options' );
				}

				update_option( 'aaeaddon_template_import_state', $msg );

			} elseif ( $next_step === 'fail' ) {
				$msg = __( 'Template Demo Import fail', 'bricksfly' );

			} else {
				$template_data['next_step'] = 'plugins-importer';
				$progress                   = '10';
				update_option( 'aaeaddon_template_import_state', __( 'Checking Setup requirement', 'bricksfly' ) );
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

			$xml_data = wp_remote_retrieve_body( $response );
			if ( empty( $xml_data ) ) {
				continue;
			}

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
		$mergeable = apply_filters( 'aab_bricks_mergeable_options', $mergeable );

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
			update_option( 'aaeaddon_template_import_state', __( 'Template Required', 'bricksfly' ) );
			return false;
		}

		$remote_url = AAB_TEMPLATE_STARTER_BASE_URL . 'wp-json/brk-starter-templates/download';

		if ( ! empty( $template['base_path'] ) ) {
			$remote_url = $template['base_path'] . 'wp-json/brk-starter-templates/download';
		}

		$args = [
			'timeout'   => 90,
			'body'      => [ 'template' => $template ],
			'sslverify' => false,
		];

		$response = wp_remote_get( $remote_url, $args );

		if ( is_wp_error( $response ) ) {
			update_option( 'aaeaddon_template_import_state', __( 'Failed to validate file from remote URL.', 'bricksfly' ) );
			return false;
		}

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			update_option( 'aaeaddon_template_import_state', __( 'Invalid file arguments.', 'bricksfly' ) );
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		if ( empty( $body ) ) {
			update_option( 'aaeaddon_template_import_state', __( 'The downloadable file is empty.', 'bricksfly' ) );
			return false;
		}

		return $body;
	}

	private function install_theme( $slug ) {
		if ( empty( $slug ) ) {
			return __( 'No theme specified.', 'bricksfly' );
		}

		$theme_slug = sanitize_key( $slug );
		$theme_data = wp_get_theme( $theme_slug );

		if ( $theme_data->exists() ) {
			switch_theme( $theme_slug );
			$msg = sprintf(
				/* translators: %s: human-readable theme name. */
				__( 'Theme "%s" activated successfully.', 'bricksfly' ),
				$theme_data->get( 'Name' )
			);
			update_option( 'aaeaddon_template_import_state', $msg );
			return $msg;
		}

		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/theme.php';

		$api = themes_api( 'theme_information', [
			'slug'   => $theme_slug,
			'fields' => [ 'sections' => false ],
		] );

		if ( is_wp_error( $api ) ) {
			return $api->get_error_message();
		}

		$upgrader = new \Theme_Upgrader( new \WP_Ajax_Upgrader_Skin() );
		$result   = $upgrader->install( $api->download_link );

		if ( is_wp_error( $result ) ) {
			update_option( 'aaeaddon_template_import_state', $result->get_error_message() );
			return $result->get_error_message();
		}

		$theme_data = wp_get_theme( $theme_slug );
		if ( $theme_data->exists() ) {
			switch_theme( $theme_slug );
		}

		$msg = sprintf(
			/* translators: %s: theme slug that was installed and activated. */
			__( 'Theme "%s" installed and activated.', 'bricksfly' ),
			$theme_slug
		);
		update_option( 'aaeaddon_template_import_state', $msg );
		return $msg;
	}

	private function install_plugin_from_wp( $slug ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		$api = plugins_api( 'plugin_information', [
			'slug'   => $slug,
			'fields' => [ 'sections' => false ],
		] );

		if ( is_wp_error( $api ) ) {
			return false;
		}

		$upgrader = new \Plugin_Upgrader( new \WP_Ajax_Upgrader_Skin() );
		$result   = $upgrader->install( $api->download_link );

		if ( ! is_wp_error( $result ) ) {
			$plugin_file = $upgrader->plugin_info();
			if ( $plugin_file ) {
				activate_plugin( $plugin_file );
			}
		}

		return $result;
	}
}

AAB_Template_Importer::instance();
