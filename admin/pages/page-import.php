<?php

namespace AABAddons\Admin\Pages;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class AAB_Page_Importer {

	const HANDLE = 'aab-page-import';

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
			AAB_ADDONS_URL . 'public/js/aab-admin-actions.js',
			[],
			AAB_ADDONS_VERSION,
			true
		);

		wp_localize_script(
			'aab-admin-actions',
			'AAB_PAGE_IMPORT',
			[
				'page_url' => esc_url( admin_url( 'admin.php?page=bf-page-importer' ) ),
				'logo'     => esc_url( AAB_ADDONS_URL . 'public/images/plugin_logo.png' ),
			]
		);
	}

	public function custom_page_tab( $views ) {
		global $wpdb;

		$count = $wpdb->get_var( "
			SELECT COUNT(*) FROM $wpdb->posts
			WHERE post_type = 'page'
			AND post_status = 'publish'
			AND ID IN (
				SELECT post_id FROM $wpdb->postmeta
				WHERE meta_key = 'aab_imported' AND meta_value = '1'
			)
		" );

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for view display only, not processing form data.
		$class                     = ( isset( $_GET['aae-latest-import'] ) && $_GET['aae-latest-import'] === 'import' ) ? 'current' : '';
		$url                       = add_query_arg( 'aae-latest-import', 'import', admin_url( 'edit.php?post_type=page' ) );
		$views['latest-import']    = "<a href='" . esc_url( $url ) . "' class='" . esc_attr( $class ) . "' style='color: #fc6848; font-weight: 500'>" . esc_html__( 'AAB Imported', 'the-bricksfly' ) . " <span class='count'>(" . (int) $count . ")</span></a>";

		return $views;
	}

	public function custom_page_filter( $query ) {
		global $pagenow;

		if ( is_admin() && $pagenow === 'edit.php' && $query->get( 'post_type' ) === 'page' ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading URL parameter for query filtering only, not processing form data.
			if ( isset( $_GET['aae-latest-import'] ) && $_GET['aae-latest-import'] === 'import' ) {
				$query->set( 'meta_key', 'aab_imported' );
				$query->set( 'meta_value', '1' );
			}
		}
	}

	public function clear_notices_for_importer() {
		$screen = get_current_screen();
		if ( $screen && strpos( $screen->id, '_page_bf-page-importer' ) !== false ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	public function admin_classes( $classes ) {
		$screen = get_current_screen();
		if ( ! is_string( $classes ) ) {
			$classes = '';
		}
		if ( $screen && strpos( $screen->id, '_page_bf-page-importer' ) !== false ) {
			$classes .= ' wcf-anim2024';
		}
		return $classes;
	}

	public function add_menu() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		add_submenu_page(
			\AABAddons\Admin\Pages\AAB_Admin_Init::MENU_PAGE_SLUG,
			__( 'Page Import', 'the-bricksfly' ),
			__( 'Page Import', 'the-bricksfly' ),
			'manage_options',
			'bf-page-importer',
			[ $this, 'page_html' ]
		);
	}

	public function page_html() {
		echo '<div id="bf-page-importer"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function importer_assets( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		if ( strpos( $screen->id, '_page_bf-page-importer' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'bf-page-importer-admin',
			AAB_ADDONS_URL . 'public/build/admin/page-import.css',
			[],
			time()
		);

		wp_enqueue_script(
			'bf-page-importer-admin',
			AAB_ADDONS_URL . 'public/build/admin/page-import.js',
			[ 'wp-element' ],
			time(),
			true
		);

		// Enrich the config with the CURRENT license state so Pro page templates
		// unlock the moment a license is activated — no manual refresh needed.
		//
		// The bug this fixes: the Page Importer used to localize a bare
		// `addons_config` with no license fields, so the React app's Pro gate
		// (TemplateShow.jsx: `activated?.product_status?.item_id === 13`) never
		// saw the active license and kept every Pro template locked. The
		// Dashboard already enriches its config this way; we mirror it here so
		// BOTH pages read the SAME single source of truth.
		//
		// Reading the options here (on every importer page load) means the state
		// is always freshly fetched — never a stale cached value — so activation
		// done elsewhere is reflected on the next load of this page.
		$addons_config = apply_filters( 'aabaddons_dashboard_config', $GLOBALS['aabaddons_config'] ?? [] );

		$license_status = (string) get_option( 'wcf_addon_sl_license_status', '' );
		$license_key    = (string) get_option( 'wcf_addon_sl_license_key', '' );

		// Valid only when the Pro plugin folder exists AND the stored status is
		// "valid" — the same combined check used by aabaddons_is_license_valid() and
		// the Dashboard, so deleting the Pro folder relocks Pro instantly.
		$pro_installed = function_exists( 'aabaddons_is_pro_installed' ) ? aabaddons_is_pro_installed() : false;
		$license_valid = $pro_installed && ( 'valid' === $license_status );

		$addons_config['sl_lic']    = $license_key;
		$addons_config['is_pro']    = $pro_installed;
		$addons_config['aab_valid'] = $license_valid;

		// The compiled React UI unlocks Pro items on `product_status.item_id === 13`.
		// Send 13 only when the license is valid (mirrors the Dashboard); the real
		// EDD item id is carried separately for the actual API verification flow.
		$addons_config['product_status'] = [
			'item_id'      => $license_valid ? 13 : 0,
			'status'       => $license_status,
			'real_item_id' => defined( 'AAB_ADDON_PRO_ITEM_ID' ) ? AAB_ADDON_PRO_ITEM_ID : 0,
		];

		$localize_data = [
			'plugin_url'         => AAB_ADDONS_URL,
			'ajaxurl'            => admin_url( 'admin-ajax.php' ),
			'nonce'              => wp_create_nonce( 'aab_admin_nonce' ),
			'addons_config'      => $addons_config,
			'adminURL'           => admin_url(),
			'page_url'           => esc_url( admin_url( 'edit.php?post_type=page' ) ),
			'user_role'          => function_exists( 'aabaddons_get_current_user_roles' ) ? aabaddons_get_current_user_roles() : [],
			'version'            => AAB_ADDONS_VERSION,
			'st_template_domain' => AAB_TEMPLATE_STARTER_BASE_URL,
			'home_url'           => home_url( '/' ),
		];

		wp_localize_script( 'bf-page-importer-admin', 'AAB_ADDONS_ADMIN', $localize_data );
	}
}

if ( is_admin() ) {
	new AAB_Page_Importer();
}
