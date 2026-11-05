<?php

namespace wealcoder\bricksfly\Includes\Extensions\Helpers;

use Bricks\Elements;

defined( 'ABSPATH' ) || exit;

class BricksElementsHelper {

	/**
	 * Get all registered Bricks elements
	 *
	 * @return array Array of element slugs
	 */
	public static function get_all() {
		if ( ! class_exists( '\Bricks\Elements' ) ) {
			return array();
		}

		$elements = Elements::$elements ?? array();

		if ( empty( $elements ) ) {
			return array();
		}

		return array_keys( $elements );
	}
}
