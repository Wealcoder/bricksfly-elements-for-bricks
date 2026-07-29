<?php

namespace wealcoder\thebricksfly\Admin\Pages;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class THEBRBRE_Page_Importer {

	const HANDLE = 'thebrbre-page-import';

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
			THEBRBRE_URL . 'public/js/aab-admin-actions.js',
			[],
			THEBRBRE_VERSION,
			true
		);

		wp_localize_script(
			'aab-admin-actions',
			'THEBRBRE_PAGE_IMPORT',
			[
				'page_url' => esc_url( admin_url( 'admin.php?page=thebrbre-page-importer' ) ),
				'logo'     => esc_url( THEBRBRE_URL . 'public/images/plugin_logo.png' ),
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
				WHERE meta_key = 'thebrbre_imported' AND meta_value = '1'
			)
		" );

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
				$query->set( 'meta_key', 'thebrbre_imported' );
				$query->set( 'meta_value', '1' );
			}
		}
	}

	public function clear_notices_for_importer() {
		$screen = get_current_screen();
		if ( $screen && strpos( $screen->id, '_page_thebrbre-page-importer' ) !== false ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	public function admin_classes( $classes ) {
		$screen = get_current_screen();
		if ( ! is_string( $classes ) ) {
			$classes = '';
		}
		if ( $screen && strpos( $screen->id, '_page_thebrbre-page-importer' ) !== false ) {
			$classes .= ' wcf-anim2024';
		}
		return $classes;
	}

	public function add_menu() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		add_submenu_page(
			\wealcoder\thebricksfly\Admin\Pages\THEBRBRE_Admin_Init::MENU_PAGE_SLUG,
			__( 'Page Import', 'bricksfly-elements-for-bricks' ),
			__( 'Page Import', 'bricksfly-elements-for-bricks' ),
			'manage_options',
			'thebrbre-page-importer',
			[ $this, 'page_html' ]
		);
	}

	public function page_html() {
		echo '<div id="thebrbre-page-importer"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function importer_assets( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		if ( strpos( $screen->id, '_page_thebrbre-page-importer' ) === false ) {
			return;
		}

		wp_enqueue_style(
			'thebrbre-page-importer-admin',
			THEBRBRE_URL . 'public/build/admin/page-import.css',
			[],
			time()
		);

		wp_enqueue_script(
			'thebrbre-page-importer-admin',
			THEBRBRE_URL . 'public/build/admin/page-import.js',
			[ 'wp-element' ],
			time(),
			true
		);

		// Bricksfly has no license tiers â€” every feature is always enabled.
		// `is_pro` still reflects whether the Pro plugin folder is installed,
		// since Pro's page templates still require the Pro plugin itself.
		$addons_config = apply_filters('thebrbre_dashboard_config', $GLOBALS['thebrbre_config'] ?? [] );

		$pro_installed = function_exists( 'thebrbre_is_pro_installed' ) ? thebrbre_is_pro_installed() : false;

		$addons_config['is_pro']         = $pro_installed;
		$addons_config['thebrbre_valid'] = true;

		$addons_config['product_status'] = [
			'item_id'      => $pro_installed ? 39996 : 0,
			'status'       => 'valid',
			'real_item_id' => defined( 'THEBRBRE_PRO_ITEM_ID' ) ? THEBRBRE_PRO_ITEM_ID : 0,
			'limitations'  => [],
		];

		$addons_config['limitations'] = [];

		$localize_data = [
			'plugin_url'         => THEBRBRE_URL,
			'ajaxurl'            => admin_url( 'admin-ajax.php' ),
			'nonce'              => wp_create_nonce( 'thebrbre_admin_nonce' ),
			'addons_config'      => $addons_config,
			'adminURL'           => admin_url(),
			'page_url'           => esc_url( admin_url( 'edit.php?post_type=page' ) ),
			'user_role'          => function_exists( 'thebrbre_get_current_user_roles' ) ? thebrbre_get_current_user_roles() : [],
			'version'            => THEBRBRE_VERSION,
			'st_template_domain' => THEBRBRE_TEMPLATE_STARTER_BASE_URL,
			'home_url'           => home_url( '/' ),
		];

		wp_localize_script( 'thebrbre-page-importer-admin', 'THEBRBRE_ADDONS_ADMIN', $localize_data );
	}
}

if ( is_admin() ) {
	new THEBRBRE_Page_Importer();
}
