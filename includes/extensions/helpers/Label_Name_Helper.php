<?php

/**
 * Label Name Helper
 *
 * Single source of truth for the plugin-branded control-group title used by
 * Bricks element panels. Both the free plugin (Starter Animations) and the
 * Pro plugin (every Pro extension) call this helper so the icon, sizing, and
 * markup stay consistent across the entire suite.
 *
 * Lives in the free plugin so the Pro plugin — which hard-depends on free —
 * can `use` the same class. Update the icon here and both plugins update.
 *
 * @package Bricks_Animation_Addons
 */

namespace wealcoder\thebricksfly\Includes\Extensions\Helpers;

defined('ABSPATH') || exit;

class Label_Name_Helper
{
    public static function title(string $label): string
    {
        static $logo_html = null;

        if ($logo_html === null) {
            $logo_html = '<img src="' . esc_url(THEBRBRE_URL . 'public/images/plugin_logo.png') . '" width="20" height="20" style="vertical-align:-6px;margin-right:6px"> ';
        }

        return $logo_html . esc_html($label);
    }
}
