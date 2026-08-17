<?php

namespace wealcoder\bricksfly\Admin\Pages;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class BRICKSFLY_Page_Importer {

	const HANDLE = 'bricksfly-page-import';

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_menu' ], 25 );
		add_action( 'admin_enqueue_scripts', [ $this, 'importer_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_page_list_button' ] );
		add_action( 'admin_print_scripts', [ $this, 'clear_notices_for_importer' ] );
		add_filter( 'admin_body_class', [ $this, 'admin_classes' ], 100 );
		add_filter( 'views_edit-page', [ $this, 'custom_page_tab' ] );
		add_action( 'pre_get_posts', [ $this, 'custom_page_filter' ] );
	}

	/**
	 * Inject an "Import Page" button next to "Add Page" on the Pages list
	 * screen (edit.php?post_type=page). Loads a small vanilla JS file that
	 * appends the button after the core .page-title-action element and links
	 * it to the Bricksfly page importer.
	 */
	public function enqueue_page_list_button() {
		if ( ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || $screen->post_type !== 'page' || $screen->base !== 'edit' ) {
			return;
		}

		wp_enqueue_script(
			'aab-admin-actions',
			BRICKSFLY_URL . 'public/js/aab-admin-actions.js',
			[],
			BRICKSFLY_VERSION,
			true
		);

		wp_localize_script(
			'aab-admin-actions',
			'BRICKSFLY_PAGE_IMPORT',
			[
				'page_url' => esc_url( admin_url( 'admin.php?page=bricksfly-page-importer' ) ),
				'logo'     => esc_url( BRICKSFLY_URL . 'public/images/plugin_logo.png' ),
			]
		);
	}

	public function custom_page_tab( $views ) {
		// Cached (5 min) rather than a raw direct query on every Pages-list
		// view: this count only feeds a UI badge, so brief staleness is fine,
		// and it's invalidated immediately on a fresh import anyway (see
		// st-init.php's write of the 'bricksfly_imported' meta key).
		$count = get_transient( 'bricksfly_imported_page_count' );

		if ( false === $count ) {
			$query = new \WP_Query( [
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'meta_key'       => 'bricksfly_imported',
				'meta_value'     => 1,
				'posts_per_page' => -1,
				'no_found_rows'  => true,
				'fields'         => 'ids',
			] );
			$count = count( $query->posts );
			set_transient( 'bricksfly_imported_page_count', $count, 5 * MINUTE_IN_SECONDS );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for view display only, not processing form data.
		$latest_import             = isset( $_GET['aae-latest-import'] ) ? sanitize_key( wp_unslash( $_GET['aae-latest-import'] ) ) : '';
		$class                     = 'import' === $latest_import ? 'current' : '';
		$url                       = add_query_arg( 'aae-latest-import', 'import', admin_url( 'edit.php?post_type=page' ) );
		$views['latest-import']    = "<a href='" . esc_url( $url ) . "' class='" . esc_attr( $class ) . "' style='color: #fc6848; font-weight: 500'>" . esc_html__( 'AAB Imported', 'bricksfly-elements-for-bricks' ) . " <span class='count'>(" . (int) $count . ")</span></a>";

		return $views;
	}

	public function custom_page_filter( $query ) {
		global $pagenow;

		if ( is_admin() && $pagenow === 'edit.php' && $query->get( 'post_type' ) === 'page' ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for query filtering only, not processing form data.
			$latest_import = isset( $_GET['aae-latest-import'] ) ? sanitize_key( wp_unslash( $_GET['aae-latest-import'] ) ) : '';
			if ( 'import' === $latest_import ) {
				$query->set( 'meta_key', 'bricksfly_imported' );
				$query->set( 'meta_value', '1' );
			}
		}
	}

	public function clear_notices_for_importer() {
		$screen = get_current_screen();
		if ( $screen && strpos( $screen->id, '_page_bricksfly-page-importer' ) !== false ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	public function admin_classes( $classes ) {
		$screen = get_current_screen();
		if ( ! is_string( $classes ) ) {
			$classes = '';
		}
		if ( $screen && strpos( $screen->id, '_page_bricksfly-page-importer' ) !== false ) {
			$classes .= ' wcf-anim2024';
		}
		return $classes;
	}

	public function add_menu() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		add_submenu_page(
			\wealcoder\bricksfly\Admin\Pages\BRICKSFLY_Admin_Init::MENU_PAGE_SLUG,
			__( 'Page Import', 'bricksfly-elements-for-bricks' ),
			__( 'Page Import', 'bricksfly-elements-for-bricks' ),
			'manage_options',
			'bricksfly-page-importer',
			[ $this, 'page_html' ]
		);
	}

	public function page_html() {
		echo '<div id="bricksfly-page-importer"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function importer_assets( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		if ( strpos( $screen->id, '_page_bricksfly-page-importer' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'bricksfly-page-importer-admin',
			BRICKSFLY_URL . 'public/build/admin/page-import.css',
			[],
			time()
		);

		wp_enqueue_script(
			'bricksfly-page-importer-admin',
			BRICKSFLY_URL . 'public/build/admin/page-import.js',
			[ 'wp-element' ],
			time(),
			true
		);

		// Enrich the config with the CURRENT license state so Pro page templates
		// unlock the moment a license is activated â€” no manual refresh needed.
		//
		// The bug this fixes: the Page Importer used to localize a bare
		// `addons_config` with no license fields, so the React app's Pro gate
		// (TemplateShow.jsx: `activated?.product_status?.item_id === 39996 `) never
		// saw the active license and kept every Pro template locked. The
		// Dashboard already enriches its config this way; we mirror it here so
		// BOTH pages read the SAME single source of truth.
		//
		// Reading the options here (on every importer page load) means the state
		// is always freshly fetched â€” never a stale cached value â€” so activation
		// done elsewhere is reflected on the next load of this page.
		$addons_config = apply_filters('bricksfly_dashboard_config', $GLOBALS['bricksfly_config'] ?? [] );

		$license_status = (string) get_option( 'bricksfly_license_status', '' );
		$license_key    = (string) get_option( 'bricksfly_license_key', '' );

		// Valid only when the Pro plugin folder exists AND the stored status is
		// "valid" â€” the same combined check used by bricksfly_is_license_valid() and
		// the Dashboard, so deleting the Pro folder relocks Pro instantly.
		$pro_installed = function_exists( 'bricksfly_is_pro_installed' ) ? bricksfly_is_pro_installed() : false;
		$license_valid = $pro_installed && ( 'valid' === $license_status );

		$addons_config['sl_lic']    = $license_key;
		$addons_config['is_pro']    = $pro_installed;
		$addons_config['bricksfly_valid'] = $license_valid;

		// The compiled React UI unlocks Pro items on `product_status.item_id === 39996 `.
		// Send 39996 only when the license is valid (mirrors the Dashboard); the real
		// EDD item id is carried separately for the actual API verification flow.
		// Per-feature license limitations â€” same single source of truth as the
		// Dashboard so the Page Importer's Pro gate reads identical state.
		$limitations = function_exists( 'bricksfly_get_license_limitations' ) ? bricksfly_get_license_limitations() : array();

		$addons_config['product_status'] = [
			'item_id'      => $license_valid ? 39996 : 0,
			'status'       => $license_status,
			'real_item_id' => defined( 'BRICKSFLY_PRO_ITEM_ID' ) ? BRICKSFLY_PRO_ITEM_ID : 0,
			'limitations'  => $limitations,
		];

		$addons_config['limitations'] = $limitations;

		$localize_data = [
			'plugin_url'         => BRICKSFLY_URL,
			'ajaxurl'            => admin_url( 'admin-ajax.php' ),
			'nonce'              => wp_create_nonce( 'bricksfly_admin_nonce' ),
			'addons_config'      => $addons_config,
			'adminURL'           => admin_url(),
			'page_url'           => esc_url( admin_url( 'edit.php?post_type=page' ) ),
			'user_role'          => function_exists( 'bricksfly_get_current_user_roles' ) ? bricksfly_get_current_user_roles() : [],
			'version'            => BRICKSFLY_VERSION,
			'st_template_domain' => BRICKSFLY_TEMPLATE_STARTER_BASE_URL,
			'home_url'           => home_url( '/' ),
		];

		wp_localize_script( 'bricksfly-page-importer-admin', 'BRICKSFLY_ADDONS_ADMIN', $localize_data );
	}
}

if ( is_admin() ) {
	new BRICKSFLY_Page_Importer();
}
