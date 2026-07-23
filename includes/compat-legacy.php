<?php
/**
 * Backward-compatibility shim layer.
 *
 * The free plugin's PHP symbols were renamed to the unique THEBRBRE_ / thebrbre_
 * prefix and the wealcoder\bricksfly namespace. The sibling "the-bricksfly-pro"
 * plugin still references the previous names (constants, functions, classes and
 * namespaced FQCNs). This file re-exposes every previously-public symbol Pro
 * depends on as a thin alias/wrapper pointing at the new canonical symbol, so
 * an already-installed Pro build keeps working without modification.
 *
 * IMPORTANT: this maps CODE symbols only. Saved option names and Bricks element
 * setting keys (e.g. aab_animation, aab_save_extensions) were intentionally NOT
 * renamed, so no data migration is required.
 *
 * @package The_BricksFly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * 1. Constant aliases (Pro reads these). New constant is canonical; the old
 *    name is defined to the same value for compatibility.
 * ---------------------------------------------------------------------- */
$thebrbre_const_aliases = array(
	'AAB_ADDONS_VERSION'            => 'THEBRBRE_VERSION',
	'AAB_ADDONS_PATH'               => 'THEBRBRE_PATH',
	'AAB_ADDONS_URL'                => 'THEBRBRE_URL',
	'AAB_ADDONS_BASE'               => 'THEBRBRE_BASE',
	'AAB_ADDONS_FILE'               => 'THEBRBRE_FILE',
	'AAB_ADDON_PRO_ITEM_ID'         => 'THEBRBRE_PRO_ITEM_ID',
	'AAB_ADDON_PRO_ITEM_NAME'       => 'THEBRBRE_PRO_ITEM_NAME',
	'AAB_ADDON_PRO_STORE_URL'       => 'THEBRBRE_PRO_STORE_URL',
	'AAB_BRICKS_ELEMENTS'           => 'THEBRBRE_BRICKS_ELEMENTS',
	'AAB_TEMPLATE_STARTER_BASE_URL' => 'THEBRBRE_TEMPLATE_STARTER_BASE_URL',
);
foreach ( $thebrbre_const_aliases as $old_const => $new_const ) {
	if ( ! defined( $old_const ) && defined( $new_const ) ) {
		define( $old_const, constant( $new_const ) );
	}
}
unset( $thebrbre_const_aliases, $old_const, $new_const );

/* -------------------------------------------------------------------------
 * 2. Function shims (Pro calls these). Each old name wraps the new function.
 * ---------------------------------------------------------------------- */
if ( ! function_exists( 'aabaddons_is_license_valid' ) && function_exists( 'thebrbre_is_license_valid' ) ) {
	function aabaddons_is_license_valid() {
		return thebrbre_is_license_valid();
	}
}
if ( ! function_exists( 'aabaddons_get_settings' ) && function_exists( 'thebrbre_get_settings' ) ) {
	function aabaddons_get_settings( $option_name, $element = null ) {
		return thebrbre_get_settings( $option_name, $element );
	}
}
if ( ! function_exists( 'aabaddons_is_extension_active' ) && function_exists( 'thebrbre_is_extension_active' ) ) {
	function aabaddons_is_extension_active( $slug ) {
		return thebrbre_is_extension_active( $slug );
	}
}
if ( ! function_exists( 'aabaddons_is_widget_active' ) && function_exists( 'thebrbre_is_widget_active' ) ) {
	function aabaddons_is_widget_active( $slug ) {
		return thebrbre_is_widget_active( $slug );
	}
}
if ( ! function_exists( 'aabaddons_get_config' ) && function_exists( 'thebrbre_get_config' ) ) {
	function aabaddons_get_config() {
		return thebrbre_get_config();
	}
}
if ( ! function_exists( 'aabaddons_is_pro_active' ) && function_exists( 'thebrbre_is_pro_active' ) ) {
	function aabaddons_is_pro_active() {
		return thebrbre_is_pro_active();
	}
}
if ( ! function_exists( 'aabaddons_is_pro_installed' ) && function_exists( 'thebrbre_is_pro_installed' ) ) {
	function aabaddons_is_pro_installed() {
		return thebrbre_is_pro_installed();
	}
}
if ( ! function_exists( 'aab_is_feature_allowed' ) && function_exists( 'thebrbre_is_feature_allowed' ) ) {
	function aab_is_feature_allowed( $feature ) {
		return thebrbre_is_feature_allowed( $feature );
	}
}
if ( ! function_exists( 'aab_feature_denied_message' ) && function_exists( 'thebrbre_feature_denied_message' ) ) {
	function aab_feature_denied_message( $feature ) {
		return thebrbre_feature_denied_message( $feature );
	}
}
if ( ! function_exists( 'aab_is_license_valid' ) && function_exists( 'thebrbre_legacy_is_license_valid' ) ) {
	function aab_is_license_valid() {
		return thebrbre_legacy_is_license_valid();
	}
}
if ( ! function_exists( 'aab_is_pro_installed' ) && function_exists( 'thebrbre_legacy_is_pro_installed' ) ) {
	function aab_is_pro_installed() {
		return thebrbre_legacy_is_pro_installed();
	}
}
if ( ! function_exists( 'aab_get_license_limitations' ) && function_exists( 'thebrbre_get_license_limitations' ) ) {
	function aab_get_license_limitations() {
		return thebrbre_get_license_limitations();
	}
}

/* -------------------------------------------------------------------------
 * 3. Class aliases (Pro references these by old name / old FQCN).
 *    Registered lazily: when PHP fails to find the OLD name, this autoloader
 *    aliases it to the NEW one — so it works regardless of load order and only
 *    triggers if legacy code actually asks for the old name.
 * ---------------------------------------------------------------------- */
$GLOBALS['thebrbre_class_aliases'] = array(
	// Global (non-namespaced) classes Pro references by their old name.
	'AABAddons_Activator' => 'THEBRBRE_Activator',

	// Namespaced classes Pro references by full old FQCN. The namespace root
	// changed (AABAddons -> wealcoder\bricksfly). AAB_Settings_Placeholder's
	// short name was ALSO renamed to THEBRBRE_Settings_Placeholder, so its map
	// changes both the root and the short name.
	'AABAddons\\Admin\\Pages\\AAB_Settings_Placeholder'             => 'wealcoder\\bricksfly\\Admin\\Pages\\THEBRBRE_Settings_Placeholder',
	'AABAddons\\Admin\\Base\\Helpers'                                => 'wealcoder\\bricksfly\\Admin\\Base\\Helpers',
	'AABAddons\\Admin\\Base\\Importer'                               => 'wealcoder\\bricksfly\\Admin\\Base\\Importer',
	'AABAddons\\Includes\\Extensions\\Helpers\\ResponsiveHelper'     => 'wealcoder\\bricksfly\\Includes\\Extensions\\Helpers\\ResponsiveHelper',
	'AABAddons\\Includes\\Extensions\\Helpers\\Label_Name_Helper'    => 'wealcoder\\bricksfly\\Includes\\Extensions\\Helpers\\Label_Name_Helper',
	'AABAddons\\Includes\\Extensions\\Helpers\\BricksElementsHelper' => 'wealcoder\\bricksfly\\Includes\\Extensions\\Helpers\\BricksElementsHelper',
);

spl_autoload_register(
	function ( $requested ) {
		$map = isset( $GLOBALS['thebrbre_class_aliases'] ) ? $GLOBALS['thebrbre_class_aliases'] : array();
		$requested = ltrim( $requested, '\\' );
		if ( isset( $map[ $requested ] ) && class_exists( $map[ $requested ] ) ) {
			class_alias( $map[ $requested ], $requested );
		}
	},
	true,
	true // prepend so it resolves legacy names before other autoloaders give up
);
