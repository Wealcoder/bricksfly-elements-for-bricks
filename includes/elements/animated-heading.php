<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class AAB_Bricks_Animated_Heading extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aab-animated-heading';
	public $icon         = 'ti-text aab-element-marker';
	public $css_selector = '.aab-animated-heading';
	public $scripts      = [ 'aabAnimatedHeading' ];

	public function get_label() {
		return esc_html__('Animated Heading', 'bricksfly');
	}

	public function get_keywords() {
		return [ 'animated', 'heading', 'text', 'title', 'animation' ];
	}

	public function enqueue_scripts() {
		wp_enqueue_style(
			'aab-animated-heading',
			AAB_ADDONS_URL . 'public/build/elements/animated-heading.css',
			[],
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-animated-heading',
			AAB_ADDONS_URL . 'public/build/elements/animated-heading.js',
			[],
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['content'] = [
			'title' => esc_html__('Content', 'bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['animation'] = [
			'title' => esc_html__('Animation', 'bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['heading_style'] = [
			'title' => esc_html__('Heading', 'bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['text_effects'] = [
			'title' => esc_html__('Text Effects', 'bricksfly'),
			'tab'   => 'style',
		];
	}

	public function set_controls() {

		/* =========================
		   CONTENT
		========================= */

		$this->controls['heading_text'] = [
			'group'   => 'content',
			'label'   => esc_html__( 'Heading Text', 'bricksfly' ),
			'type'    => 'text',
			'default' => esc_html__( 'Animated Heading', 'bricksfly' ),
		];

		$this->controls['heading_tag'] = [
			'group'   => 'content',
			'label'   => esc_html__( 'HTML Tag', 'bricksfly' ),
			'type'    => 'select',
			'options' => [
				'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3',
				'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6',
			],
			'default' => 'h2',
			'inline'  => true,
		];

		$this->controls['heading_link'] = [
			'group' => 'content',
			'label' => esc_html__( 'Link', 'bricksfly' ),
			'type'  => 'link',
		];

		$this->controls['text_align'] = [
			'group' => 'content',
			'label' => esc_html__( 'Text Align', 'bricksfly' ),
			'type'  => 'text-align',
			'css'   => [[
				'property' => 'text-align',
				'selector' => '.aab-animated-heading',
			]],
		];

		/* =========================
		   ANIMATION
		========================= */

		$this->controls['animation_type'] = [
			'group'   => 'animation',
			'label'   => esc_html__( 'Animation Type', 'bricksfly' ),
			'type'    => 'select',
			'options' => [
				'none'             => esc_html__( 'None', 'bricksfly' ),
				'reveal'           => esc_html__( 'Reveal', 'bricksfly' ),
				'scale'            => esc_html__( 'Scale', 'bricksfly' ),
				'slide'            => esc_html__( 'Slide', 'bricksfly' ),
				'skew_reveal'      => esc_html__( 'Skew Reveal', 'bricksfly' ),
				'glow_pulse'       => esc_html__( 'Glow Pulse', 'bricksfly' ),
				'typewriter'       => esc_html__( 'Typewriter', 'bricksfly' ),
				'mask_wipe'        => esc_html__( 'Mask Wipe', 'bricksfly' ),
				'water_wave'       => esc_html__( 'Water Wave', 'bricksfly' ),
				'background_clip'  => esc_html__( 'Background Clip Text', 'bricksfly' ),
				'character'        => esc_html__( 'Character Animation', 'bricksfly' ),
			],
			'default' => 'reveal',
		];

		$this->controls['animation_trigger'] = [
			'group'   => 'animation',
			'label'   => esc_html__( 'Trigger', 'bricksfly' ),
			'type'    => 'select',
			'options' => [
				'on_scroll'        => esc_html__( 'On Scroll', 'bricksfly' ),
				'on_page_load'     => esc_html__( 'On Page Load', 'bricksfly' ),
				'play_with_scroll' => esc_html__( 'Play With Scroll', 'bricksfly' ),
				'mouseover'        => esc_html__( 'Hover', 'bricksfly' ),
				'click'            => esc_html__( 'Click', 'bricksfly' ),
			],
			'default'  => 'on_scroll',
			'required' => [['animation_type', '!=', 'none']],
		];

		$this->controls['trigger_selector'] = [
			'group'       => 'animation',
			'label'       => esc_html__( 'Trigger Selector', 'bricksfly' ),
			'type'        => 'text',
			'placeholder' => '.my-class',
			'required'    => [
				['animation_trigger', '=', ['mouseover', 'click']],
				['animation_type', '!=', 'none'],
			],
		];

		$this->controls['animation_duration'] = [
			'group'    => 'animation',
			'label'    => esc_html__( 'Duration (s)', 'bricksfly' ),
			'type'     => 'number',
			'default'  => 1,
			'step'     => 0.1,
			'required' => [['animation_type', '!=', 'none']],
		];

		$this->controls['animation_delay'] = [
			'group'    => 'animation',
			'label'    => esc_html__( 'Delay (s)', 'bricksfly' ),
			'type'     => 'number',
			'default'  => 0,
			'step'     => 0.1,
			'required' => [['animation_type', '!=', 'none']],
		];

		$this->controls['animation_stagger'] = [
			'group'    => 'animation',
			'label'    => esc_html__( 'Stagger (s)', 'bricksfly' ),
			'type'     => 'number',
			'default'  => 0.02,
			'step'     => 0.01,
			'required' => [['animation_type', '=', ['character', 'typewriter', 'water_wave']]],
		];

		/* =========================
		   HEADING STYLE
		========================= */

		$this->controls['heading_typo'] = [
			'group' => 'heading_style',
			'label' => esc_html__( 'Typography', 'bricksfly' ),
			'type'  => 'typography',
			'css'   => [[
				'property' => 'typography',
				'selector' => '.aab-animated-heading',
			]],
		];

		$this->controls['heading_color'] = [
			'group' => 'heading_style',
			'label' => esc_html__( 'Color', 'bricksfly' ),
			'type'  => 'color',
			'css'   => [[
				'property' => 'color',
				'selector' => '.aab-animated-heading',
			]],
		];

		$this->controls['heading_margin'] = [
			'group' => 'heading_style',
			'label' => esc_html__( 'Margin', 'bricksfly' ),
			'type'  => 'dimensions',
			'css'   => [[
				'property' => 'margin',
				'selector' => '.aab-animated-heading',
			]],
		];

		$this->controls['heading_padding'] = [
			'group' => 'heading_style',
			'label' => esc_html__( 'Padding', 'bricksfly' ),
			'type'  => 'dimensions',
			'css'   => [[
				'property' => 'padding',
				'selector' => '.aab-animated-heading',
			]],
		];

		/* =========================
		   TEXT EFFECTS
		========================= */

		$this->controls['text_shadow'] = [
			'group' => 'text_effects',
			'label' => esc_html__( 'Text Shadow', 'bricksfly' ),
			'type'  => 'box-shadow',
			'css'   => [[
				'property' => 'text-shadow',
				'selector' => '.aab-animated-heading',
			]],
		];

		$this->controls['bg_clip_gradient'] = [
			'group'    => 'text_effects',
			'label'    => esc_html__( 'Gradient Background', 'bricksfly' ),
			'type'     => 'background',
			'required' => [['animation_type', '=', 'background_clip']],
			'css'      => [[
				'property' => 'background',
				'selector' => '.aab-animated-heading',
			]],
		];

		$this->controls['stroke_color'] = [
			'group' => 'text_effects',
			'label' => esc_html__( 'Text Stroke Color', 'bricksfly' ),
			'type'  => 'color',
			'css'   => [[
				'property' => '-webkit-text-stroke-color',
				'selector' => '.aab-animated-heading',
			]],
		];

		$this->controls['stroke_width'] = [
			'group' => 'text_effects',
			'label' => esc_html__( 'Text Stroke Width', 'bricksfly' ),
			'type'  => 'number',
			'units' => true,
			'css'   => [[
				'property' => '-webkit-text-stroke-width',
				'selector' => '.aab-animated-heading',
			]],
		];
	}

	public function render() {
		$s = $this->settings;

		$text    = $s['heading_text'] ?? esc_html__( 'Animated Heading', 'bricksfly' );
		$tag     = $s['heading_tag'] ?? 'h2';
		$tag     = in_array( $tag, ['h1','h2','h3','h4','h5','h6'], true ) ? $tag : 'h2';

		$anim_type    = $s['animation_type'] ?? 'reveal';
		$anim_trigger = $s['animation_trigger'] ?? 'on_scroll';
		$duration     = $s['animation_duration'] ?? 1;
		$delay        = $s['animation_delay'] ?? 0;
		$stagger      = $s['animation_stagger'] ?? 0.02;
		$trigger_sel  = $s['trigger_selector'] ?? '';

		// Build animation data as JSON
		$anim_data = [
			'type'     => $anim_type,
			'trigger'  => $anim_trigger,
			'duration' => floatval( $duration ),
			'delay'    => floatval( $delay ),
			'stagger'  => floatval( $stagger ),
		];

		if ( ! empty( $trigger_sel ) ) {
			$anim_data['triggerSelector'] = $trigger_sel;
		}

		// Set attributes on the heading element
		$this->set_attribute( 'heading', 'class', 'aab-animated-heading' );
		$this->set_attribute( 'heading', 'data-aab-anim', esc_attr( wp_json_encode( $anim_data ) ) );

		// Link handling
		$content = esc_html( $text );

		if ( ! empty( $s['heading_link']['url'] ) ) {
			$link_open  = '<a href="' . esc_url( $s['heading_link']['url'] ) . '"';
			if ( ! empty( $s['heading_link']['newTab'] ) ) {
				$link_open .= ' target="_blank" rel="noopener noreferrer"';
			}
			$link_open .= '>';
			$content = $link_open . $content . '</a>';
		}

		echo '<div ' . $this->render_attributes( '_root' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<' . tag_escape( $tag ) . ' ' . $this->render_attributes( 'heading' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</' . tag_escape( $tag ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
