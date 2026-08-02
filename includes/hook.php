<?php

if (! defined('ABSPATH')) {
  exit;
}

/**
 * Allow the plugin's element wrapper attributes through wp_kses_post().
 *
 * Bricks' Element::render_attributes() already escapes every attribute value
 * (esc_url()/esc_attr()) and validates attribute names, so its output is safe.
 * We still pass that output through wp_kses_post() at echo time ("escape late")
 * so the escaping is explicit and verifiable. But wp_kses_post() strips
 * data- and aria- attributes and inline SVG, which the elements rely on (e.g.
 * the sliders' data-swiper-options on the wrapper, and rendered inline icons).
 *
 * This filter re-allows those on the wrapper/icon tags the plugin outputs, so
 * the elements' wp_kses_post() calls keep data- and aria- attributes and inline
 * SVG intact. It only ADDS to the allowlist (never removes), so existing
 * post/comment sanitization keeps all of its own permitted tags.
 *
 * @param array  $tags    Allowed tags/attributes map.
 * @param string $context Context passed by wp_kses().
 * @return array
 */
function bricksfly_kses_allow_element_attrs($tags, $context)
{
  if ('post' !== $context) {
    return $tags;
  }

  // data-*/aria-* + common structural, link, media and ARIA attributes.
  $extra_attrs = array(
    'id'              => true,
    'class'           => true,
    'style'           => true,
    'title'           => true,
    'href'            => true,
    'src'             => true,
    'alt'             => true,
    'target'          => true,
    'rel'             => true,
    'role'            => true,
    'tabindex'        => true,
    'dir'             => true,
    'width'           => true,
    'height'          => true,
    'loading'         => true,
    'referrerpolicy'  => true,
    'allow'           => true,
    'allowfullscreen' => true,
    'frameborder'     => true,
    'name'            => true,
    'type'            => true,
    'value'           => true,
    'for'             => true,
    'aria-*'          => true,
    'data-*'          => true,
  );

  $wrapper_tags = array('div', 'a', 'span', 'li', 'ul', 'button', 'iframe', 'img', 'p', 'strong', 'em');
  foreach ($wrapper_tags as $tag) {
    $existing     = isset($tags[$tag]) && is_array($tags[$tag]) ? $tags[$tag] : array();
    $tags[$tag]   = array_merge($existing, $extra_attrs);
  }

  // Inline SVG icon markup produced by Bricks' render_icon().
  $svg_attrs = array(
    'xmlns'           => true,
    'viewbox'         => true,
    'width'           => true,
    'height'          => true,
    'fill'            => true,
    'stroke'          => true,
    'stroke-width'    => true,
    'stroke-linecap'  => true,
    'stroke-linejoin' => true,
    'class'           => true,
    'style'           => true,
    'aria-hidden'     => true,
    'role'            => true,
    'focusable'       => true,
    'd'               => true,
    'points'          => true,
    'cx'              => true,
    'cy'              => true,
    'r'               => true,
    'x'               => true,
    'y'               => true,
    'x1'              => true,
    'y1'              => true,
    'x2'              => true,
    'y2'              => true,
    'transform'       => true,
    'data-*'          => true,
    'aria-*'          => true,
  );

  $svg_tags = array('svg', 'path', 'g', 'circle', 'rect', 'line', 'polyline', 'polygon', 'use');
  foreach ($svg_tags as $tag) {
    $tags[$tag] = $svg_attrs;
  }

  $tags['i'] = array(
    'class'       => true,
    'style'       => true,
    'aria-hidden' => true,
    'data-*'      => true,
    'aria-*'      => true,
  );

  return $tags;
}

// Register the element-attribute allowlist so the plugin's wp_kses_post()
// calls (used to escape element output at echo time) keep data-/aria-
// attributes and inline SVG. array_merge-only, so it never narrows the
// default post allowlist used elsewhere.
add_filter('wp_kses_allowed_html', 'bricksfly_kses_allow_element_attrs', 10, 2);

// smooth scroller
function bricksfly_add_header_smoother_start()
{
  echo '<div id="smooth-wrapper"><div id="smooth-content">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

function bricksfly_add_header_smoother_end()
{
  echo '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

add_action('wp_body_open', 'bricksfly_add_header_smoother_start');

add_action('wp_footer', 'bricksfly_add_header_smoother_end', -1);

// Plugin-logo branding for the plugin's custom Bricks elements (left-side
// elements drawer in the Bricks builder).
function bricksfly_enqueue_element_logo_css()
{
  if (! function_exists('bricks_is_builder_main') || ! bricks_is_builder_main()) {
    return;
  }

  $url = esc_url(BRICKSFLY_URL . 'public/images/plugin_logo.png');

  $css = ''
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
    . '}';

  wp_register_style('aab-element-logo', false, [], BRICKSFLY_VERSION);
  wp_enqueue_style('aab-element-logo');
  wp_add_inline_style('aab-element-logo', $css);
}
add_action('wp_enqueue_scripts', 'bricksfly_enqueue_element_logo_css', 100);
