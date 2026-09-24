<?php
/**
 * Dump every registered Bricks element (native + Bricksfly) with its controls,
 * exactly as Bricks sees them at runtime (after all plugin filters ran).
 *
 * Usage: php dump-controls.php [--free] > ../reference/controls[-free].json
 */

$_SERVER['HTTP_HOST']   = 'bricks-animation-dev.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_SCHEME'] = 'http';
ini_set( 'display_errors', '0' );

// --free: load WordPress without Bricksfly Pro (in memory only, nothing is saved), so the dump
// contains exactly what the free plugin provides. Uses WordPress' pre-initialized hooks support.
if ( in_array( '--free', $argv, true ) ) {
	$GLOBALS['wp_filter']['option_active_plugins'][10][] = [
		'function'      => function ( $plugins ) {
			return array_values(
				array_filter(
					(array) $plugins,
					function ( $plugin ) {
						return strpos( $plugin, 'bricksfly-elements-for-bricks-pro/' ) !== 0;
					}
				)
			);
		},
		'accepted_args' => 1,
	];
}

define( 'WP_USE_THEMES', false );
require dirname( __DIR__, 5 ) . '/wp-load.php';

// Controls are built in the context of a logged-in admin (some are capability-gated).
$admins = get_users( [ 'role' => 'administrator', 'number' => 1 ] );
if ( $admins ) {
	wp_set_current_user( $admins[0]->ID );
}

if ( ! class_exists( '\Bricks\Elements' ) ) {
	fwrite( STDERR, "Bricks not loaded\n" );
	exit( 1 );
}

if ( empty( \Bricks\Elements::$elements ) ) {
	\Bricks\Elements::init_elements();
}

\Bricks\Elements::load_elements();

$keep = [ 'type', 'label', 'group', 'tab', 'options', 'default', 'units', 'inline', 'placeholder', 'multiple', 'required', 'css', 'hasDynamicData', 'exclude', 'fields', 'min', 'max', 'step', 'unitless', 'rerender' ];

$simplify = function ( $control ) use ( &$simplify, $keep ) {
	$out = [];
	foreach ( $keep as $k ) {
		if ( ! array_key_exists( $k, $control ) ) {
			continue;
		}
		$v = $control[ $k ];
		if ( $k === 'fields' && is_array( $v ) ) {
			$fields = [];
			foreach ( $v as $fk => $fc ) {
				if ( is_array( $fc ) ) {
					$fields[ $fk ] = $simplify( $fc );
				}
			}
			$v = $fields;
		}
		if ( $v instanceof Closure ) {
			$v = '(closure)';
		}
		$out[ $k ] = $v;
	}
	return $out;
};

$result = [
	'bricksVersion' => defined( 'BRICKS_VERSION' ) ? BRICKS_VERSION : null,
	'breakpoints'   => array_map(
		function ( $bp ) {
			return [ 'key' => $bp['key'], 'width' => $bp['width'] ?? null, 'base' => ! empty( $bp['base'] ) ];
		},
		\Bricks\Breakpoints::get_breakpoints()
	),
	'elements'      => [],
];

foreach ( \Bricks\Elements::$elements as $name => $el ) {
	$controls = [];
	foreach ( $el['controls'] ?? [] as $key => $control ) {
		if ( is_array( $control ) ) {
			$controls[ $key ] = $simplify( $control );
		}
	}
	$result['elements'][ $name ] = [
		'class'    => $el['class'] ?? null,
		'label'    => $el['label'] ?? null,
		'category' => $el['category'] ?? null,
		'nestable' => ! empty( $el['nestable'] ),
		'scripts'  => $el['scripts'] ?? [],
		'controls' => $controls,
	];
}

echo wp_json_encode( $result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR );
