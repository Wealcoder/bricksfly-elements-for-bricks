<?php

if (! defined('ABSPATH')) {
  exit;
}

// smooth scroller
function aaeaddon_add_header_smoother_start()
{
  echo '<div id="smooth-wrapper"><div id="smooth-content">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

function aaeaddon_add_header_smoother_end()
{
  echo '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

add_action('wp_body_open', 'aaeaddon_add_header_smoother_start');

add_action('wp_footer', 'aaeaddon_add_header_smoother_end', -1);

// Plugin-logo branding for the plugin's custom Bricks elements (left-side
// elements drawer in the Bricks builder).
function aab_print_element_logo_css()
{
  if (! function_exists('bricks_is_builder_main') || ! bricks_is_builder_main()) {
    return;
  }

  $url = esc_url(AAB_ADDONS_URL . 'public/images/plugin_logo.png');

  echo '<style id="aab-element-logo-css">'
    // Elements drawer (left): logo pinned to the top-left corner of the card.
    . '.bricks-add-element:has(i.aab-element-marker){position:relative;}'
    . '.bricks-add-element:has(i.aab-element-marker)::before{'
    . 'content:"";'
    . 'position:absolute;'
    . 'top:3px;left:3px;'
    . 'width:16px;height:16px;'
    . 'background:url(' . esc_url( $url ) . ') center/contain no-repeat;'
    . 'pointer-events:none;'
    . 'z-index:2;'
    . '}'
    // Structure panel (right): logo AFTER the label. Bricks uses
    // <span class="label readonly"> when the element has a custom label,
    // <span class="name readonly"> otherwise — cover both.
    . '.structure-item:has(i.aab-element-marker) > .title > .label::after,'
    . '.structure-item:has(i.aab-element-marker) > .title > .name::after{'
    . 'content:"";'
    . 'display:inline-block;'
    . 'width:12px;height:12px;'
    . 'margin-left:6px;'
    . 'background:url(' . esc_url( $url ) . ') center/contain no-repeat;'
    . 'vertical-align:-2px;'
    . '}'
    . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action('wp_head', 'aab_print_element_logo_css');
