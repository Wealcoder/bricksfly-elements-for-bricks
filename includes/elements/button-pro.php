<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BRICKSFLY_Bricks_Button_Pro extends \Bricks\Element {


	public $category     = 'bricks fly';
	public $name         = 'aab-button-pro';
	public $icon         = 'ti-mouse-alt aab-element-marker';
	public $css_selector = '.aae--btn-pro-wrapper';
	public $scripts      = array( 'bricksflyButtonPro' );

	public function get_label() {
		return esc_html__( 'Advanced Button Pro', 'bricksfly-elements-for-bricks' );
	}

	public function get_keywords() {
		return array( 'button', 'pro', 'advanced', 'hover', 'cta' );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'bricks-font-awesome-6' );
		wp_enqueue_style( 'bricks-font-awesome-6-brands' );

		wp_enqueue_style(
			'aab-button-pro',
			BRICKSFLY_URL . 'public/build/elements/button-pro.css',
			array(),
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-button-pro',
			BRICKSFLY_URL . 'public/build/elements/button-pro.js',
			array(),
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['button'] = array(
			'title' => esc_html__( 'Button', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['button_style'] = array(
			'title' => esc_html__( 'Button Style', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);
	}

	public function set_controls() {

		// Content
		$this->controls['btnStyle'] = array(
			'tab'     => 'content',
			'group'   => 'button',
			'label'   => esc_html__( 'Style', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'base-default'   => esc_html__( 'Default', 'bricksfly-elements-for-bricks' ),
				'base-square'    => esc_html__( 'Square', 'bricksfly-elements-for-bricks' ),
				'base-underline' => esc_html__( 'Underline', 'bricksfly-elements-for-bricks' ),
				'base-mask'      => esc_html__( 'Mask', 'bricksfly-elements-for-bricks' ),
				'base-oval'      => esc_html__( 'Oval', 'bricksfly-elements-for-bricks' ),
				'base-circle'    => esc_html__( 'Circle', 'bricksfly-elements-for-bricks' ),
				'base-ellipse'   => esc_html__( 'Ellipse', 'bricksfly-elements-for-bricks' ),
				'pro-1'          => esc_html__( 'Border Divide', 'bricksfly-elements-for-bricks' ),
				'pro-2'          => esc_html__( 'Shadow Offset', 'bricksfly-elements-for-bricks' ),
				'pro-3'          => esc_html__( 'Text Flip', 'bricksfly-elements-for-bricks' ),
				'pro-4'          => esc_html__( 'Radial Reveal', 'bricksfly-elements-for-bricks' ),
				'pro-5'          => esc_html__( 'Icon Swap L→R', 'bricksfly-elements-for-bricks' ),
				'pro-6'          => esc_html__( 'Icon Swap R→L', 'bricksfly-elements-for-bricks' ),
				'pro-7'          => esc_html__( 'Outline Pill', 'bricksfly-elements-for-bricks' ),
				'pro-8'          => esc_html__( 'Slide Reveal', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'base-default',
		);

		// --- Square size (responsive) ---
		// The Square style ships with a fixed 215×215 box in CSS, so Padding alone
		// can't resize it. These Width/Height controls override that box and, because
		// Bricks generates breakpoint-scoped rules for `css` controls, they work
		// responsively (set a value per device via the control's breakpoint switcher).
		$this->controls['btnSquareWidth'] = array(
			'tab'         => 'content',
			'group'       => 'button',
			'label'       => esc_html__( 'Square Width', 'bricksfly-elements-for-bricks' ),
			'type'        => 'number',
			'units'       => true,
			'breakpoints' => true,
			'required'    => array( 'btnStyle', '=', 'base-square' ),
			'css'         => array(
				// Override both width and the min-width default, so any value (even
				// smaller than the 215px default) takes effect.
				array(
					'property' => 'width',
					'selector' => '.wcf-btn-square',
				),
				array(
					'property' => 'min-width',
					'selector' => '.wcf-btn-square',
				),
			),
		);

		$this->controls['btnSquareHeight'] = array(
			'tab'         => 'content',
			'group'       => 'button',
			'label'       => esc_html__( 'Square Height', 'bricksfly-elements-for-bricks' ),
			'type'        => 'number',
			'units'       => true,
			'breakpoints' => true,
			'required'    => array( 'btnStyle', '=', 'base-square' ),
			'css'         => array(
				array(
					'property' => 'height',
					'selector' => '.wcf-btn-square',
				),
				array(
					'property' => 'min-height',
					'selector' => '.wcf-btn-square',
				),
			),
		);

		$this->controls['btnCircleSize'] = array(
			'tab'         => 'content',
			'group'       => 'button',
			'label'       => esc_html__( 'Circle Size', 'bricksfly-elements-for-bricks' ),
			'type'        => 'number',
			'units'       => true,
			'default'     => '130px',
			'breakpoints' => true,
			'required'    => array( 'btnStyle', '=', 'base-circle' ),
			'css'         => array(
				// Override both width and the min-width default, so any value (even
				// smaller than the 215px default) takes effect.
				array(
					'property' => 'width',
					'selector' => '.wcf-btn-circle',
				),
				array(
					'property' => 'height',
					'selector' => '.wcf-btn-circle',
				),
			),
		);

		$this->controls['btnHoverVariant'] = array(
			'tab'      => 'content',
			'group'    => 'button',
			'label'    => esc_html__( 'Hover Style', 'bricksfly-elements-for-bricks' ),
			'type'     => 'select',
			'options'  => array(
				'hover-none'      => esc_html__( 'None', 'bricksfly-elements-for-bricks' ),
				'hover-divide'    => esc_html__( 'Divided', 'bricksfly-elements-for-bricks' ),
				'hover-cross'     => esc_html__( 'Cross', 'bricksfly-elements-for-bricks' ),
				'hover-cropping'  => esc_html__( 'Cropping', 'bricksfly-elements-for-bricks' ),
				'rollover-top'    => esc_html__( 'Rollover Top', 'bricksfly-elements-for-bricks' ),
				'rollover-left'   => esc_html__( 'Rollover Left', 'bricksfly-elements-for-bricks' ),
				'parallal-border' => esc_html__( 'Parallel Border', 'bricksfly-elements-for-bricks' ),
				'rollover-cross'  => esc_html__( 'Rollover Cross', 'bricksfly-elements-for-bricks' ),
			),
			'default'  => 'hover-none',
			'required' => array( 'btnStyle', '=', array( 'base-default', 'base-square' ) ),
		);

		$this->controls['btnText'] = array(
			'tab'     => 'content',
			'group'   => 'button',
			'label'   => esc_html__( 'Text', 'bricksfly-elements-for-bricks' ),
			'type'    => 'text',
			'default' => esc_html__( 'Discover More', 'bricksfly-elements-for-bricks' ),
		);

		$this->controls['btnIcon'] = array(
			'tab'      => 'content',
			'group'    => 'button',
			'label'    => esc_html__( 'Icon', 'bricksfly-elements-for-bricks' ),
			'type'     => 'icon',
			'default'  => array(
				'icon'    => 'fas fa-arrow-right',
				'library' => 'fontawesomeSolid',
			),
			'required' => array( 'btnStyle', '!=', 'pro-4' ),
		);

		$this->controls['btnIconPosition'] = array(
			'tab'      => 'content',
			'group'    => 'button',
			'label'    => esc_html__( 'Icon Position', 'bricksfly-elements-for-bricks' ),
			'type'     => 'select',
			'inline'   => true,
			'options'  => array(
				'row'         => esc_html__( 'After', 'bricksfly-elements-for-bricks' ),
				'row-reverse' => esc_html__( 'Before', 'bricksfly-elements-for-bricks' ),
			),
			'default'  => 'row',
			'required' => array( 'btnStyle', '!=', array( 'pro-5', 'pro-6' ) ),
			'css'      => array(
				array(
					'property' => 'flex-direction',
					'selector' => '.aae--btn-pro, .wcf__btn a',
				),
			),
		);

		$this->controls['btnLink'] = array(
			'tab'   => 'content',
			'group' => 'button',
			'label' => esc_html__( 'Link', 'bricksfly-elements-for-bricks' ),
			'type'  => 'link',
		);

		$this->controls['btnOutlineGap'] = array(
			'tab'      => 'content',
			'group'    => 'button',
			'label'    => esc_html__( 'Outline Gap', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'units'    => true,
			'default'  => '10px',
			'required' => array( 'btnStyle', '=', '7' ),
			'css'      => array(
				array(
					'property' => '--outline-gap',
					'selector' => '&.style-7 .aae--btn-pro',
				),
			),
		);

		$this->controls['btnAlign'] = array(
			'tab'     => 'content',
			'group'   => 'button',
			'label'   => esc_html__( 'Alignment', 'bricksfly-elements-for-bricks' ),
			'type'    => 'align-items',
			'inline'  => true,
			'exclude' => array( 'stretch' ),  // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Bricks control option, not a WP_Query arg
			'css'     => array(
				array(
					'property' => 'justify-content',
					'selector' => '',
				),
			),
		);

		// Style
		$this->controls['btnTypo'] = array(
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'typography',
					'selector' => '.aae--btn-pro, .g-btn-text, .wcf__btn a',
				),
			),
		);

		$this->controls['btnBg'] = array(
			'tab'      => 'style',
			'group'    => 'button_style',
			'label'    => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'     => 'background',
			'required' => array( 'btnStyle', '!=', array( 'pro-7', 'base-underline' ) ),
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.aae--btn-pro, .g-btn-text, .g-btn-icon, .wcf__btn a',
				),
			),
		);

		$this->controls['btnMaskBg'] = array(
			'tab'      => 'style',
			'group'    => 'button_style',
			'label'    => esc_html__( 'Mask Background', 'bricksfly-elements-for-bricks' ),
			'type'     => 'background',
			'required' => array( 'btnStyle', '=', 'base-mask' ),
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.wcf-btn-mask::after',
				),
			),
		);

		$this->controls['btnBg2'] = array(
			'tab'      => 'style',
			'group'    => 'button_style',
			'label'    => esc_html__( 'Inner Background', 'bricksfly-elements-for-bricks' ),
			'type'     => 'background',
			'required' => array( 'btnStyle', '=', array( 'pro-7', 'pro-8' ) ),
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.aae--btn-pro::after',
				),
			),
		);

		$this->controls['btnBorder'] = array(
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => '.aae--btn-pro, .g-btn-text, .g-btn-icon, .wcf__btn a',
				),
			),
		);

		$this->controls['btnBorderHeight'] = array(
			'tab'      => 'style',
			'group'    => 'button_style',
			'label'    => esc_html__( 'Divider Width', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'units'    => true,
			'min'      => 0,
			'required' => array( 'btnStyle', '=', 'pro-1' ),
			'default'  => '1px',
			'css'      => array(
				array(
					'property' => 'border-bottom-width',
					'selector' => '.btn-border-divide .text, .btn-border-divide .icon',
				),
			),
		);

		$this->controls['btnPadding'] = array(
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.aae--btn-pro, .g-btn-text, .wcf__btn a',
				),
			),
		);

		$this->controls['iconHeading'] = array(
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Icon', 'bricksfly-elements-for-bricks' ),
			'type'  => 'separator',
		);

		$this->controls['btnIconSize']       = array(
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Icon Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'font-size',
					'selector' => '.aae--btn-pro .icon, .g-btn-icon, .wcf__btn a i',
				),
				array(
					'property' => 'width',
					'selector' => '&.style-4 .aae--btn-pro strong',
				),
			),
		);
		$this->controls['btnRedialLineSize'] = array(
			'tab'      => 'style',
			'group'    => 'button_style',
			'label'    => esc_html__( 'Radial Line Size', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'default'  => '50px',
			'units'    => true,
			'required' => array( 'btnStyle', '=', 'pro-4' ),
			'css'      => array(
				array(
					'property' => 'width',
					'selector' => '.aae--btn-pro strong',
				),

			),
		);

			$this->controls['btnRedialLineHeight'] = array(
				'tab'      => 'style',
				'group'    => 'button_style',
				'label'    => esc_html__( 'Radial Line Height', 'bricksfly-elements-for-bricks' ),
				'type'     => 'number',
				'default'  => '1px',
				'units'    => true,
				'required' => array( 'btnStyle', '=', 'pro-4' ),
				'css'      => array(
					array(
						'property' => 'height',
						'selector' => '.aae--btn-pro strong',
					),

					array(
						'property' => 'border-right-width',
						'selector' => '.aae--btn-pro strong::after',
					),

					array(
						'property' => 'border-bottom-width',
						'selector' => '.aae--btn-pro strong::after',
					),

				),
			);

			// Icon container size for the icon-swap pill styles (Pro 5 / Pro 6). Sets
			// the .g-btn-icon box (width + height for a square) and the --icon-width
			// variable the swap animation reads for its slide-out margin, so the
			// motion stays in sync with the box size.
			$this->controls['btnIconContainerSize'] = array(
				'tab'      => 'style',
				'group'    => 'button_style',
				'label'    => esc_html__( 'Icon Container Size', 'bricksfly-elements-for-bricks' ),
				'type'     => 'number',
				'units'    => true,
				'default'  => '60px',
				'required' => array( 'btnStyle', '=', array( 'pro-5', 'pro-6' ) ),
				'css'      => array(
					array(
						'property' => 'width',
						'selector' => '.g-btn-icon',
					),
					array(
						'property' => 'height',
						'selector' => '.g-btn-icon',
					),
					array(
						'property' => '--icon-width',
						'selector' => '.g-btn-icon',
					),
				),
			);

			$this->controls['btnGap'] = array(
				'tab'      => 'style',
				'group'    => 'button_style',
				'label'    => esc_html__( 'Gap', 'bricksfly-elements-for-bricks' ),
				'type'     => 'number',
				'units'    => true,
				'required' => array( 'btnStyle', '!=', array( 'pro-5', 'pro-6' ) ),
				'css'      => array(
					array(
						'property' => 'gap',
						'selector' => '.aae--btn-pro, .g-btn-text, .wcf__btn a',
					),
				),
			);

			$this->controls['colorsHeading'] = array(
				'tab'   => 'style',
				'group' => 'button_style',
				'label' => esc_html__( 'Colors', 'bricksfly-elements-for-bricks' ),
				'type'  => 'separator',
			);

			$this->controls['btnColor'] = array(
				'tab'   => 'style',
				'group' => 'button_style',
				'label' => esc_html__( 'Text Color', 'bricksfly-elements-for-bricks' ),
				'type'  => 'color',
				'css'   => array(
					array(
						'property' => 'color',
						'selector' => '.aae--btn-pro, .btn-text-flip span, .g-btn-text, .g-btn-icon, .wcf__btn a',
					),
					array(
						'property' => 'fill',
						'selector' => '.aae--btn-pro, .g-btn-text, .g-btn-icon, .wcf__btn a',
					),
					array(
						'property' => 'background-color',
						'selector' => '&.style-4 .aae--btn-pro strong',
					),
				),
			);

			$this->controls['btnBrColor'] = array(
				'tab'      => 'style',
				'group'    => 'button_style',
				'label'    => esc_html__( 'Divider Color', 'bricksfly-elements-for-bricks' ),
				'type'     => 'color',
				'required' => array( 'btnStyle', '=', 'pro-1' ),
				'css'      => array(
					array(
						'property' => 'border-bottom-color',
						'selector' => '.btn-border-divide .text, .btn-border-divide .icon',
					),
				),
			);

			$this->controls['btnHColor'] = array(
				'tab'   => 'style',
				'group' => 'button_style',
				'label' => esc_html__( 'Hover Text Color', 'bricksfly-elements-for-bricks' ),
				'type'  => 'color',
				'css'   => array(
					array(
						'property' => 'color',
						'selector' => '.aae--btn-pro:hover, .aae--btn-pro:hover .icon, .btn-text-flip:hover span, .aae-btn-pro-group:hover .g-btn-text, .aae-btn-pro-group:hover .g-btn-icon, .wcf__btn a:hover',
					),
					array(
						'property' => 'fill',
						'selector' => '.aae--btn-pro:hover, .aae--btn-pro:hover .icon, .aae-btn-pro-group:hover .g-btn-icon, .wcf__btn a:hover',
					),
					array(
						'property' => 'background-color',
						'selector' => '&.style-4 .aae--btn-pro:hover strong',
					),
				),
			);

			$this->controls['btnHIconColor'] = array(
				'tab'      => 'style',
				'group'    => 'button_style',
				'label'    => esc_html__( 'Hover Icon Color', 'bricksfly-elements-for-bricks' ),
				'type'     => 'color',
				'required' => array( 'btnStyle', '!=', 'pro-4' ),
				'css'      => array(
					// Icon (font icon) color on hover — overrides the inherited text hover color.
					array(
						'property' => 'color',
						'selector' => '.aae--btn-pro:hover .icon, .aae-btn-pro-group:hover .g-btn-icon, .wcf__btn a:hover i',
					),
					// SVG icon fill on hover.
					array(
						'property' => 'fill',
						'selector' => '.aae--btn-pro:hover .icon, .aae-btn-pro-group:hover .g-btn-icon, .wcf__btn a:hover svg',
					),
				),
			);

			$this->controls['btnRadialIconColor'] = array(
				'tab'      => 'style',
				'group'    => 'button_style',
				'label'    => esc_html__( ' Radial Line/icon Color', 'bricksfly-elements-for-bricks' ),
				'type'     => 'color',
				'required' => array( 'btnStyle', '=', 'pro-4' ),
				'css'      => array(
					// Icon (font icon) color on hover — overrides the inherited text hover color.
					array(
						'property' => 'background-color',
						'selector' => '.aae--btn-pro strong',
					),

					array(
						'property' => 'border-color',
						'selector' => '.aae--btn-pro strong::after',
					),

				),
			);

			$this->controls['btnHBorder'] = array(
				'tab'   => 'style',
				'group' => 'button_style',
				'label' => esc_html__( 'Hover Border Color', 'bricksfly-elements-for-bricks' ),
				'type'  => 'color',
				'css'   => array(
					array(
						'property' => 'border-color',
						'selector' => '.aae--btn-pro:hover, .g-btn-text:hover, .btn-border-divide:hover .text, .btn-border-divide:hover .icon, .wcf__btn a:hover, .g-btn-icon:hover',
					),
				),
			);

			// $this->controls['btnHBg'] = [
			// 'tab'   => 'style',
			// 'group' => 'button_style',
			// 'label' => esc_html__('Hover Background', 'bricksfly-elements-for-bricks'),
			// 'type'  => 'background',
			// 'css'   => [
			// [
			// 'property' => 'background',
			// 'selector' => '.aae--btn-pro:hover, .aae-btn-pro-group:hover .g-btn-text, .aae-btn-pro-group:hover .g-btn-icon, .wcf__btn a:hover',
			// ],
			// ],
			// ];

			$this->controls['btnHBg'] = array(
				'tab'      => 'style',
				'group'    => 'button_style',
				'label'    => esc_html__( 'Hover Background', 'bricksfly-elements-for-bricks' ),
				'type'     => 'background',
				'css'      => array(
					array(
						'property' => 'background',
						'selector' => '.aae--btn-pro:hover, .aae-btn-pro-group:hover .g-btn-text, .aae-btn-pro-group:hover .g-btn-icon, .wcf__btn a:hover',
					),
				),

				'required' => array(
					array( 'btnStyle', '=', array( 'base-default', 'base-square', 'base-ellipse', 'pro-3', 'pro-5', 'pro-6' ) ),
					array( 'btnHoverVariant', '=', array( 'hover-none' ) ),
				),

			);

			$this->controls['btnRevealColor'] = array(
				'tab'      => 'style',
				'group'    => 'button_style',
				'label'    => esc_html__( 'Reveal Color', 'bricksfly-elements-for-bricks' ),
				'type'     => 'color',
				// 'required' => ['btnStyle', '=', ['4', 'base-default']],
				'default'  => array( 'hex' => '#FC5A11' ),
				'css'      => array(
					array(
						'property' => 'background-color',
						'selector' => '&.style-4 .aae--btn-pro span',
					),
					array(
						'property' => '--btn-hover-bg',
						'selector' => '.wcf__btn a',
					),
				),
				'required' => array(
					// ['btnHoverVariant', '!=', ['hover-none']],
					array( 'btnStyle', '=', array( 'base-default', 'base-square', 'base-square', 'base-circle', 'base-oval', 'pro-4' ) ),
				),
			// 'required' => ['btnStyle', '!=', ['base-default', 'base-square', 'base-underline', 'base-mask', 'base-ellipse']],
			);

			$this->controls['btnBoxShadow'] = array(
				'tab'      => 'style',
				'group'    => 'button_style',
				'label'    => esc_html__( 'Hover Shadow', 'bricksfly-elements-for-bricks' ),
				'type'     => 'box-shadow',
				'required' => array( 'btnStyle', '=', 'pro-2' ),
				'css'      => array(
					array(
						'property' => 'box-shadow',
						'selector' => '&.style-2 .aae--btn-pro:hover',
					),
				),
			);

			$this->controls['btnRevealOffset'] = array(
				'tab'      => 'style',
				'group'    => 'button_style',
				'label'    => esc_html__( 'Hover Reveal Offset', 'bricksfly-elements-for-bricks' ),
				'type'     => 'number',
				'units'    => true,
				'required' => array( 'btnStyle', '=', 'pro-7' ),
				'default'  => '4px',
				'css'      => array(
					array(
						'property' => 'top',
						'selector' => '&.style-7 .aae--btn-pro:hover::after',
					),
				),
			);

			$this->controls['btnSlideOffset'] = array(
				'tab'      => 'style',
				'group'    => 'button_style',
				'label'    => esc_html__( 'Hover Slide Offset', 'bricksfly-elements-for-bricks' ),
				'type'     => 'number',
				'units'    => true,
				'required' => array( 'btnStyle', '=', 'pro-8' ),
				'default'  => '-92%',
				'css'      => array(
					array(
						'property' => 'left',
						'selector' => '&.style-8 .aae--btn-pro:hover::after',
					),
				),
			);
	}

	private function get_allowed_text_html(): array {
		$attrs = array(
			'class' => true,
			'style' => true,
			'id'    => true,
		);

		return array(
			'span'   => $attrs,
			'strong' => $attrs,
			'b'      => $attrs,
			'em'     => $attrs,
			'i'      => $attrs,
			'br'     => array(),
			'h1'     => $attrs,
			'h2'     => $attrs,
			'h3'     => $attrs,
			'h4'     => $attrs,
			'h5'     => $attrs,
			'h6'     => $attrs,
		);
	}

	public function render() {
		$settings = $this->settings;
		$style    = ! empty( $settings['btnStyle'] ) ? $settings['btnStyle'] : 'base-default';

		// The Style control now uses non-numeric keys (`pro-1` … `pro-8`) so PHP
		// can't recast them to integers and reorder the dropdown. Internally the
		// renderer and CSS still key off the bare number (`1` … `8`) and the
		// `style-N` class, so strip the `pro-` prefix here to keep everything
		// downstream — the switch cases and the `style-N` class — untouched.
		if ( 0 === strpos( $style, 'pro-' ) ) {
			$style = substr( $style, 4 );
		}

		$text = isset( $settings['btnText'] ) ? (string) $settings['btnText'] : '';
		$icon = $settings['btnIcon'] ?? array();
		if ( ! is_array( $icon ) ) {
			$icon = array();
		}

		// Sanitized HTML version for visible content; plain version for attributes.
		$allowed_html = $this->get_allowed_text_html();
		$text_html    = wp_kses( $text, $allowed_html );
		$text_plain   = wp_strip_all_tags( $text );

		$this->set_attribute( '_root', 'class', array( 'aae--btn-pro-wrapper', 'style-' . $style ) );

		$link_key = 'btn-link';

		if ( ! empty( $settings['btnLink'] ) ) {
			$this->set_link_attributes( $link_key, $settings['btnLink'] );
		} else {
			$this->set_attribute( $link_key, 'href', '#' );
		}

		// render_icon() (Bricks core) can return null from several of its own
		// early-return branches (e.g. an SVG icon whose attached file no longer
		// exists) rather than an empty string — guard here so every wp_kses_post()
		// call below always receives a string, never null.
		$icon_html = $icon ? (string) self::render_icon( $icon, array( 'aria-hidden' => 'true' ) ) : '';

		// Base (WCF) styles — render via .wcf__btn > a.wcf-btn-{slug}
		if ( 0 === strpos( $style, 'base-' ) ) {
			$slug = substr( $style, 5 );

			$icon_position_after = isset( $settings['btnIconPosition'] ) && 'row-reverse' === $settings['btnIconPosition'];
			$ext_wrap            = in_array( $slug, array( 'oval', 'circle', 'ellipse' ), true );
			$bg_change           = in_array( $slug, array( 'oval', 'circle' ), true );
			$hover_variant       = isset( $settings['btnHoverVariant'] ) ? $settings['btnHoverVariant'] : 'hover-none';

			$btn_classes = array( 'wcf-btn-' . $slug );

			if ( in_array( $slug, array( 'default', 'square' ), true ) && ! empty( $hover_variant ) && 'hover-none' !== $hover_variant ) {
				$btn_classes[] = 'btn-' . $hover_variant;
			}

			if ( $bg_change ) {
				$btn_classes[] = 'btn-hover-bgchange';
				$btn_classes[] = 'btn-item';
			} elseif ( 'ellipse' === $slug ) {
				$btn_classes[] = 'btn-item';
			}

			$this->set_attribute( $link_key, 'class', $btn_classes );

			if ( 'mask' === $slug ) {
				// Attribute value → must be plain text, no tags.
				$this->set_attribute( $link_key, 'data-text', $text_plain );
			}

			if ( $ext_wrap ) {
				$this->set_attribute( $link_key, 'data-magnetic', 'true' );
			}

			$wrapper_classes = array( 'wcf__btn' );
			if ( $icon_position_after ) {
				$wrapper_classes[] = 'icon-position-after';
			}

			$ext_close = '';
			echo wp_kses_post( '<div ' . $this->render_attributes( '_root' ) . '>' );
			echo '<div class="' . esc_attr( implode( ' ', $wrapper_classes ) ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( $ext_wrap ) {
				echo '<div class="btn-wrapper">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$ext_close = '</div>';
			}

			echo wp_kses_post( '<a ' . $this->render_attributes( $link_key ) . '>' );
			echo wp_kses_post( $text_html );
			echo wp_kses_post( $icon_html );
			if ( $bg_change ) {
				echo '<span></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			echo wp_kses_post( $ext_close );
			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		echo wp_kses_post( '<div ' . $this->render_attributes( '_root' ) . '>' );

		switch ( $style ) {

			case '1':
				$this->set_attribute( $link_key, 'class', array( 'aae--btn-pro', 'btn-border-divide' ) );
				echo wp_kses_post( '<a ' . $this->render_attributes( $link_key ) . '>' );
				echo wp_kses_post( '<span class="text">' . $text_html . '</span>' );
				echo wp_kses_post( '<span class="icon">' . $icon_html . $icon_html . '</span>' );
				echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case '2':
				$this->set_attribute( $link_key, 'class', array( 'aae--btn-pro' ) );
				echo wp_kses_post( '<a ' . $this->render_attributes( $link_key ) . '>' );
				echo wp_kses_post( $text_html );
				echo wp_kses_post( '<span class="icon">' . $icon_html . '</span>' );
				echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case '3':
				$this->set_attribute( $link_key, 'class', array( 'aae--btn-pro', 'btn-text-flip' ) );
				echo wp_kses_post( '<a ' . $this->render_attributes( $link_key ) . '>' );
				// data-text is an attribute → plain text only.
				echo wp_kses_post( '<span data-text="' . esc_attr( $text_plain ) . '">' . $text_html . '</span>' );
				echo wp_kses_post( $icon_html );
				echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case '4':
				$this->set_attribute( $link_key, 'class', array( 'btn-hover', 'aae--btn-pro' ) );
				echo wp_kses_post( '<a ' . $this->render_attributes( $link_key ) . '>' );
				echo '<span></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo wp_kses_post( $text_html );
				echo '<strong></strong>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case '5':
			case '6':
				$this->set_attribute( $link_key, 'class', array( 'aae-btn-pro-group' ) );
				echo wp_kses_post( '<a ' . $this->render_attributes( $link_key ) . '>' );
				echo wp_kses_post( '<span class="g-btn-icon">' . $icon_html . '</span>' );
				echo wp_kses_post( '<span class="g-btn-text">' . $text_html . '</span>' );
				echo wp_kses_post( '<span class="g-btn-icon">' . $icon_html . '</span>' );
				echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case '7':
			case '8':
			default:
				$this->set_attribute( $link_key, 'class', array( 'aae--btn-pro' ) );
				echo wp_kses_post( '<a ' . $this->render_attributes( $link_key ) . '>' );
				echo wp_kses_post( $text_html );
				echo wp_kses_post( '<span class="icon">' . $icon_html . '</span>' );
				echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
		}

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
