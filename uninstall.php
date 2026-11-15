<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://#
 * @since      1.0.0
 *
 * @package    Bricks_Animation_Addons
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Remove the admin notices' own bookkeeping on the current site: the
 * dismiss / snooze rows (option or transient named after the notice id,
 * admin/Notices/class-notices.php) and the per-user view counts. Settings,
 * templates and imported content are left alone.
 */
function bricksfly_uninstall_notice_data() {
	global $wpdb;

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- One-off cleanup on uninstall; the names are not known in advance.
	$names = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s",
			$wpdb->esc_like( 'bricksfly_notice_' ) . '%',
			$wpdb->esc_like( '_transient_bricksfly_notice_' ) . '%',
			$wpdb->esc_like( '_transient_timeout_bricksfly_notice_' ) . '%'
		)
	);
	foreach ( $names as $name ) {
		if ( 0 === strpos( $name, '_transient_timeout_' ) ) {
			continue;
		}
		if ( 0 === strpos( $name, '_transient_' ) ) {
			delete_transient( substr( $name, strlen( '_transient_' ) ) );
		} else {
			delete_option( $name );
		}
	}
}

if ( is_multisite() ) {
	foreach ( get_sites( array( 'fields' => 'ids' ) ) as $bricksfly_site_id ) {
		switch_to_blog( $bricksfly_site_id );
		bricksfly_uninstall_notice_data();
		restore_current_blog();
	}
} else {
	bricksfly_uninstall_notice_data();
}

// User meta is shared by every site of a network.
delete_metadata( 'user', 0, 'bricksfly_notice_views', '', true );
