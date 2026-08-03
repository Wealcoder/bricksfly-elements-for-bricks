<?php

/**
 * Starter Animations for Bricks core elements.
 *
 * Injects an animation control group into Bricks's core text/image/container
 * elements via the `bricks/elements/{name}/controls` and
 * `bricks/elements/{name}/control_groups` filters. Animation behavior on the
 * front-end is provided by `public/js/starter-animations-client.js` and
 * `public/css/starter-animations.css`.
 *
 * @package Bricks_Animation_Addons
 */

namespace wealcoder\bricksfly\Includes\Extensions;

use wealcoder\bricksfly\Includes\Extensions\Helpers\Label_Name_Helper;

if (! defined('ABSPATH')) {
	exit;
}

class BRICKSFLY_Starter_Animations
{

	/**
	 * Bricks core text-style elements. Get the full set of animations
	 * including text effects (glow, typewriter, mask, wave, char-animate).
	 */
	const TEXT_WIDGETS = ['heading', 'text-basic', 'text', 'text-link'];

	/**
	 * Bricks elements that get layout animations only (no text effects).
	 */
	const MEDIA_WIDGETS = ['image'];

	/**
	 * Bricks layout elements. Get a separate, smaller container animation set.
	 */
	const CONTAINER_WIDGETS = ['container', 'section', 'block', 'div'];

	const GROUP_ID            = '_bricksfly_starter_animations';
	const GROUP_ID_CONTAINER  = '_bricksfly_starter_animations_container';

	private static $instance = null;

	public static function instance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct()
	{
		// Text widgets: full text + layout animation set (includes text effects).
		foreach (self::TEXT_WIDGETS as $name) {
			add_filter("bricks/elements/{$name}/controls", [$this, 'inject_text_controls']);
			add_filter("bricks/elements/{$name}/control_groups", [$this, 'inject_text_group']);
		}

		// Media widgets: layout animations only — no text effects.
		foreach (self::MEDIA_WIDGETS as $name) {
			add_filter("bricks/elements/{$name}/controls", [$this, 'inject_media_controls']);
			add_filter("bricks/elements/{$name}/control_groups", [$this, 'inject_text_group']);
		}

		// Containers: smaller container-only animation set.
		foreach (self::CONTAINER_WIDGETS as $name) {
			add_filter("bricks/elements/{$name}/controls", [$this, 'inject_container_controls']);
			add_filter("bricks/elements/{$name}/control_groups", [$this, 'inject_container_group']);
		}

		add_filter('bricks/element/render_attributes', [$this, 'apply_render_classes'], 10, 3);

		add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
	}

	/* =====================================================================
	 * Control group registration
	 * ================================================================== */

	public function inject_text_group($groups)
	{
		$groups[self::GROUP_ID] = [
			'title' => Label_Name_Helper::title(__('Starter Animations', 'bricksfly-elements-for-bricks')),
			'tab'   => 'content',
		];
		return $groups;
	}

	public function inject_container_group($groups)
	{
		$groups[self::GROUP_ID_CONTAINER] = [
			'title' => Label_Name_Helper::title(__('Starter Animations', 'bricksfly-elements-for-bricks')),
			'tab'   => 'content',
		];
		return $groups;
	}

	/* =====================================================================
	 * Helper: option lists
	 * ================================================================== */

	private function get_text_animation_options()
	{
		return [
			'none'             => esc_html__('None', 'bricksfly-elements-for-bricks'),
			'reveal'           => esc_html__('Reveal', 'bricksfly-elements-for-bricks'),
			'scale-up'         => esc_html__('Scale', 'bricksfly-elements-for-bricks'),
			'slide'            => esc_html__('Slide', 'bricksfly-elements-for-bricks'),
			'skew-reveal'      => esc_html__('Skew Reveal', 'bricksfly-elements-for-bricks'),
			'flip'             => esc_html__('Flip', 'bricksfly-elements-for-bricks'),
			'text-glow'        => esc_html__('Glow Pulse (text)', 'bricksfly-elements-for-bricks'),
			'text-typewriter'  => esc_html__('Typewriter (text)', 'bricksfly-elements-for-bricks'),
			'text-mask-wipe'   => esc_html__('Mask Wipe (text)', 'bricksfly-elements-for-bricks'),
			'text-wave'        => esc_html__('Water Wave (text)', 'bricksfly-elements-for-bricks'),
			'text-bg-clip'     => esc_html__('Background Clip (text)', 'bricksfly-elements-for-bricks'),
			'text-char-animate' => esc_html__('Character Animation (text)', 'bricksfly-elements-for-bricks'),
		];
	}

	private function get_media_animation_options()
	{
		// Image / media: layout animations only — strip text effects.
		return [
			'none'        => esc_html__('None', 'bricksfly-elements-for-bricks'),
			'reveal'      => esc_html__('Reveal', 'bricksfly-elements-for-bricks'),
			'scale-up'    => esc_html__('Scale', 'bricksfly-elements-for-bricks'),
			'slide'       => esc_html__('Slide', 'bricksfly-elements-for-bricks'),
			'skew-reveal' => esc_html__('Skew Reveal', 'bricksfly-elements-for-bricks'),
			'flip'        => esc_html__('Flip', 'bricksfly-elements-for-bricks'),
		];
	}

	private function get_easing_options()
	{
		return [
			'ease'                          => esc_html__('Ease (Default)', 'bricksfly-elements-for-bricks'),
			'linear'                        => esc_html__('Linear', 'bricksfly-elements-for-bricks'),
			'ease-in'                       => esc_html__('Ease In', 'bricksfly-elements-for-bricks'),
			'ease-out'                      => esc_html__('Ease Out', 'bricksfly-elements-for-bricks'),
			'ease-in-out'                   => esc_html__('Ease In Out', 'bricksfly-elements-for-bricks'),
			'cubic-bezier(.25,.8,.25,1)'    => esc_html__('Smooth Cubic', 'bricksfly-elements-for-bricks'),
			'cubic-bezier(.17,.67,.83,.67)' => esc_html__('Elastic Feel', 'bricksfly-elements-for-bricks'),
		];
	}

	/* =====================================================================
	 * Text/media controls (full set)
	 * ================================================================== */

	public function inject_text_controls($controls)
	{
		return $this->inject_animation_controls($controls, true);
	}

	public function inject_media_controls($controls)
	{
		return $this->inject_animation_controls($controls, false);
	}

	private function inject_animation_controls($controls, $is_text)
	{

		// Animation picker
		$controls['_bricksfly_starter_anim'] = [
			'tab'         => 'content',
			'group'       => self::GROUP_ID,
			'label'       => esc_html__('Animation', 'bricksfly-elements-for-bricks'),
			'type'        => 'select',
			'options'     => $is_text ? $this->get_text_animation_options() : $this->get_media_animation_options(),
			'default'     => 'none',
			'inline'      => true,
		];

		// Common timing — duration / delay / easing
		$controls['_bricksfly_anim_duration'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'label'    => esc_html__('Duration (ms)', 'bricksfly-elements-for-bricks'),
			'type'     => 'number',
			'default'  => 1000,
			'min'      => 100,
			'max'      => 10000,
			'step'     => 50,
			'required' => ['_bricksfly_starter_anim', '!=', ['', 'none']],
			'css'      => [
				[
					'property' => '--bricksfly-duration',
					'selector' => '',
					'value'    => '%sms',
				],
			],
		];

		$controls['_bricksfly_anim_delay'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'label'    => esc_html__('Delay (ms)', 'bricksfly-elements-for-bricks'),
			'type'     => 'number',
			'default'  => 0,
			'min'      => 0,
			'max'      => 10000,
			'step'     => 50,
			'required' => ['_bricksfly_starter_anim', '!=', ['', 'none']],
			'css'      => [
				[
					'property' => '--bricksfly-delay',
					'selector' => '',
					'value'    => '%sms',
				],
			],
		];

		$controls['_bricksfly_anim_ease'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'label'    => esc_html__('Easing', 'bricksfly-elements-for-bricks'),
			'type'     => 'select',
			'options'  => $this->get_easing_options(),
			'default'  => 'ease',
			'inline'   => true,
			'required' => ['_bricksfly_starter_anim', '!=', ['', 'none']],
			'css'      => [
				[
					'property' => '--bricksfly-ease',
					'selector' => '',
				],
			],
		];

		if ($is_text) {
			// ---- text-glow ----
			$controls['_bricksfly_glow_color'] = [
				'tab'      => 'content',
				'group'    => self::GROUP_ID,
				'label'    => esc_html__('Glow Color', 'bricksfly-elements-for-bricks'),
				'type'     => 'color',
				'default'  => ['hex' => '#0000ff'],
				'required' => ['_bricksfly_starter_anim', '=', 'text-glow'],
				'css'      => [
					[
						'property' => '--bricksfly-glow-color',
						'selector' => '',
					],
				],
			];

			$controls['_bricksfly_glow_size'] = [
				'tab'      => 'content',
				'group'    => self::GROUP_ID,
				'label'    => esc_html__('Glow Size (px)', 'bricksfly-elements-for-bricks'),
				'type'     => 'number',
				'default'  => 20,
				'min'      => 5,
				'max'      => 100,
				'required' => ['_bricksfly_starter_anim', '=', 'text-glow'],
				'css'      => [
					[
						'property' => '--bricksfly-glow-size',
						'selector' => '',
						'value'    => '%spx',
					],
				],
			];

			$controls['_bricksfly_glow_iteration'] = [
				'tab'      => 'content',
				'group'    => self::GROUP_ID,
				'label'    => esc_html__('Animation Loop', 'bricksfly-elements-for-bricks'),
				'type'     => 'select',
				'options'  => [
					'1'        => esc_html__('Play Once', 'bricksfly-elements-for-bricks'),
					'infinite' => esc_html__('Infinite', 'bricksfly-elements-for-bricks'),
				],
				'default'  => 'infinite',
				'inline'   => true,
				'required' => ['_bricksfly_starter_anim', '=', 'text-glow'],
				'css'      => [
					[
						'property' => '--bricksfly-iteration',
						'selector' => '',
					],
				],
			];

			// ---- text-mask-wipe ----
			$controls['_bricksfly_mask_wipe_bg'] = [
				'tab'      => 'content',
				'group'    => self::GROUP_ID,
				'label'    => esc_html__('Mask Color', 'bricksfly-elements-for-bricks'),
				'type'     => 'color',
				'default'  => ['hex' => '#000000'],
				'required' => ['_bricksfly_starter_anim', '=', 'text-mask-wipe'],
				'css'      => [
					[
						'property' => '--bricksfly-mask-bg',
						'selector' => '',
					],
				],
			];
		} // end if ( $is_text ) — text-glow / text-mask-wipe

		// ---- reveal ----
		$controls['_bricksfly_reveal_direction'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'label'    => esc_html__('Direction', 'bricksfly-elements-for-bricks'),
			'type'     => 'select',
			'options'  => [
				'bottom' => esc_html__('Bottom -> Top', 'bricksfly-elements-for-bricks'),
				'top'    => esc_html__('Top -> Bottom', 'bricksfly-elements-for-bricks'),
				'left'   => esc_html__('Left -> Right', 'bricksfly-elements-for-bricks'),
				'right'  => esc_html__('Right -> Left', 'bricksfly-elements-for-bricks'),
				'center' => esc_html__('Center Expand', 'bricksfly-elements-for-bricks'),
			],
			'default'  => 'bottom',
			'inline'   => true,
			'required' => ['_bricksfly_starter_anim', '=', 'reveal'],
		];

		$controls['_bricksfly_reveal_fade'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'label'    => esc_html__('Enable Fade', 'bricksfly-elements-for-bricks'),
			'type'     => 'checkbox',
			'default'  => false,
			'required' => ['_bricksfly_starter_anim', '=', 'reveal'],
		];

		if ($is_text) {
			// ---- text-wave ----
			$controls['_bricksfly_wave_fill_color'] = [
				'tab'      => 'content',
				'group'    => self::GROUP_ID,
				'label'    => esc_html__('Wave Fill Color', 'bricksfly-elements-for-bricks'),
				'type'     => 'color',
				'required' => ['_bricksfly_starter_anim', '=', 'text-wave'],
				'css'      => [
					[
						'property' => '--bricksfly-wave-fill',
						'selector' => '',
					],
				],
			];

			// ---- text-bg-clip ----
			// Image source for the background-clip text effect. The control returns
			// a Bricks image array; the URL is emitted as inline style for the
			// `--bricksfly-bg-text-image` CSS variable in apply_render_classes() because
			// Bricks's `css` array doesn't unwrap image arrays into CSS-variable
			// values cleanly.
			$controls['_bricksfly_bg_text_image'] = [
				'tab'      => 'content',
				'group'    => self::GROUP_ID,
				'label'    => esc_html__('Background Image', 'bricksfly-elements-for-bricks'),
				'type'     => 'image',
				'required' => ['_bricksfly_starter_anim', '=', 'text-bg-clip'],
			];

			$controls['_bricksfly_bg_text_speed'] = [
				'tab'      => 'content',
				'group'    => self::GROUP_ID,
				'label'    => esc_html__('Animation Speed (s)', 'bricksfly-elements-for-bricks'),
				'type'     => 'number',
				'default'  => 15,
				'min'      => 1,
				'max'      => 60,
				'required' => ['_bricksfly_starter_anim', '=', 'text-bg-clip'],
				'css'      => [
					[
						'property' => '--bricksfly-bg-speed',
						'selector' => '',
						'value'    => '%ss',
					],
				],
			];

			// ---- text-char-animate ----
			$controls['_bricksfly_char_preset'] = [
				'tab'      => 'content',
				'group'    => self::GROUP_ID,
				'label'    => esc_html__('Character Preset', 'bricksfly-elements-for-bricks'),
				'type'     => 'select',
				'options'  => [
					'revolve'      => esc_html__('Revolve Scale', 'bricksfly-elements-for-bricks'),
					'ball'         => esc_html__('Ball Drop', 'bricksfly-elements-for-bricks'),
					'slide'        => esc_html__('Side Slide', 'bricksfly-elements-for-bricks'),
					'revolve_drop' => esc_html__('Revolve Drop', 'bricksfly-elements-for-bricks'),
					'drop_vanish'  => esc_html__('Drop Vanish', 'bricksfly-elements-for-bricks'),
					'twister'      => esc_html__('Twister', 'bricksfly-elements-for-bricks'),
				],
				'default'  => 'revolve',
				'inline'   => true,
				'required' => ['_bricksfly_starter_anim', '=', 'text-char-animate'],
			];

			$controls['_bricksfly_char_revolve_x'] = [
				'tab'      => 'content',
				'group'    => self::GROUP_ID,
				'label'    => esc_html__('Translate X (px)', 'bricksfly-elements-for-bricks'),
				'type'     => 'number',
				'default'  => -150,
				'required' => [
					['_bricksfly_starter_anim', '=', 'text-char-animate'],
					['_bricksfly_char_preset',  '=', 'revolve'],
				],
				'css'      => [
					[
						'property' => '--bricksfly-char-x',
						'selector' => '',
						'value'    => '%spx',
					],
				],
			];

			$controls['_bricksfly_char_revolve_y'] = [
				'tab'      => 'content',
				'group'    => self::GROUP_ID,
				'label'    => esc_html__('Translate Y (px)', 'bricksfly-elements-for-bricks'),
				'type'     => 'number',
				'default'  => -50,
				'required' => [
					['_bricksfly_starter_anim', '=', 'text-char-animate'],
					['_bricksfly_char_preset',  '=', 'revolve'],
				],
				'css'      => [
					[
						'property' => '--bricksfly-char-y',
						'selector' => '',
						'value'    => '%spx',
					],
				],
			];

			$controls['_bricksfly_char_ball_y'] = [
				'tab'      => 'content',
				'group'    => self::GROUP_ID,
				'label'    => esc_html__('Drop Distance (px)', 'bricksfly-elements-for-bricks'),
				'type'     => 'number',
				'default'  => 200,
				'required' => [
					['_bricksfly_starter_anim', '=', 'text-char-animate'],
					['_bricksfly_char_preset',  '=', 'ball'],
				],
				'css'      => [
					[
						'property' => '--bricksfly-char-y',
						'selector' => '',
						'value'    => '%spx',
					],
				],
			];

			$controls['_bricksfly_char_twister_rotate'] = [
				'tab'      => 'content',
				'group'    => self::GROUP_ID,
				'label'    => esc_html__('Rotate Degree', 'bricksfly-elements-for-bricks'),
				'type'     => 'number',
				'default'  => -180,
				'required' => [
					['_bricksfly_starter_anim', '=', 'text-char-animate'],
					['_bricksfly_char_preset',  '=', 'twister'],
				],
				'css'      => [
					[
						'property' => '--bricksfly-char-rotate',
						'selector' => '',
						'value'    => '%sdeg',
					],
				],
			];
		} // end if ( $is_text ) — text-wave / text-bg-clip / text-char-animate

		// ---- scale-up ----
		$controls['_bricksfly_scale_start'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'label'    => esc_html__('Start Scale', 'bricksfly-elements-for-bricks'),
			'type'     => 'number',
			'default'  => 0.6,
			'step'     => 0.1,
			'min'      => 0,
			'max'      => 3,
			'required' => ['_bricksfly_starter_anim', '=', 'scale-up'],
			'css'      => [
				[
					'property' => '--bricksfly-scale-start',
					'selector' => '',
				],
			],
		];

		$controls['_bricksfly_scale_end'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'label'    => esc_html__('End Scale', 'bricksfly-elements-for-bricks'),
			'type'     => 'number',
			'default'  => 1,
			'step'     => 0.1,
			'min'      => 0,
			'max'      => 3,
			'required' => ['_bricksfly_starter_anim', '=', 'scale-up'],
			'css'      => [
				[
					'property' => '--bricksfly-scale-end',
					'selector' => '',
				],
			],
		];

		$controls['_bricksfly_scale_origin'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'label'    => esc_html__('Scale From', 'bricksfly-elements-for-bricks'),
			'type'     => 'select',
			'options'  => [
				'center' => esc_html__('Center', 'bricksfly-elements-for-bricks'),
				'top'    => esc_html__('Top', 'bricksfly-elements-for-bricks'),
				'bottom' => esc_html__('Bottom', 'bricksfly-elements-for-bricks'),
				'left'   => esc_html__('Left', 'bricksfly-elements-for-bricks'),
				'right'  => esc_html__('Right', 'bricksfly-elements-for-bricks'),
			],
			'default'  => 'center',
			'inline'   => true,
			'required' => ['_bricksfly_starter_anim', '=', 'scale-up'],
			'css'      => [
				[
					'property' => '--bricksfly-scale-origin',
					'selector' => '',
				],
			],
		];

		// ---- slide ----
		$controls['_bricksfly_slide_direction'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'label'    => esc_html__('Direction', 'bricksfly-elements-for-bricks'),
			'type'     => 'select',
			'options'  => [
				'bottom' => esc_html__('Bottom → Top', 'bricksfly-elements-for-bricks'),
				'top'    => esc_html__('Top → Bottom', 'bricksfly-elements-for-bricks'),
				'left'   => esc_html__('Left → Right', 'bricksfly-elements-for-bricks'),
				'right'  => esc_html__('Right → Left', 'bricksfly-elements-for-bricks'),
			],
			'default'  => 'bottom',
			'inline'   => true,
			'required' => ['_bricksfly_starter_anim', '=', 'slide'],
		];

		$controls['_bricksfly_slide_distance'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'label'    => esc_html__('Distance (px)', 'bricksfly-elements-for-bricks'),
			'type'     => 'number',
			'default'  => 60,
			'min'      => 0,
			'max'      => 500,
			'step'     => 5,
			'required' => ['_bricksfly_starter_anim', '=', 'slide'],
			'css'      => [
				[
					'property' => '--bricksfly-slide-distance',
					'selector' => '',
					'value'    => '%spx',
				],
			],
		];

		// ---- skew-reveal ----
		$controls['_bricksfly_skew_angle'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'label'    => esc_html__('Skew Angle (deg)', 'bricksfly-elements-for-bricks'),
			'type'     => 'number',
			'default'  => 18,
			'required' => ['_bricksfly_starter_anim', '=', 'skew-reveal'],
			'css'      => [
				[
					'property' => '--bricksfly-skew-angle',
					'selector' => '',
					'value'    => '%sdeg',
				],
			],
		];

		$controls['_bricksfly_skew_distance'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'label'    => esc_html__('Translate Distance (px)', 'bricksfly-elements-for-bricks'),
			'type'     => 'number',
			'default'  => 40,
			'required' => ['_bricksfly_starter_anim', '=', 'skew-reveal'],
			'css'      => [
				[
					'property' => '--bricksfly-skew-distance',
					'selector' => '',
					'value'    => '%spx',
				],
			],
		];

		// ---- flip ----
		$controls['_bricksfly_flip_axis'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'label'    => esc_html__('Flip Direction', 'bricksfly-elements-for-bricks'),
			'type'     => 'select',
			'options'  => [
				'x' => esc_html__('Flip X', 'bricksfly-elements-for-bricks'),
				'y' => esc_html__('Flip Y', 'bricksfly-elements-for-bricks'),
			],
			'default'  => 'x',
			'inline'   => true,
			'required' => ['_bricksfly_starter_anim', '=', 'flip'],
		];

		$controls['_bricksfly_flip_angle'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'label'    => esc_html__('Flip Angle (deg)', 'bricksfly-elements-for-bricks'),
			'type'     => 'number',
			'default'  => 90,
			'required' => ['_bricksfly_starter_anim', '=', 'flip'],
			'css'      => [
				[
					'property' => '--bricksfly-flip-angle',
					'selector' => '',
					'value'    => '%sdeg',
				],
			],
		];

		$controls['_bricksfly_flip_perspective'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'label'    => esc_html__('Perspective (px)', 'bricksfly-elements-for-bricks'),
			'type'     => 'number',
			'default'  => 800,
			'required' => ['_bricksfly_starter_anim', '=', 'flip'],
			'css'      => [
				[
					'property' => '--bricksfly-flip-perspective',
					'selector' => '',
					'value'    => '%spx',
				],
			],
		];

		// ---- repeat-on-enter (footer) ----
		$controls['_bricksfly_repeat_on_enter'] = [
			'tab'         => 'content',
			'group'       => self::GROUP_ID,
			'label'       => esc_html__('Repeat Animation?', 'bricksfly-elements-for-bricks'),
			'description' => esc_html__('Play once, or replay every time the element enters the viewport.', 'bricksfly-elements-for-bricks'),
			'type'        => 'select',
			'options'     => [
				'no'  => esc_html__('Play Once', 'bricksfly-elements-for-bricks'),
				'yes' => esc_html__('Every Time', 'bricksfly-elements-for-bricks'),
			],
			'default'     => 'no',
			'inline'      => true,
			'required'    => ['_bricksfly_starter_anim', '!=', ['', 'none']],
		];


		// ---- editor preview ----
		$controls['_bricksfly_anim_editor_enabled'] = [
			'tab'         => 'content',
			'group'       => self::GROUP_ID,
			'label'       => esc_html__('Enable On Editor', 'bricksfly-elements-for-bricks'),
			'description' => esc_html__('For better performance in editor mode, keep this off.', 'bricksfly-elements-for-bricks'),
			'type'        => 'checkbox',
			'inline'      => true,
			'required'    => ['_bricksfly_starter_anim', '!=', ['', 'none']],
			'separator'   => 'before',
		];

		$controls['_bricksfly_play_starter_animation'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID,
			'type'     => 'info',
			'content'  => '<button type="button" class="bricksfly-free-play-animation"><span class="bricksfly-free-play-animation__icon" aria-hidden="true">▶</span><span class="bricksfly-free-play-animation__label">PLAY ANIMATION</span></button>',
			'required' => [
				['_bricksfly_starter_anim', '!=', ['', 'none']],
				['_bricksfly_anim_editor_enabled', '=', true],
			],
		];

		return $controls;
	}

	/* =====================================================================
	 * Container controls (smaller set: none / slide / flip)
	 * ================================================================== */

	public function inject_container_controls($controls)
	{

		$controls['_bricksfly_starter_anim_container'] = [
			'tab'     => 'content',
			'group'   => self::GROUP_ID_CONTAINER,
			'label'   => esc_html__('Animation', 'bricksfly-elements-for-bricks'),
			'type'    => 'select',
			'options' => [
				'none'  => esc_html__('None', 'bricksfly-elements-for-bricks'),
				'slide' => esc_html__('Slide', 'bricksfly-elements-for-bricks'),
				'flip'  => esc_html__('Flip', 'bricksfly-elements-for-bricks'),
			],
			'default' => 'none',
			'inline'  => true,
		];

		// ---- container slide ----
		$controls['_bricksfly_slide_direction_container'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID_CONTAINER,
			'label'    => esc_html__('Slide Direction', 'bricksfly-elements-for-bricks'),
			'type'     => 'select',
			'options'  => [
				'bottom' => esc_html__('Bottom → Top', 'bricksfly-elements-for-bricks'),
				'top'    => esc_html__('Top → Bottom', 'bricksfly-elements-for-bricks'),
				'left'   => esc_html__('Left → Right', 'bricksfly-elements-for-bricks'),
				'right'  => esc_html__('Right → Left', 'bricksfly-elements-for-bricks'),
			],
			'default'  => 'bottom',
			'inline'   => true,
			'required' => ['_bricksfly_starter_anim_container', '=', 'slide'],
		];

		$controls['_bricksfly_slide_distance_container'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID_CONTAINER,
			'label'    => esc_html__('Distance (px)', 'bricksfly-elements-for-bricks'),
			'type'     => 'number',
			'default'  => 40,
			'required' => ['_bricksfly_starter_anim_container', '=', 'slide'],
			'css'      => [
				[
					'property' => '--bricksfly-slide-distance',
					'selector' => '',
					'value'    => '%spx',
				],
			],
		];

		$controls['_bricksfly_slide_duration_container'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID_CONTAINER,
			'label'    => esc_html__('Duration (ms)', 'bricksfly-elements-for-bricks'),
			'type'     => 'number',
			'default'  => 600,
			'required' => ['_bricksfly_starter_anim_container', '=', 'slide'],
			'css'      => [
				[
					'property' => '--bricksfly-slide-duration',
					'selector' => '',
					'value'    => '%sms',
				],
			],
		];

		$controls['_bricksfly_slide_delay_container'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID_CONTAINER,
			'label'    => esc_html__('Delay (ms)', 'bricksfly-elements-for-bricks'),
			'type'     => 'number',
			'default'  => 0,
			'required' => ['_bricksfly_starter_anim_container', '=', 'slide'],
			'css'      => [
				[
					'property' => '--bricksfly-slide-delay',
					'selector' => '',
					'value'    => '%sms',
				],
			],
		];

		$controls['_bricksfly_slide_ease_container'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID_CONTAINER,
			'label'    => esc_html__('Easing', 'bricksfly-elements-for-bricks'),
			'type'     => 'select',
			'options'  => $this->get_easing_options(),
			'default'  => 'ease',
			'inline'   => true,
			'required' => ['_bricksfly_starter_anim_container', '=', 'slide'],
			'css'      => [
				[
					'property' => '--bricksfly-slide-ease',
					'selector' => '',
				],
			],
		];

		// ---- container flip ----
		$controls['_bricksfly_flip_axis_container'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID_CONTAINER,
			'label'    => esc_html__('Flip Direction', 'bricksfly-elements-for-bricks'),
			'type'     => 'select',
			'options'  => [
				'x' => esc_html__('Flip X', 'bricksfly-elements-for-bricks'),
				'y' => esc_html__('Flip Y', 'bricksfly-elements-for-bricks'),
			],
			'default'  => 'x',
			'inline'   => true,
			'required' => ['_bricksfly_starter_anim_container', '=', 'flip'],
		];

		$controls['_bricksfly_flip_angle_container'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID_CONTAINER,
			'label'    => esc_html__('Flip Angle (deg)', 'bricksfly-elements-for-bricks'),
			'type'     => 'number',
			'default'  => 90,
			'required' => ['_bricksfly_starter_anim_container', '=', 'flip'],
			'css'      => [
				[
					'property' => '--bricksfly-flip-angle-container',
					'selector' => '',
					'value'    => '%sdeg',
				],
			],
		];

		$controls['_bricksfly_flip_perspective_container'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID_CONTAINER,
			'label'    => esc_html__('Perspective (px)', 'bricksfly-elements-for-bricks'),
			'type'     => 'number',
			'default'  => 800,
			'required' => ['_bricksfly_starter_anim_container', '=', 'flip'],
			'css'      => [
				[
					'property' => '--bricksfly-flip-perspective-container',
					'selector' => '',
					'value'    => '%spx',
				],
			],
		];

		// Repeat behavior on containers as well.
		$controls['_bricksfly_repeat_on_enter_container'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID_CONTAINER,
			'label'    => esc_html__('Repeat Animation?', 'bricksfly-elements-for-bricks'),
			'type'     => 'select',
			'options'  => [
				'no'  => esc_html__('Play Once', 'bricksfly-elements-for-bricks'),
				'yes' => esc_html__('Every Time', 'bricksfly-elements-for-bricks'),
			],
			'default'  => 'no',
			'inline'   => true,
			'required' => ['_bricksfly_starter_anim_container', '!=', ['', 'none']],
		];

		$controls['_bricksfly_anim_editor_enabled_container'] = [
			'tab'         => 'content',
			'group'       => self::GROUP_ID_CONTAINER,
			'label'       => esc_html__('Enable On Editor', 'bricksfly-elements-for-bricks'),
			'description' => esc_html__('For better performance in editor mode, keep this off.', 'bricksfly-elements-for-bricks'),
			'type'        => 'checkbox',
			'inline'      => true,
			'required'    => ['_bricksfly_starter_anim_container', '!=', ['', 'none']],
			'separator'   => 'before',
		];

		$controls['_bricksfly_play_starter_animation_container'] = [
			'tab'      => 'content',
			'group'    => self::GROUP_ID_CONTAINER,
			'type'     => 'info',
			'content'  => '<button type="button" class="bricksfly-free-play-animation"><span class="bricksfly-free-play-animation__icon" aria-hidden="true">▶</span><span class="bricksfly-free-play-animation__label">PLAY ANIMATION</span></button>',
			'required' => [
				['_bricksfly_starter_anim_container', '!=', ['', 'none']],
				['_bricksfly_anim_editor_enabled_container', '=', true],
			],
		];

		return $controls;
	}

	/* =====================================================================
	 * Render-time class injection (Elementor's `prefix_class` equivalent).
	 *
	 * Bricks does not have a per-control `prefix_class` mechanism. We
	 * intercept the root element's render attributes and append the
	 * animation classes manually based on the saved settings.
	 * ================================================================== */

public function apply_render_classes($attributes, $key, $element)
{
	if (! is_object($element) || empty($element->name)) {
		return $attributes;
	}

	$name         = $element->name;
	$is_image     = in_array($name, self::MEDIA_WIDGETS, true);
	$is_text      = in_array($name, self::TEXT_WIDGETS, true) || $is_image;
	$is_container = in_array($name, self::CONTAINER_WIDGETS, true);

	if (! $is_text && ! $is_container) {
		return $attributes;
	}

	// heading / text / container render their outer tag from the `_root`
	// attribute set, so we only act on that key. The core *image* element does
	// NOT reliably expose `_root` to this filter — gating strictly on `_root`
	// means image classes are never injected (the bug). For image we instead
	// act on the FIRST key Bricks renders (its outer wrapper) and write the
	// classes onto that same set, once per element.
	if (! $is_image && $key !== '_root') {
		return $attributes;
	}

	if ($is_image) {
		static $img_done = [];
		$eid = isset($element->id) ? $element->id : spl_object_id($element);
		if (isset($img_done[$eid])) {
			return $attributes;
		}
		$img_done[$eid] = true;
	}

	// The attribute set we write onto: `_root` for text/container, the current
	// (outer) key for image.
	$root_key = $is_image ? $key : '_root';

	$settings = isset($element->settings) && is_array($element->settings) ? $element->settings : [];

	if (! isset($attributes[$root_key]) || ! is_array($attributes[$root_key])) {
		$attributes[$root_key] = [];
	}
	if (! isset($attributes[$root_key]['class'])) {
		$attributes[$root_key]['class'] = [];
	} elseif (! is_array($attributes[$root_key]['class'])) {
		$attributes[$root_key]['class'] = [$attributes[$root_key]['class']];
	}

	$add = function ($class) use (&$attributes, $root_key) {
		$class = sanitize_html_class($class);
		if ($class !== '') {
			$attributes[$root_key]['class'][] = $class;
		}
	};

	if ($is_text) {
		$anim = isset($settings['_bricksfly_starter_anim']) ? $settings['_bricksfly_starter_anim'] : '';
		if ($anim && $anim !== 'none') {
			$add('bricksfly-starter-animations-' . $anim);
			$add('bricksfly-target-self');

			$repeat = ($settings['_bricksfly_repeat_on_enter'] ?? '') === 'yes' ? 'yes' : 'no';
			$add('bricksfly-repeat-' . $repeat);


			// text-bg-clip needs the image URL piped into a CSS variable. (Image
			// elements never use this preset, so this only runs for text widgets.)
			if ($anim === 'text-bg-clip' && ! empty($settings['_bricksfly_bg_text_image'])) {
				$img    = $settings['_bricksfly_bg_text_image'];
				$bg_url = '';
				if (is_array($img)) {
					if (! empty($img['url'])) {
						$bg_url = $img['url'];
					} elseif (! empty($img['id'])) {
						$src    = wp_get_attachment_image_src((int) $img['id'], 'large');
						$bg_url = $src && ! empty($src[0]) ? $src[0] : '';
					}
				} elseif (is_string($img)) {
					$bg_url = $img;
				}

				if ($bg_url !== '') {
					$style_decl = '--bricksfly-bg-text-image:url(' . esc_url_raw($bg_url) . ');';
					$existing   = isset($attributes[$root_key]['style']) ? $attributes[$root_key]['style'] : '';
					if (is_array($existing)) {
						$existing[]                      = $style_decl;
						$attributes[$root_key]['style']  = $existing;
					} else {
						$attributes[$root_key]['style']  = trim((string) $existing . ' ' . $style_decl);
					}
				}
			}

			if ($anim === 'reveal') {
				$direction = ! empty($settings['_bricksfly_reveal_direction']) ? $settings['_bricksfly_reveal_direction'] : 'bottom';
				$add('bricksfly-reveal-' . $direction);
				if (! empty($settings['_bricksfly_reveal_fade'])) {
					$add('bricksfly-reveal-yes');
				}
			}

			if ($anim === 'text-char-animate') {
				$preset = ! empty($settings['_bricksfly_char_preset']) ? $settings['_bricksfly_char_preset'] : 'revolve';
				$add('bricksfly-char-preset-' . $preset);
			}

			if ($anim === 'slide') {
				$direction = ! empty($settings['_bricksfly_slide_direction']) ? $settings['_bricksfly_slide_direction'] : 'bottom';
				$add('bricksfly-slide-' . $direction);
			}

			if ($anim === 'flip') {
				$axis = ! empty($settings['_bricksfly_flip_axis']) ? $settings['_bricksfly_flip_axis'] : 'x';
				$add('bricksfly-flip-axis-' . $axis);
			}
		}

	}

	if ($is_container) {
		$anim = isset($settings['_bricksfly_starter_anim_container']) ? $settings['_bricksfly_starter_anim_container'] : '';
		if ($anim && $anim !== 'none') {
			$add('bricksfly-starter-animations-' . $anim);

			if ($anim === 'slide') {
				$direction = ! empty($settings['_bricksfly_slide_direction_container']) ? $settings['_bricksfly_slide_direction_container'] : 'bottom';
				$add('bricksfly-slide-' . $direction);
			}

			if ($anim === 'flip') {
				$axis = ! empty($settings['_bricksfly_flip_axis_container']) ? $settings['_bricksfly_flip_axis_container'] : 'x';
				$add('bricksfly-flip-axis-container-' . $axis);
			}

			// reads `bricksfly-repeat-yes` — so never emit the no-op `bricksfly-repeat-no`.
			$repeat = ($settings['_bricksfly_repeat_on_enter_container'] ?? '') === 'yes' ? 'yes' : 'no';
			$add('bricksfly-repeat-' . $repeat);
		}
	
	}

	return $attributes;
}

	/* =====================================================================
	 * Frontend assets
	 * ================================================================== */

	public function enqueue_assets()
	{
		$css_path = BRICKSFLY_PATH . 'public/build/extensions/starter-animations-client.css';
		$css_ver  = file_exists($css_path) ? filemtime($css_path) : BRICKSFLY_VERSION;

		// Animation CSS is needed in every context.
		wp_enqueue_style(
			'bricksfly-starter-animations-client',
			BRICKSFLY_URL . 'public/build/extensions/starter-animations-client.css',
			[],
			$css_ver
		);

		$in_builder_iframe = function_exists('bricks_is_builder_iframe') && bricks_is_builder_iframe();

		if ($in_builder_iframe) {
			// Builder iframe: lightweight preview bundle — handles Play-button
			// messages, no IntersectionObserver.
			$js_path = BRICKSFLY_PATH . 'public/build/extensions/starter-animations-builder.js';
			wp_enqueue_script(
				'bricksfly-starter-animations-preview',
				BRICKSFLY_URL . 'public/build/extensions/starter-animations-builder.js',
				[],
				file_exists($js_path) ? filemtime($js_path) : BRICKSFLY_VERSION,
				true
			);
		} else {
			// Frontend (and builder parent frame as a no-op): full scroll-driven
			// animation bundle with IntersectionObserver.
			$js_path = BRICKSFLY_PATH . 'public/build/extensions/starter-animations-client.js';
			wp_enqueue_script(
				'bricksfly-starter-animations-client',
				BRICKSFLY_URL . 'public/build/extensions/starter-animations-client.js',
				[],
				file_exists($js_path) ? filemtime($js_path) : BRICKSFLY_VERSION,
				true
			);
		}
	}
}

BRICKSFLY_Starter_Animations::instance();
