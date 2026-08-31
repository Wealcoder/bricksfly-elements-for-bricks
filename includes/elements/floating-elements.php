<?php

if (! defined('ABSPATH')) exit;

use wealcoder\bricksfly\Includes\Extensions\Helpers\ResponsiveHelper;

class BRICKSFLY_Bricks_Floating_Elements extends \Bricks\Element
{

	public $category     = 'bricks fly';
	public $name         = 'aab-floating-elements';
	public $icon         = 'ti-layout-media-overlay aab-element-marker';
	public $css_selector = '.aab-floating-elements';
	public $scripts      = ['bricksflyFloatingElements'];

	public function get_label()
	{
		return esc_html__('Floating Elements', 'bricksfly-elements-for-bricks');
	}

	public function get_keywords()
	{
		return ['floating', 'image', 'animation', 'parallax'];
	}

	public function enqueue_scripts()
	{
		$css_path = BRICKSFLY_PATH . 'public/build/elements/floating-elements.css';
		$js_path  = BRICKSFLY_PATH . 'public/build/elements/floating-elements.js';

		wp_enqueue_style(
			'aab-floating-elements',
			BRICKSFLY_URL . 'public/build/elements/floating-elements.css',
			[],
			file_exists($css_path) ? (string) filemtime($css_path) : BRICKSFLY_VERSION
		);

		if (bricks_is_builder()) {
			wp_enqueue_script(
				'aab-floating-elements-builder',
				BRICKSFLY_URL . 'public/build/elements/floating-elements.js',
				[],
				file_exists($js_path) ? (string) filemtime($js_path) : BRICKSFLY_VERSION,
				true
			);
		}
	}

	public function set_control_groups()
	{
		$this->control_groups['elements'] = [
			'title' => esc_html__('Elements', 'bricksfly-elements-for-bricks'),
			'tab'   => 'content',
		];
	}

	/**
	 * Append px to bare numeric values so inline CSS is always valid, and
	 * strictly validate anything else against a CSS-length allowlist before
	 * it can reach the generated <style> block via wp_add_inline_style().
	 *
	 * $value ultimately comes from a Bricks number/unit control's saved
	 * setting (post meta) — admin-controlled, but not a hard type boundary:
	 * a raw save request, an imported/copy-pasted template, or a future
	 * control-type change could all put an arbitrary string here.
	 * wp_add_inline_style() does not sanitize its input at all, so this is
	 * the only gate before the value is concatenated into real CSS output.
	 * Anything that isn't a plain number or number+standard-length-unit is
	 * rejected outright (returns ''), rather than passed through unchanged.
	 *
	 * @param bool $allow_negative  Pass false for values that must never be
	 *                              negative (e.g. width/size) — Bricks'
	 *                              slider control's paired number box lets
	 *                              the admin type past its own `min`, so the
	 *                              server-side clamp here is the real gate.
	 */
	private function format_css_value($value, bool $allow_negative = true): string
	{
		if ($value === '' || $value === null) {
			return '';
		}
		if (is_numeric($value)) {
			if (! $allow_negative && (float) $value < 0) {
				return '0px';
			}
			return $value . 'px';
		}

		$value = (string) $value;

		// Number (incl. decimal/negative) + one standard CSS length/percentage
		// unit — nothing else is accepted.
		if (preg_match('/^(-?\d+(?:\.\d+)?)((?:px|%|em|rem|vh|vw|vmin|vmax))$/', $value, $matches)) {
			if (! $allow_negative && (float) $matches[1] < 0) {
				return '0' . $matches[2];
			}
			return $value;
		}

		return '';
	}

	/**
	 * Build responsive CSS for a single floating-element item.
	 *
	 * Uses ResponsiveHelper::normalize() to read all breakpoint values from
	 * the repeater item array (Bricks stores them as "fieldKey:bp_key"),
	 * then emits CSS custom properties wrapped in the correct @media rules.
	 *
	 * CSS vars consumed by the stylesheet:
	 *   --aab-fe-width   → width
	 *   --aab-fe-left    → left  (.h-left)
	 *   --aab-fe-right   → right (.h-right)
	 *   --aab-fe-top     → top   (.v-top)
	 *   --aab-fe-bottom  → bottom(.v-bottom)
	 *
	 * @param string $uid_selector  Unique CSS selector for this item, e.g. ".aab-fe-abc-0"
	 * @param array  $item          Raw repeater item array
	 * @param string $h_orient      'left' | 'right'
	 * @param string $v_orient      'top'  | 'bottom'
	 */
	private function get_responsive_css(
		string $uid_selector,
		array  $item,
		string $h_orient,
		string $v_orient
	): string {

		$breakpoints = ResponsiveHelper::getBreakpoints();
		$base_key    = ResponsiveHelper::getBaseKey();

		// Only resolve the active orientation side to avoid stale values
		// bleeding in when the user switches orientation.
		$h_field = $h_orient === 'right' ? 'offsetXEnd' : 'offsetX';
		$v_field = $v_orient === 'bottom' ? 'offsetYEnd' : 'offsetY';

		// Size/offsets default to 120px/10px (forced, even when the item's
		// own settings never touched the field) so a freshly added item has
		// a sensible size and floats slightly off the edge instead of
		// collapsing to 0 width at 0,0.
		$size_map     = ResponsiveHelper::normalize($item, 'size', 120, true);
		$offset_x_map = ResponsiveHelper::normalize($item, $h_field, 10, true);
		$offset_y_map = ResponsiveHelper::normalize($item, $v_field, 10, true);

		$css_var_h = $h_orient === 'right' ? '--aab-fe-right' : '--aab-fe-left';
		$css_var_v = $v_orient === 'bottom' ? '--aab-fe-bottom' : '--aab-fe-top';

		// Nothing to emit if no responsive field was ever touched.
		if ($size_map === null && $offset_x_map === null && $offset_y_map === null) {
			return '';
		}

		// Group CSS vars by breakpoint key.
		$by_bp = [];

		foreach ($breakpoints as $bp) {
			$key  = (string) $bp['key'];
			$vars = [];

			if ($size_map !== null && isset($size_map[$key]) && $size_map[$key] !== null && $size_map[$key] !== '') {
				// Width can never be negative, regardless of what the builder's
				// slider control let the admin type into its paired number box.
				$vars['--aab-fe-width'] = $this->format_css_value($size_map[$key], false);
			}
			if ($offset_x_map !== null && isset($offset_x_map[$key]) && $offset_x_map[$key] !== null && $offset_x_map[$key] !== '') {
				$vars[$css_var_h] = $this->format_css_value($offset_x_map[$key]);
			}
			if ($offset_y_map !== null && isset($offset_y_map[$key]) && $offset_y_map[$key] !== null && $offset_y_map[$key] !== '') {
				$vars[$css_var_v] = $this->format_css_value($offset_y_map[$key]);
			}

			if (! empty($vars)) {
				$by_bp[$key] = ['bp' => $bp, 'vars' => $vars];
			}
		}

		if (empty($by_bp)) {
			return '';
		}

		$rules = [];

		foreach ($by_bp as $key => $data) {
			$bp   = $data['bp'];
			$vars = $data['vars'];

			$declarations = '';
			foreach ($vars as $prop => $val) {
				$declarations .= $prop . ':' . $val . ';';
			}

			$rule = $uid_selector . '{' . $declarations . '}';

			// Base breakpoint: no @media wrapper.
			// All others: Bricks is desktop-first → max-width.
			if ($key === $base_key || empty($bp['width'])) {
				$rules[] = $rule;
			} else {
				$rules[] = '@media(max-width:' . (int) $bp['width'] . 'px){' . $rule . '}';
			}
		}

		return implode('', $rules);
	}

	public function set_controls()
	{
		$fields = [

			'image' => [
				'label' => esc_html__('Image', 'bricksfly-elements-for-bricks'),
				'type'  => 'image',
			],

			// ── Size (responsive) ──────────────────────────────────────────────
			// No 'css' array: changing this fires Bricks' full PHP re-render,
			// which rebuilds the per-item rules in enqueue_responsive_styles().
			'size' => [
				'label'      => esc_html__('Size', 'bricksfly-elements-for-bricks'),
				// Plain number input, not the slider — no drag handle, admin
				// just types the value. No 'units' here; format_css_value()
				// already appends 'px' to a bare numeric value.
				'type'       => 'number',
				'min'        => 0,
				'max'        => 1000,
				'step'       => 1,
				'default'    => 120,
				'placeholder' => 120,
				'responsive' => true,
			],

			// ── Horizontal offset ──────────────────────────────────────────────

			'horizontalOrientation' => [
				'label'   => esc_html__('Horizontal Orientation', 'bricksfly-elements-for-bricks'),
				'type'    => 'select',
				'inline'  => true,
				'options' => [
					'left'  => esc_html__('Left', 'bricksfly-elements-for-bricks'),
					'right' => esc_html__('Right', 'bricksfly-elements-for-bricks'),
				],
				'default' => 'left',
			],

			// 'default' alone doesn't show in this control's number box —
			// Bricks' control-number component only reads `placeholder` for
			// the empty-field display (confirmed in main.min.js:
			// getPlaceholder() checks control.placeholder, not .default).
			// Both are set below: `default` for whatever seeds new repeater
			// items, `placeholder` for what's actually shown on screen.
			'offsetX' => [
				'label'      => esc_html__('Offset', 'bricksfly-elements-for-bricks'),
				'type'       => 'number',
				'min'        => -1000,
				'max'        => 1000,
				'step'       => 1,
				'default'    => 10,
				'placeholder' => 10,
				'responsive' => true,
				'required'   => ['horizontalOrientation', '!=', 'right'],
			],

			'offsetXEnd' => [
				'label'      => esc_html__('Offset', 'bricksfly-elements-for-bricks'),
				'type'       => 'number',
				'min'        => -1000,
				'max'        => 1000,
				'step'       => 1,
				'default'    => 10,
				'placeholder' => 10,
				'responsive' => true,
				'required'   => ['horizontalOrientation', '=', 'right'],
			],

			// ── Vertical offset ────────────────────────────────────────────────

			'verticalOrientation' => [
				'label'   => esc_html__('Vertical Orientation', 'bricksfly-elements-for-bricks'),
				'type'    => 'select',
				'inline'  => true,
				'options' => [
					'top'    => esc_html__('Top', 'bricksfly-elements-for-bricks'),
					'bottom' => esc_html__('Bottom', 'bricksfly-elements-for-bricks'),
				],
				'default' => 'top',
			],

			'offsetY' => [
				'label'      => esc_html__('Offset', 'bricksfly-elements-for-bricks'),
				'type'       => 'number',
				'min'        => -1000,
				'max'        => 1000,
				'step'       => 1,
				'default'    => 10,
				'placeholder' => 10,
				'responsive' => true,
				'required'   => ['verticalOrientation', '!=', 'bottom'],
			],

			'offsetYEnd' => [
				'label'      => esc_html__('Offset', 'bricksfly-elements-for-bricks'),
				'type'       => 'number',
				'min'        => -1000,
				'max'        => 1000,
				'step'       => 1,
				'default'    => 10,
				'placeholder' => 10,
				'responsive' => true,
				'required'   => ['verticalOrientation', '=', 'bottom'],
			],

			'zIndex' => [
				'label'   => esc_html__('Z-Index', 'bricksfly-elements-for-bricks'),
				'type'    => 'number',
				'default' => 1,
			],

			'liveAnimation' => [
				'label'   => esc_html__('Live Animation', 'bricksfly-elements-for-bricks'),
				'type'    => 'select',
				'options' => [
					''        => esc_html__('None', 'bricksfly-elements-for-bricks'),
					'float'   => esc_html__('Float Y', 'bricksfly-elements-for-bricks'),
					'float-x' => esc_html__('Float X', 'bricksfly-elements-for-bricks'),
					'spin'    => esc_html__('Spin', 'bricksfly-elements-for-bricks'),
					'scale'   => esc_html__('Scale', 'bricksfly-elements-for-bricks'),
					'wiggle'  => esc_html__('Wiggle', 'bricksfly-elements-for-bricks'),
				],
				'default' => '',
			],

			// ── Scroll Smoother ────────────────────────────────────────────────

			'enableScrollSmoother' => [
				'label'       => esc_html__('Enable Scroll Smoother', 'bricksfly-elements-for-bricks'),
				'description' => esc_html__('If you want to use scroll smooth, please enable global settings first', 'bricksfly-elements-for-bricks'),
				'type'        => 'checkbox',
			],

			'dataSpeed' => [
				'label'    => esc_html__('Data Speed', 'bricksfly-elements-for-bricks'),
				'type'     => 'number',
				'step'     => 0.1,
				'default'  => 0.9,
				'required' => ['enableScrollSmoother', '!=', ''],
			],

			'dataLag' => [
				'label'    => esc_html__('Data Lag', 'bricksfly-elements-for-bricks'),
				'type'     => 'number',
				'step'     => 0.1,
				'default'  => 0.5,
				'required' => ['enableScrollSmoother', '!=', ''],
			],

			// Not shown to users (hidden via CSS in the builder panel, see
			// editor-panel.js / floating-elements.js). Size/Offset are
			// `responsive: true` with no `css` array, and Bricks only grants
			// automatic live-preview reactivity to a control via one of those
			// two paths — a plain (non-responsive) field change still forces
			// Bricks' generic "settings changed -> re-render" path, which is
			// how horizontalOrientation/verticalOrientation already update
			// live. floating-elements.js writes a new value here whenever a
			// watched Size/Offset field changes, purely to give Bricks a
			// plain-field change to react to and pull a fresh render()
			// (and thus the inline <style> it now emits) into the canvas.
			'_cssSnapshot' => [
				'label'   => esc_html__('_cssSnapshot', 'bricksfly-elements-for-bricks'),
				'type'    => 'text',
				'default' => '',
			],

		];

		$this->controls['floating_items'] = [
			'tab'     => 'content',
			'group'   => 'elements',
			'label'   => esc_html__('Elements', 'bricksfly-elements-for-bricks'),
			'type'    => 'repeater',
			'fields'  => $fields,
			// Start with one item so the element renders a real (dummy) image on insert
			// instead of the empty-state. Uses the Bricks placeholder image as content;
			// orientation/animation use the field defaults.
			'default' => [
				[
					'image'                 => [
						'url'      => defined('BRICKS_URL_ASSETS') ? BRICKS_URL_ASSETS . 'images/placeholder-image-800x600.jpg' : '',
						'filename' => 'placeholder-image-800x600.jpg',
					],
					'horizontalOrientation' => 'left',
					'verticalOrientation'   => 'top',
					'liveAnimation'         => 'float',
					'zIndex'                => 1,
				],
			],
		];
	}

	public function render()
	{
		$settings   = $this->settings;
		$items      = ! empty($settings['floating_items']) ? $settings['floating_items'] : [];
		$element_id = $this->id; // Unique Bricks element ID on the page.

		if (empty($items)) {
			return $this->render_element_placeholder([
				'icon-class' => 'ti-layout-media-overlay',
				'text'       => esc_html__('No floating elements added.', 'bricksfly-elements-for-bricks'),
			]);
		}

		$this->set_attribute('_root', 'class', ['aab-floating-elements']);

		// Emitted inline (not via wp_add_inline_style()/enqueue_scripts()) so it
		// travels with the HTML on every render path, including Bricks' AJAX
		// builder re-renders. Those only return render()'s echoed output
		// (Ajax::render_element() -> Element::init() -> ob_get_clean()) — CSS
		// queued through wp_add_inline_style() during that request is never
		// flushed, since the request never calls wp_head()/wp_print_styles().
		// That gap made width/offset edits invisible in the live preview
		// (adding a repeater item still "worked" because that's plain HTML).
		//
		// Must be echoed AFTER the root div is opened (as its first child),
		// never before it: Bricks' canvas applies every AJAX re-render via
		// `body.firstElementChild` on the parsed response (iframe.min.js
		// Element.setHTML()) — a <style> tag echoed before the div becomes
		// that first element instead of the div itself, so the canvas swaps
		// in the empty <style> tag and silently drops the real markup. This
		// is invisible until $css is non-empty for the first time, which is
		// exactly when Size/Offset get their first responsive value — so
		// only those fields ever appeared "broken" in the builder.
		$css = '';
		foreach ($items as $index => $item) {
			$h_orient   = ! empty($item['horizontalOrientation']) ? $item['horizontalOrientation'] : 'left';
			$v_orient   = ! empty($item['verticalOrientation']) ? $item['verticalOrientation'] : 'top';
			$item_class = 'aab-fe-' . sanitize_html_class($element_id) . '-' . $index;
			$css       .= $this->get_responsive_css('.' . $item_class, $item, $h_orient, $v_orient);
		}

		echo wp_kses_post('<div ' . $this->render_attributes('_root') . '>');

		if ($css !== '') {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS is sanitized by format_css_value() before reaching get_responsive_css().
			echo '<style>' . $css . '</style>';
		}

		foreach ($items as $index => $item) {

			$h_orient = ! empty($item['horizontalOrientation']) ? $item['horizontalOrientation'] : 'left';
			$v_orient = ! empty($item['verticalOrientation'])   ? $item['verticalOrientation']   : 'top';
			$anim     = ! empty($item['liveAnimation'])         ? $item['liveAnimation']         : '';

			// Unique class used as the responsive CSS selector target.
			$item_class = 'aab-fe-' . sanitize_html_class($element_id) . '-' . $index;

			$classes   = ['floating-element', $item_class];
			$classes[] = $h_orient === 'right'  ? 'h-right'  : 'h-left';
			$classes[] = $v_orient === 'bottom' ? 'v-bottom' : 'v-top';

			if ($anim) {
				$classes[] = 'aab-live-anim-' . sanitize_html_class($anim);
			}

			// Only z-index goes in the inline style — all responsive values
			// are handled through the enqueued CSS custom properties.
			$z_index    = isset($item['zIndex']) && $item['zIndex'] !== '' ? intval($item['zIndex']) : 1;
			$style_attr = ' style="z-index:' . $z_index . '"';

			// ── Responsive rules ───────────────────────────────────────────────
			// ── Image ──────────────────────────────────────────────────────────
			$image_data = $item['image'] ?? '';
			$image_url  = '';
			$image_id   = 0;

			if (is_array($image_data)) {
				if (! empty($image_data['url'])) {
					$image_url = $image_data['url'];
				}
				if (! empty($image_data['id'])) {
					$image_id = (int) $image_data['id'];
				}
				if (! $image_url && $image_id) {
					$image_url = wp_get_attachment_url($image_id);
				}
			} elseif (is_numeric($image_data) && $image_data > 0) {
				$image_id  = (int) $image_data;
				$image_url = wp_get_attachment_url($image_id);
			}

			if (empty($image_url)) {
				$image_url = defined('BRICKS_URL_ASSETS')
					? BRICKS_URL_ASSETS . 'images/placeholder-image-800x600.jpg'
					: '';
			}

			$alt = $image_id ? get_post_meta($image_id, '_wp_attachment_image_alt', true) : '';

			// ── Scroll Smoother ────────────────────────────────────────────────
			$data_attrs = '';
			if (! empty($item['enableScrollSmoother'])) {
				$data_speed  = isset($item['dataSpeed']) && $item['dataSpeed'] !== '' ? $item['dataSpeed'] : 0.9;
				$data_lag    = isset($item['dataLag'])   && $item['dataLag']   !== '' ? $item['dataLag']   : 0.5;
				$data_attrs  = ' data-speed="' . esc_attr($data_speed) . '"';
				$data_attrs .= ' data-lag="'   . esc_attr($data_lag)   . '"';
			}

			echo '<div class="' . esc_attr(implode(' ', $classes)) . '"' . $style_attr . $data_attrs . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($alt) . '">';
			echo '</div>';
		}

		echo '</div>';
	}
}
