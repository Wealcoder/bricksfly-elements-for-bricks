<?php

namespace wealcoder\bricksfly\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Links in this plugin's row on the Plugins screen.
 */
class BRICKSFLY_Row_Actions {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_filter( 'plugin_action_links', [ $this, 'bricksfly_add_plugin_link' ], 10, 2 );
		add_filter( 'plugin_row_meta', [ $this, '_plugin_row_meta' ], 10, 2 );
	}

	function _plugin_row_meta( $meta, $plugin_file ) {
		if ( BRICKSFLY_BASE !== $plugin_file ) {
			return $meta;
		}

		$meta[] = '<a href="https://bricksfly.com/docs/" target="_blank" rel="noopener">' . esc_html__( 'Documentation', 'bricksfly-elements-for-bricks' ) . '</a>';
		$meta[] = '<a href="https://wordpress.org/support/plugin/bricksfly-elements-for-bricks/" target="_blank" rel="noopener">' . esc_html__( 'Support', 'bricksfly-elements-for-bricks' ) . '</a>';
		if ( function_exists( 'bricksfly_is_pro_installed' ) && ! bricksfly_is_pro_installed() ) {
			$meta[] = '<a href="https://bricksfly.com" style="color:#ff7a00; font-weight: bold;" target="_blank" rel="noopener">' . esc_html__( 'Upgrade to Pro', 'bricksfly-elements-for-bricks' ) . '</a>';
		}
		$meta[] = '<a href="https://wordpress.org/support/plugin/bricksfly-elements-for-bricks/reviews/#new-post" target="_blank" rel="noopener">' . esc_html__( 'Rate the plugin', 'bricksfly-elements-for-bricks' ) . '</a>';
		return $meta;
	}

	function bricksfly_add_plugin_link( $plugin_actions, $plugin_file ) {
		$new_actions = array();
		if ( BRICKSFLY_BASE === $plugin_file ) {
			$new_actions['aab-dsb-settings'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( admin_url( 'admin.php?page=bricksfly_addons_settings' ) ),
				esc_html__( 'Settings', 'bricksfly-elements-for-bricks' )
			);
		}
		return array_merge( $new_actions, $plugin_actions );
	}
}

new BRICKSFLY_Row_Actions();
