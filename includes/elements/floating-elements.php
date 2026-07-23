<?php

if (! defined('ABSPATH')) exit;

use wealcoder\bricksfly\Includes\Extensions\Helpers\ResponsiveHelper;

class THEBRBRE_Bricks_Floating_Elements extends \Bricks\Element
{

	public $category     = 'bricks fly';
	public $name         = 'aab-floating-elements';
	public $icon         = 'ti-layout-media-overlay aab-element-marker';
	public $css_selector = '.aab-floating-elements';
	public $scripts      = ['thebrbreFloatingElements'];

	public function get_label()
	{
		return esc_html__('Floating Elements', 'the-bricksfly');
	}

	public function get_keywords()
	{
		return ['floating', 'image', 'animation', 'parallax'];
	}

	public function enqueue_scripts()
	{

		wp_enqueue_style(
			'aab-floating-elements',
			THEBRBRE_URL . 'public/build/elements/floating-elements.css',
			[],
			'1.0.0'
		);

		$this->enqueue_responsive_styles();


		if (bricks_is_builder()) {
			wp_enqueue_script(
				'aab-floating-elements-builder',
				THEBRBRE_URL . 'public/build/elements/floating-elements.js',
				[],
				'1.0.0',
				true
			);
		}
	}

	public function set_control_groups()
	{
		$this->control_groups['elements'] = [
			'title' => esc_html__('Elements', 'the-bricksfly'),
			'tab'   => 'content',
		];
	}

	/**
	 * Append px to bare numeric values so inline CSS is always valid.
	 * Bricks number/slider controls save unit-picked values as e.g. "100px",
	 * but typing a number with no unit picked leaves the value as a bare 100.
	 */
	private function format_css_value($value): string
	{
		if ($value === '' || $value === null) {
			return '';
		}
		if (is_numeric($value)) {
			return $value . 'px';
		}
		return (string) $value;
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

		$size_map     = ResponsiveHelper::normalize($item, 'size');
		$offset_x_map = ResponsiveHelper::normalize($item, $h_field);
		$offset_y_map = ResponsiveHelper::normalize($item, $v_field);

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
				$vars['--aab-fe-width'] = $this->format_css_value($size_map[$key]);
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

	/**
	 * Add instance-specific responsive rules through WordPress' style API.
	 */
	private function enqueue_responsive_styles(): void
	{
		$items = ! empty($this->settings['floating_items']) ? $this->settings['floating_items'] : [];

		if (empty($items)) {
			return;
		}

		$css = '';
		foreach ($items as $index => $item) {
			$h_orient   = ! empty($item['horizontalOrientation']) ? $item['horizontalOrientation'] : 'left';
			$v_orient   = ! empty($item['verticalOrientation']) ? $item['verticalOrientation'] : 'top';
			$item_class = 'aab-fe-' . sanitize_html_class($this->id) . '-' . $index;
			$css       .= $this->get_responsive_css('.' . $item_class, $item, $h_orient, $v_orient);
		}

		if ($css !== '') {
			wp_add_inline_style('aab-floating-elements', $css);
		}
	}

	public function set_controls()
	{
		$fields = [

			'image' => [
				'label' => esc_html__('Image', 'the-bricksfly'),
				'type'  => 'image',
			],

			// ── Size (responsive) ──────────────────────────────────────────────
			// No 'css' array: changing this fires Bricks' full PHP re-render,
			// which rebuilds the per-item rules in enqueue_responsive_styles().
			'size' => [
				'label'      => esc_html__('Size', 'the-bricksfly'),
				'type'       => 'number',
				'units'      => true,
				'responsive' => true,
			],

			// ── Horizontal offset ──────────────────────────────────────────────

			'horizontalOrientation' => [
				'label'   => esc_html__('Horizontal Orientation', 'the-bricksfly'),
				'type'    => 'select',
				'inline'  => true,
				'options' => [
					'left'  => esc_html__('Left', 'the-bricksfly'),
					'right' => esc_html__('Right', 'the-bricksfly'),
				],
				'default' => 'left',
			],

			'offsetX' => [
				'label'      => esc_html__('Offset', 'the-bricksfly'),
				'type'       => 'number',
				'units'      => true,
				'responsive' => true,
				'required'   => ['horizontalOrientation', '!=', 'right'],
			],

			'offsetXEnd' => [
				'label'      => esc_html__('Offset', 'the-bricksfly'),
				'type'       => 'number',
				'units'      => true,
				'responsive' => true,
				'required'   => ['horizontalOrientation', '=', 'right'],
			],

			// ── Vertical offset ────────────────────────────────────────────────

			'verticalOrientation' => [
				'label'   => esc_html__('Vertical Orientation', 'the-bricksfly'),
				'type'    => 'select',
				'inline'  => true,
				'options' => [
					'top'    => esc_html__('Top', 'the-bricksfly'),
					'bottom' => esc_html__('Bottom', 'the-bricksfly'),
				],
				'default' => 'top',
			],

			'offsetY' => [
				'label'      => esc_html__('Offset', 'the-bricksfly'),
				'type'       => 'number',
				'units'      => true,
				'responsive' => true,
				'required'   => ['verticalOrientation', '!=', 'bottom'],
			],

			'offsetYEnd' => [
				'label'      => esc_html__('Offset', 'the-bricksfly'),
				'type'       => 'number',
				'units'      => true,
				'responsive' => true,
				'required'   => ['verticalOrientation', '=', 'bottom'],
			],

			'zIndex' => [
				'label'   => esc_html__('Z-Index', 'the-bricksfly'),
				'type'    => 'number',
				'default' => 1,
			],

			'liveAnimation' => [
				'label'   => esc_html__('Live Animation', 'the-bricksfly'),
				'type'    => 'select',
				'options' => [
					''        => esc_html__('None', 'the-bricksfly'),
					'float'   => esc_html__('Float Y', 'the-bricksfly'),
					'float-x' => esc_html__('Float X', 'the-bricksfly'),
					'spin'    => esc_html__('Spin', 'the-bricksfly'),
					'scale'   => esc_html__('Scale', 'the-bricksfly'),
					'wiggle'  => esc_html__('Wiggle', 'the-bricksfly'),
				],
				'default' => '',
			],

			// ── Scroll Smoother ────────────────────────────────────────────────

			'enableScrollSmoother' => [
				'label'       => esc_html__('Enable Scroll Smoother', 'the-bricksfly'),
				'description' => esc_html__('If you want to use scroll smooth, please enable global settings first', 'the-bricksfly'),
				'type'        => 'checkbox',
			],

			'dataSpeed' => [
				'label'    => esc_html__('Data Speed', 'the-bricksfly'),
				'type'     => 'number',
				'step'     => 0.1,
				'default'  => 0.9,
				'required' => ['enableScrollSmoother', '!=', ''],
			],

			'dataLag' => [
				'label'    => esc_html__('Data Lag', 'the-bricksfly'),
				'type'     => 'number',
				'step'     => 0.1,
				'default'  => 0.5,
				'required' => ['enableScrollSmoother', '!=', ''],
			],

		];

		$this->controls['floating_items'] = [
			'tab'     => 'content',
			'group'   => 'elements',
			'label'   => esc_html__('Elements', 'the-bricksfly'),
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
				'text'       => esc_html__('No floating elements added.', 'the-bricksfly'),
			]);
		}

		$this->set_attribute('_root', 'class', ['aab-floating-elements']);

		echo wp_kses_post('<div ' . $this->render_attributes('_root') . '>');

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
