<?php

namespace AABAddons\Includes\Extensions\Helpers;

use Bricks\Elements;

defined('ABSPATH') || exit;

class BricksElementsHelper
{
    /**
     * Get all registered Bricks elements
     *
     * @return array Array of element slugs
     */
    public static function get_all()
    {
        if (!class_exists('\Bricks\Elements')) {
            return [];
        }

        $elements = Elements::$elements ?? [];

        if (empty($elements)) {
            return [];
        }

        return array_keys($elements);
    }
}
