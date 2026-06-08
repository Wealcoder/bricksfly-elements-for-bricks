<?php
namespace AAB\Includes;
defined( 'ABSPATH' ) || exit;

class WBAA_Dependency_Manager {

    public static function resolve( $settings ) {

        // Pin area
        if ( ( $settings['wcf_enable_pin_area'] ?? '' ) === 'yes' ) {
            wp_enqueue_script( 'wbaa-pin-area' );
        }

        // Interaction triggers
        if ( in_array(
            $settings['wbaa_text_trigger'] ?? '',
            [ 'mouseover', 'click' ],
            true
        ) ) {
            wp_enqueue_script( 'wbaa-interaction' );
        }

        // Scroll triggers
        if ( in_array(
            $settings['wbaa_text_trigger'] ?? '',
            [ 'on_scroll', 'play_with_scroll' ],
            true
        ) ) {
            wp_enqueue_script( 'wbaa-scroll' );
        }
    }
}
